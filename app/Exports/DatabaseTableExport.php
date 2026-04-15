<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DatabaseTableExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected string $table;
    protected int $chunkSize;
    protected array $columns;

    public function __construct(string $table, int $chunkSize = 1000)
    {
        $this->table = $table;
        $this->chunkSize = $chunkSize;
        $this->columns = $this->getColumnNames();
    }

    public function collection(): Collection
    {
        // Fetch all rows as arrays (not objects) for clean Excel output
        return collect(
            DB::table($this->table)
                ->orderBy(DB::raw('1'))
                ->get()
                ->map(fn ($row) => (array) $row)
                ->toArray()
        );
    }

    public function headings(): array
    {
        return $this->columns;
    }

    public function title(): string
    {
        return $this->table;
    }

    public function styles(Worksheet $sheet)
    {
        $lastCol = count($this->columns);
        if ($lastCol === 0) {
            return [];
        }

        $colLetter = Coordinate::stringFromColumnIndex($lastCol);

        // Bold header row with light background
        $sheet->getStyle("A1:{$colLetter}1")->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 11,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFE2E8F0'],
            ],
        ]);

        // Auto-size columns (cap at 30 to stay fast)
        foreach (range(1, min($lastCol, 30)) as $i) {
            $letter = Coordinate::stringFromColumnIndex($i);
            $sheet->getColumnDimension($letter)->setAutoSize(true);
        }

        return [];
    }

    /**
     * Get column names for the table from the database schema.
     */
    protected function getColumnNames(): array
    {
        $cols = DB::select(
            "SELECT column_name FROM information_schema.columns WHERE table_schema = 'public' AND table_name = ? ORDER BY ordinal_position",
            [$this->table]
        );

        return array_map(fn ($c) => $c->column_name, $cols);
    }
}
