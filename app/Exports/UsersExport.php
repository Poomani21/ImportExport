<?php

namespace App\Exports;

use App\Models\BulkUpload;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ShouldQueue;

class UsersExport implements FromCollection, WithHeadings 
{
    use Exportable;

    protected $search;
    protected $limit = 500000; // Set the limit for export

    public function __construct($search)
    {
        $this->search = $search;
    }

    /**
     * Retrieve data in chunks of 1000 records up to 10,000 records.
     */
    public function collection()
    {
        $query = BulkUpload::select('id', 'name', 'email', 'gender', 'phone', 'description', 'pincode', 'city', 'state', 'country')
                           ->orderBy('id'); // Ensure indexed ordering

        // Apply search filters if necessary
        if ($this->search) {
            $search = "%{$this->search}%";

            
                $query->where(function ($q) use ($search) {
                    $q->where('gender', 'like', $search)
                      ->orWhere('city', 'like', $search)
                      ->orWhere('state', 'like', $search)
                      ->orWhere('country', 'like', $search)
                      ->orwhere('pincode', 'like', $search);

                });
            
        }

        // Use chunking for memory efficiency
        $data = new Collection();
        $query->chunk(5000, function ($records) use (&$data) {
            foreach ($records as $record) {
                $data->push([
                    'name' => $record->name,
                    'email' => $record->email,
                    'gender' => $record->gender,
                    'phone' => $record->phone,
                    'description' => $record->description,
                    'pincode' => $record->pincode,
                    'city' => $record->city,
                    'state' => $record->state,
                    'country' => $record->country,
                ]);
            }

            // // Stop when we reach 10,000 records
            // if ($data->count() >= $this->limit) {
            //     return false;
            // }
        });

        // return $data->take($this->limit); // Return exactly 10,000 records
        return $data;
    }

    /**
     * Return the headings for the Excel file.
     */
    public function headings(): array
    {
        return [ 'Name', 'Email', 'Gender', 'Phone', 'Description', 'Pincode', 'City', 'State', 'Country'];
    }
}
