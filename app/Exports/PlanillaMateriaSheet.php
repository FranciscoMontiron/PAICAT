<?php

namespace App\Exports;

use App\Models\Comision;
use App\Models\Materia;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class PlanillaMateriaSheet implements FromArray, WithStyles, WithTitle, ShouldAutoSize, WithColumnWidths
{
    protected Comision $comision;
    protected ?Materia $materia;
    protected Collection $evaluaciones;
    protected Collection $inscripciones;
    protected array $notasMap;
    protected $notasFinales;
    protected array $promedios;

    // Offsets
    private int $headerRows = 10;
    private int $totalCols = 0;

    public function __construct(
        Comision $comision,
        ?Materia $materia,
        Collection $evaluaciones,
        Collection $inscripciones,
        array $notasMap,
        $notasFinales,
        array $promedios
    ) {
        $this->comision = $comision;
        $this->materia = $materia;
        $this->evaluaciones = $evaluaciones;
        $this->inscripciones = $inscripciones;
        $this->notasMap = $notasMap;
        $this->notasFinales = $notasFinales;
        $this->promedios = $promedios;
        // Nro + Apellido + Nombre + DNI + evals + Promedio + Nota Final + Estado
        $this->totalCols = 4 + $this->evaluaciones->count() + 3;
    }

    public function title(): string
    {
        $name = $this->materia ? $this->materia->nombre : 'Sin materias';
        // Excel sheet names max 31 chars, no special chars
        return mb_substr(preg_replace('/[\\\\\/\?\*\[\]:]+/', '', $name), 0, 31);
    }

    public function array(): array
    {
        $notaAprobacion = \App\Services\ConfiguracionService::get('nota_aprobacion', 6);

        // Header institucional
        $rows = [
            ['UNIVERSIDAD TECNOLÓGICA NACIONAL'],
            [\App\Services\ConfiguracionService::get('nombre_institucion', 'FACULTAD REGIONAL LA PLATA')],
            [''],
            ['PLANILLA DE NOTAS - CURSO DE INGRESO'],
            [''],
            ['Comisión: ' . ($this->comision->codigo ? $this->comision->codigo . ' - ' : '') . $this->comision->nombre],
            ['Materia: ' . ($this->materia->nombre ?? 'Sin materia')],
            ['Período: ' . $this->comision->anio . ' - ' . $this->comision->periodo . ($this->comision->turno ? ' (' . ucfirst($this->comision->turno) . ')' : '')],
            ['Fecha de emisión: ' . date('d/m/Y H:i')],
            [''],
        ];

        // Encabezado de la tabla
        $header = ['Nro', 'Apellido', 'Nombre', 'DNI'];
        foreach ($this->evaluaciones as $eval) {
            $tipo = \App\Models\Evaluacion::tiposDisponibles()[$eval->tipo] ?? $eval->tipo;
            $header[] = $eval->nombre . ($eval->fecha ? ' (' . $eval->fecha->format('d/m') . ')' : '');
        }
        $header[] = 'Promedio';
        $header[] = 'Nota Final';
        $header[] = 'Estado';
        $rows[] = $header;

        // Datos de alumnos
        $nro = 1;
        foreach ($this->inscripciones as $ic) {
            $alumno = $ic->inscripcion?->getPerson() ?? $ic->academicoDato;
            $apellido = $alumno->apellido ?? '';
            $nombre = $alumno->nombre ?? '';
            $dni = $alumno->documento ?? $alumno->dni ?? '';
            $inscId = $ic->inscripcion_id;

            $row = [$nro, $apellido, $nombre, $dni];

            foreach ($this->evaluaciones as $eval) {
                $notaObj = $this->notasMap[$inscId][$eval->id] ?? null;
                $row[] = $notaObj ? (float) $notaObj->nota : '';
            }

            // Promedio
            $promedio = $this->promedios[$inscId] ?? null;
            $row[] = $promedio !== null ? (float) $promedio : '';

            // Nota final
            $nf = $this->notasFinales[$inscId] ?? null;
            $notaFinalVal = $nf ? (float) $nf->nota_final : '';
            $row[] = $notaFinalVal;

            // Estado
            if ($notaFinalVal !== '') {
                $row[] = $notaFinalVal >= $notaAprobacion ? 'Aprobado' : 'Desaprobado';
            } elseif ($promedio !== null) {
                $row[] = 'En curso';
            } else {
                $row[] = 'Pendiente';
            }

            $rows[] = $row;
            $nro++;
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,   // Nro
            'B' => 20,  // Apellido
            'C' => 20,  // Nombre
            'D' => 14,  // DNI
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastDataRow = $this->headerRows + 1 + $this->inscripciones->count();
        $lastColLetter = $this->getColumnLetter($this->totalCols);
        $tableHeaderRow = $this->headerRows + 1; // Row 11
        $notaAprobacion = \App\Services\ConfiguracionService::get('nota_aprobacion', 6);

        // Merge header cells
        $sheet->mergeCells("A1:{$lastColLetter}1");
        $sheet->mergeCells("A2:{$lastColLetter}2");
        $sheet->mergeCells("A4:{$lastColLetter}4");
        $sheet->mergeCells("A6:{$lastColLetter}6");
        $sheet->mergeCells("A7:{$lastColLetter}7");
        $sheet->mergeCells("A8:{$lastColLetter}8");
        $sheet->mergeCells("A9:{$lastColLetter}9");

        // Columnas de Promedio, Nota Final, Estado
        $promedioCol = $this->getColumnLetter(4 + $this->evaluaciones->count() + 1);
        $notaFinalCol = $this->getColumnLetter(4 + $this->evaluaciones->count() + 2);
        $estadoCol = $this->getColumnLetter(4 + $this->evaluaciones->count() + 3);

        // Colorear celdas de notas condicionalmente
        $firstEvalCol = 5; // Column E
        for ($row = $tableHeaderRow + 1; $row <= $lastDataRow; $row++) {
            // Notas de evaluaciones
            for ($col = $firstEvalCol; $col < $firstEvalCol + $this->evaluaciones->count(); $col++) {
                $cellCoord = $this->getColumnLetter($col) . $row;
                $cellValue = $sheet->getCell($cellCoord)->getValue();
                if ($cellValue !== '' && $cellValue !== null && is_numeric($cellValue)) {
                    $sheet->getStyle($cellCoord)->getFont()->setBold(true);
                    if ((float)$cellValue >= $notaAprobacion) {
                        $sheet->getStyle($cellCoord)->getFont()->getColor()->setRGB('15803D');
                    } else {
                        $sheet->getStyle($cellCoord)->getFont()->getColor()->setRGB('DC2626');
                    }
                }
            }

            // Promedio
            $promVal = $sheet->getCell($promedioCol . $row)->getValue();
            if ($promVal !== '' && $promVal !== null && is_numeric($promVal)) {
                $sheet->getStyle($promedioCol . $row)->getFont()->setBold(true);
                if ((float)$promVal >= $notaAprobacion) {
                    $sheet->getStyle($promedioCol . $row)->getFont()->getColor()->setRGB('15803D');
                    $sheet->getStyle($promedioCol . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7');
                } else {
                    $sheet->getStyle($promedioCol . $row)->getFont()->getColor()->setRGB('DC2626');
                    $sheet->getStyle($promedioCol . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');
                }
            }

            // Nota Final
            $nfVal = $sheet->getCell($notaFinalCol . $row)->getValue();
            if ($nfVal !== '' && $nfVal !== null && is_numeric($nfVal)) {
                $sheet->getStyle($notaFinalCol . $row)->getFont()->setBold(true)->setSize(11);
                if ((float)$nfVal >= $notaAprobacion) {
                    $sheet->getStyle($notaFinalCol . $row)->getFont()->getColor()->setRGB('15803D');
                    $sheet->getStyle($notaFinalCol . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('BBF7D0');
                } else {
                    $sheet->getStyle($notaFinalCol . $row)->getFont()->getColor()->setRGB('DC2626');
                    $sheet->getStyle($notaFinalCol . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FECACA');
                }
            }

            // Estado
            $estadoVal = $sheet->getCell($estadoCol . $row)->getValue();
            if ($estadoVal === 'Aprobado') {
                $sheet->getStyle($estadoCol . $row)->getFont()->setBold(true)->getColor()->setRGB('15803D');
                $sheet->getStyle($estadoCol . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7');
            } elseif ($estadoVal === 'Desaprobado') {
                $sheet->getStyle($estadoCol . $row)->getFont()->setBold(true)->getColor()->setRGB('DC2626');
                $sheet->getStyle($estadoCol . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');
            } elseif ($estadoVal === 'En curso') {
                $sheet->getStyle($estadoCol . $row)->getFont()->getColor()->setRGB('A16207');
                $sheet->getStyle($estadoCol . $row)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF9C3');
            }
        }

        // Promedio column light blue bg for header
        $sheet->getStyle("{$promedioCol}{$tableHeaderRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DBEAFE');
        // Nota Final column light green bg for header
        $sheet->getStyle("{$notaFinalCol}{$tableHeaderRow}")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D1FAE5');

        // Number format for note columns
        $firstEvalLetter = $this->getColumnLetter($firstEvalCol);
        $sheet->getStyle("{$firstEvalLetter}{$tableHeaderRow}:{$notaFinalCol}{$lastDataRow}")
            ->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER_00);

        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '003366']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '003366']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            4 => [
                'font' => ['bold' => true, 'size' => 13],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            6 => ['font' => ['bold' => true, 'size' => 11]],
            7 => ['font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '7C3AED']]],
            8 => ['font' => ['size' => 10]],
            9 => ['font' => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '6B7280']]],
            $tableHeaderRow => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '003366'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],
            "A{$tableHeaderRow}:{$lastColLetter}{$lastDataRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'D1D5DB'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            "A{$tableHeaderRow}:A{$lastDataRow}" => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            "D{$tableHeaderRow}:D{$lastDataRow}" => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            "{$firstEvalLetter}{$tableHeaderRow}:{$estadoCol}{$lastDataRow}" => [
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    private function getColumnLetter(int $columnNumber): string
    {
        $letter = '';
        while ($columnNumber > 0) {
            $columnNumber--;
            $letter = chr(65 + ($columnNumber % 26)) . $letter;
            $columnNumber = intval($columnNumber / 26);
        }
        return $letter ?: 'A';
    }
}
