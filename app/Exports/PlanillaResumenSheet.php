<?php

namespace App\Exports;

use App\Models\Comision;
use App\Models\NotaFinalMateria;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PlanillaResumenSheet implements FromArray, WithStyles, WithTitle, ShouldAutoSize
{
    protected Comision $comision;
    protected $materias;
    protected Collection $inscripciones;
    protected int $materiasCount;

    public function __construct(Comision $comision, $materias, Collection $inscripciones)
    {
        $this->comision = $comision;
        $this->materias = $materias;
        $this->inscripciones = $inscripciones;
        $this->materiasCount = $materias->count();
    }

    public function title(): string
    {
        return 'Resumen';
    }

    public function array(): array
    {
        $notaAprobacion = \App\Services\ConfiguracionService::get('nota_aprobacion', 6);

        $rows = [
            ['UNIVERSIDAD TECNOLÓGICA NACIONAL'],
            [\App\Services\ConfiguracionService::get('nombre_institucion', 'FACULTAD REGIONAL LA PLATA')],
            [''],
            ['RESUMEN DE NOTAS FINALES - CURSO DE INGRESO'],
            [''],
            ['Comisión: ' . ($this->comision->codigo ? $this->comision->codigo . ' - ' : '') . $this->comision->nombre],
            ['Período: ' . $this->comision->anio . ' - ' . $this->comision->periodo . ($this->comision->turno ? ' (' . ucfirst($this->comision->turno) . ')' : '')],
            ['Alumnos inscriptos: ' . $this->inscripciones->count()],
            ['Fecha de emisión: ' . date('d/m/Y H:i')],
            [''],
            ['Este archivo contiene ' . $this->materiasCount . ' hojas adicionales con el detalle de notas por materia (ver pestañas de abajo).'],
            [''],
        ];

        // Cargar todas las notas finales de esta comisión
        $inscripcionIds = $this->inscripciones->pluck('inscripcion_id');
        $notasFinales = NotaFinalMateria::where('comision_id', $this->comision->id)
            ->whereIn('inscripcion_id', $inscripcionIds)
            ->get();

        // Header
        $header = ['Nro', 'Apellido', 'Nombre', 'DNI'];
        foreach ($this->materias as $materia) {
            $header[] = $materia->nombre;
        }
        $header[] = 'Estado General';
        $rows[] = $header;

        // Filas de alumnos
        $nro = 1;
        foreach ($this->inscripciones as $ic) {
            $alumno = $ic->inscripcion?->getPerson() ?? $ic->academicoDato;
            $row = [
                $nro,
                $alumno->apellido ?? '',
                $alumno->nombre ?? '',
                $alumno->documento ?? $alumno->dni ?? '',
            ];

            $inscId = $ic->inscripcion_id;
            $todasCargadas = true;
            $todasAprobadas = true;

            foreach ($this->materias as $materia) {
                $nf = $notasFinales->where('inscripcion_id', $inscId)->where('materia_id', $materia->id)->first();
                if ($nf) {
                    $row[] = (float) $nf->nota_final;
                    if ((float) $nf->nota_final < $notaAprobacion) {
                        $todasAprobadas = false;
                    }
                } else {
                    $row[] = '';
                    $todasCargadas = false;
                    $todasAprobadas = false;
                }
            }

            if ($todasCargadas && $todasAprobadas) {
                $row[] = 'Aprobado';
            } elseif ($todasCargadas && !$todasAprobadas) {
                $row[] = 'Desaprobado';
            } else {
                $row[] = 'Pendiente';
            }

            $rows[] = $row;
            $nro++;
        }

        return $rows;
    }

    public function styles(Worksheet $sheet): array
    {
        $tableHeaderRow = 13;
        $lastDataRow = $tableHeaderRow + $this->inscripciones->count();
        $totalCols = 4 + $this->materiasCount + 1;
        $lastColLetter = $this->getColumnLetter($totalCols);
        $notaAprobacion = \App\Services\ConfiguracionService::get('nota_aprobacion', 6);

        $sheet->mergeCells("A1:{$lastColLetter}1");
        $sheet->mergeCells("A2:{$lastColLetter}2");
        $sheet->mergeCells("A4:{$lastColLetter}4");
        $sheet->mergeCells("A11:{$lastColLetter}11");

        // Warning row
        $sheet->getStyle("A11:{$lastColLetter}11")->getFill()
            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEF3C7');
        $sheet->getStyle("A11")->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('92400E');

        // Color notas finales por materia
        for ($row = $tableHeaderRow + 1; $row <= $lastDataRow; $row++) {
            for ($col = 5; $col < 5 + $this->materiasCount; $col++) {
                $cellCoord = $this->getColumnLetter($col) . $row;
                $val = $sheet->getCell($cellCoord)->getValue();
                if ($val !== '' && $val !== null && is_numeric($val)) {
                    $sheet->getStyle($cellCoord)->getFont()->setBold(true);
                    if ((float)$val >= $notaAprobacion) {
                        $sheet->getStyle($cellCoord)->getFont()->getColor()->setRGB('15803D');
                        $sheet->getStyle($cellCoord)->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7');
                    } else {
                        $sheet->getStyle($cellCoord)->getFont()->getColor()->setRGB('DC2626');
                        $sheet->getStyle($cellCoord)->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');
                    }
                }
            }

            // Estado
            $estadoCol = $this->getColumnLetter($totalCols) . $row;
            $estadoVal = $sheet->getCell($estadoCol)->getValue();
            if ($estadoVal === 'Aprobado') {
                $sheet->getStyle($estadoCol)->getFont()->setBold(true)->getColor()->setRGB('15803D');
                $sheet->getStyle($estadoCol)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DCFCE7');
            } elseif ($estadoVal === 'Desaprobado') {
                $sheet->getStyle($estadoCol)->getFont()->setBold(true)->getColor()->setRGB('DC2626');
                $sheet->getStyle($estadoCol)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FEE2E2');
            }
        }

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
            7 => ['font' => ['size' => 10]],
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
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ],
            "A{$tableHeaderRow}:A{$lastDataRow}" => [
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
