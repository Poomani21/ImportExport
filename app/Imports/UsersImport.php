<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class UsersImport implements ToModel, WithHeadingRow, WithChunkReading, ShouldQueue
{
    /**
     * @var array
     */
    protected $rows = [];
    protected $processedRows = 0;
    protected $successfulRows = 0;
    /**
     * Process each row of the Excel file.
     *
     * @param array $row
     * @return void
     */
    public function model(array $row)
    {
        $this->processedRows++;
        try{
            // Check if required keys exist before processing
            // if (!isset($row['name'], $row['email'],$row['gender'], $row['phone'], $row['pincode'], $row['city'],$row['state'],$row['country'])) {
            //     Log::error('Missing required keys in row: ', $row);
            //     return;
            // }
       
        // Collect the rows to insert in batches
        $this->rows[] = [
            
            'name'        => $row['name'] ?? null,
            'email'       => $row['email'] ?? null,
            'phone'       => $row['phone'] ?? null,
            'description' => $row['description'] ?? null,
            'pincode'     => $row['pincode'] ?? null,
            'city'        => $row['city'] ?? 'anna nagar',
            'state'       => $row['state'] ?? 'chennai',
            'country'     => $row['country'] ?? 'india',
            'gender'      => $row['gender'] ?? 'male',
            'created_at'  => now(), // Add current timestamp for created_at
            'updated_at'  => now(), // Add current timestamp for updated_at
        
        ];

        // Insert rows in bulk when reaching the batch size
        if (count($this->rows) >= 1000) {
            $this->insertRows();
        }
        $this->successfulRows++;

    } catch (\Exception $e) {
        Log::error('Failed to process row', ['row' => $row, 'error' => $e->getMessage()]);
    }
    }

    /**
     * Insert rows into the database.
     */
    protected function insertRows()
    {
        if (!empty($this->rows)) {
            DB::table('bulk_upload')->insert($this->rows);
            $this->rows = []; // Reset the array after insertion
        }
    }

    /**
     * Define the chunk size for processing.
     *
     * @return int
     */
    public function chunkSize(): int
    {
        return 10000;
    }

    /**
     * Specify the row containing the headers.
     *
     * @return int
     */
    public function headingRow(): int
    {
        return 1; // The first row in the Excel file contains the headers
    }

    /**
     * Insert remaining rows after processing all chunks.
     */
    public function __destruct()
    {
        $this->insertRows();
    }
    public function getStats(): array
    {
        return [
            'processed' => $this->processedRows,
            'successful' => $this->successfulRows,
        ];
    }

}
