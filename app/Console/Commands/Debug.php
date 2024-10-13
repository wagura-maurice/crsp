<?php

namespace App\Console\Commands;

use Illuminate\Support\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\NumberParseException;

class Debug extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:debug';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debugging command to clean up records in areas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Loop through the areas defined in the config
        foreach (config('app.areas') as $area) {
            // Select all records in the respective area
            $records = DB::table($area)->get();

            /* foreach ($records as $record) {
                // Prepare the data to update
                $dataToUpdate = [];

                // Names: trim and ensure lowercase
                if (isset($record->names)) {
                    $dataToUpdate['names'] = strtolower(trim($record->names));
                }

                // DOB: trim and convert into Carbon object
                if (isset($record->dob) && strtotime(trim($record->dob)) !== false) {
                    // Trim and convert the DOB to a Carbon object and format it as a date string
                    $dataToUpdate['dob'] = Carbon::parse(trim($record->dob))->toDateString();
                }

                // Gender: trim and ensure it's MALE or FEMALE
                if (isset($record->gender)) {
                    $gender = strtolower(trim($record->gender));
                    $dataToUpdate['gender'] = ($gender === 'male') ? 'MALE' : (($gender === 'female') ? 'FEMALE' : null);
                }

                // Phone Number: trim and apply phone formatting function
                if (isset($record->phone_number)) {
                    $dataToUpdate['phone_number'] = $this->phoneNumberPrefix(trim($record->phone_number));
                }

                // Other fields: trim them if present
                $fieldsToTrim = [
                    'national_id', 'district_of_birth', 'county', 'education',
                    'skill_level', 'preferred_job', 'sub_county', 'ward', 'village', 'age'
                ];
                
                foreach ($fieldsToTrim as $field) {
                    if (isset($record->$field)) {
                        $dataToUpdate[$field] = strtolower(trim($record->$field));
                    }
                }

                // Update the record if there are any changes
                if (!empty($dataToUpdate)) {
                    DB::table($area)
                        ->where('id', $record->id) // Assuming each record has a unique identifier 'id'
                        ->update($dataToUpdate);
                }
            } */

            /* foreach ($records as $record) {
                // Check if the phone_number field is null
                if (is_null($record->phone_number)) {
                    // Delete the record from the current table based on the id
                    DB::table($area)->where('id', $record->id)->delete();
                }
            }  */

            foreach ($records as $record) {
                // Check if the record has a phone_number and if it exists in the DublicatePhoneNumber model
                if (!empty($record->phone_number) && \App\Models\DublicatePhoneNumber::where('value', $record->phone_number)->exists()) {
                    // If the phone_number is in the DublicatePhoneNumber model, fetch all values (as an array if needed)
                    $duplicatePhoneRecords = \App\Models\DublicatePhoneNumber::where('value', $record->phone_number)->get()->toArray();
                    
                    // Delete the record from the current table based on the id
                    DB::table($area)->where('id', $record->id)->delete();
                }

                // Check if the record has a national_id and if it exists in the DublicateNationalNumber model
                if (!empty($record->national_id) && \App\Models\DublicateNationalNumber::where('value', $record->national_id)->exists()) {
                    // If the national_id is in the DublicateNationalNumber model, fetch all values (as an array if needed)
                    $duplicateNationalRecords = \App\Models\DublicateNationalNumber::where('value', $record->national_id)->get()->toArray();
                    
                    // Delete the record from the current table based on the id
                    DB::table($area)->where('id', $record->id)->delete();
                }
            }           
        }

        $this->info('Records have been updated successfully.');
    }
    
    protected function phoneNumberPrefix(string $telephone, string $code = 'KE', int $length = -9): ?string
    {
        // Extract the relevant part of the phone number based on the provided length
        $number = substr($telephone, $length);
        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            // Parse the phone number
            $phoneNumber = $phoneUtil->parse($number, $code);

            // Validate the phone number
            if (!$phoneUtil->isValidNumber($phoneNumber)) {
                return null; // Return null if the number is not valid
            }

            // Format and return the phone number in E164 format
            return $phoneUtil->format($phoneNumber, PhoneNumberFormat::E164);
        } catch (NumberParseException $e) {
            return null; // Return null in case of parsing errors
        }
    }
}
