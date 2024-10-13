<?php

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\Cohort2WorkersExport;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// http://crsp.devops/get/cohort2/workers?Westlands=true&Starehe=true&Mathare=true&Makadara=true&Langata=true&Kibra=true&Kasarani=true&Njiru=true&Kamukunji=true&Dagoretti=true&Embakasi=true

Route::get('/get/cohort2/workers', function (Request $request) {
    // Prepare an array for areas that are set to true
    $selectedAreas = [];

    // Loop through the areas and check if each one is set to true
    foreach (config('app.areas') as $area) {
        if ($request->get($area) === 'true') {
            $selectedAreas[] = $area; // Add area to the list if true
        }
    }

    // If no areas are selected, return a message
    if (empty($selectedAreas)) {
        return response()->json(['message' => 'No data to export'], 404);
    }

    // Call the Cohort2WorkersExport and pass the selected areas to it
    return Excel::download(new Cohort2WorkersExport($selectedAreas), 'cohort_1_after_clean_up_with_Pv_Climate_works_12th_Oct_2024' . now()->format('Y-m-d_His') . '.xlsx');
})->name('get.cohort2.workers');
