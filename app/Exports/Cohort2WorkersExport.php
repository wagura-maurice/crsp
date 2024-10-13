<?php

namespace App\Exports;

use App\Exports\Sheets\Cohort2WorkersSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Cohort2WorkersExport implements WithMultipleSheets
{
    protected $areas;

    public function __construct(array $areas)
    {
        $this->areas = $areas;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        // Create a new sheet for each area
        foreach ($this->areas as $area) {
            $sheets[] = new Cohort2WorkersSheet($area);
        }

        return $sheets;
    }
}
