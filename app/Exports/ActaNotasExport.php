<?php

namespace App\Exports;

use App\Models\Comision;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ActaNotasExport implements FromArray, WithStyles, WithTitle, ShouldAutoSize
{
    protected array $datos;
    protected Comision $comision;

    public function __construct(array $datos, Comision $comision)
    {
        $this->datos = $datos;
        $this->comision = $comision;
    }

    public function array(): array
    {
        // Agregar información de cabecera
        $header = [
            ['UNIVERSIDAD TECNOLÓGICA NACIONAL'],
            ['FACULTAD REGIONAL RESISTENCIA'],
            [''],
            ['ACTA DE NOTAS - CURSO DE INGRESO'],
            [''],
            ['Comisión: ' . $this->comision->codigo . ' - ' . $this->comision->nombre],
            ['Período: ' . $this->comision->anio . ' - ' . $this->comision->periodo],
            ['Docente: ' . ($this->comision->docente->name ?? 'Sin asignar')],
            ['Fecha de emisión: ' . date('d/m/Y H:i')],
            [''],
        ];

        return array_merge($header, $this->datos);
    }

    public function title(): string
    {
        return 'Acta de Notas';
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = count($this->datos) + 10; // +10 por las filas de cabecera
        $lastCol = count($this->datos[0] ?? []);
        $lastColLetter = $this->getColumnLetter($lastCol);

        // Título UTN
        $sheet->mergeCells('A1:' . $lastColLetter . '1');
        $sheet->mergeCells('A2:' . $lastColLetter . '2');
        $sheet->mergeCells('A4:' . $lastColLetter . '4');

        return [
            // Título UTN - azul
            1 => [
                'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '003366']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '003366']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            4 => [
                'font' => ['bold' => true, 'size' => 14],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Información de comisión
            6 => ['font' => ['bold' => true]],
            7 => ['font' => ['bold' => true]],
            8 => ['font' => ['bold' => true]],
            9 => ['font' => ['italic' => true, 'size' => 10]],
            // Encabezados de tabla (fila 11)
            11 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '003366'],
                ],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            // Bordes para toda la tabla
            'A11:' . $lastColLetter . $lastRow => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                    ],
                ],
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
