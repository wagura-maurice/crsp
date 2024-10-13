<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class Cohort2WorkersSheet implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithColumnFormatting, WithEvents
{
    use Exportable;
    
    protected $area;

    public function __construct($area)
    {
        $this->area = $area;
    }

    public function collection()
    {
        // Get all column names for the table except 'id'
        $columns = Schema::getColumnListing($this->area); // Fetch all column names
        $columns = array_diff($columns, ['id']); // Exclude the 'id' column

        // Fetch all records from the specified area's table, excluding the 'id' column
        return DB::table($this->area)->select($columns)->get();
    }

    public function title(): string
    {
        // Get the count of records for the specified area
        $count = DB::table($this->area)->count();

        // Return the area name along with the count of the records
        return $this->area . ' (' . $count . ')';
    }

    public function headings(): array
    {
        return [
            'names',
            'dob',
            'gender',
            'phone_number',
            'national_id',
            'district_of_birth',
            'county',
            'education',
            'skill_level',
            'preferred_job',
            'sub_county',
            'ward',
            'village',
            'age',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => NumberFormat::FORMAT_NUMBER,
            'E' => NumberFormat::FORMAT_NUMBER
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {                
                $cellRange = 'A1:W1'; // All headers
                // $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $event->sheet->getStyle($cellRange)->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            },
        ];
    }

    public function failed(\Throwable $th): void
    {
        throw $th;
    }
}
