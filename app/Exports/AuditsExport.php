<?php

namespace App\Exports;

use App\Models\Audit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditsExport implements FromCollection, WithHeadings, ShouldAutoSize, WithStyles
{
    public function __construct(private $audits) {}

    public function collection(): \Illuminate\Support\Enumerable
    {
        return $this->audits->map(fn (Audit $a) => [
            $a->audit_number,
            $a->audit_date->format('d/m/Y'),
            $a->category->name,
            $a->unit->name,
            $a->auditor->name,
            $a->shift,
            $a->officer_name,
            $a->total_items,
            $a->conform_items,
            $a->nonconform_items,
            $a->na_items,
            $a->compliance_percentage,
            $a->grade,
        ]);
    }

    public function headings(): array
    {
        return [
            'No Audit', 'Tanggal', 'Kategori', 'Unit', 'Auditor', 'Shift', 'Petugas',
            'Total Item', 'Sesuai', 'Tidak Sesuai', 'N/A', 'Kepatuhan (%)', 'Predikat',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
