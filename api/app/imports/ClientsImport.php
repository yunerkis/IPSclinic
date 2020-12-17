<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Client;

class ClientsImport implements ToCollection {

    public function collection(Collection $rows)
    {   
        $collections = $rows->toArray();

        array_shift($collections);
        
        foreach ($collections as $row) 
        {
            $register = explode(';', $row[0]);
           
            if(isset($register[5])) {
                
                $client = Client::where('dni', $register[5])->first();
            
                if ($client) {
                   
                    $client->update([
                        'first_names' => $register[8],
                        'last_names_1' => $register[7],
                        'last_names_2' => $register[6],
                        'dni' => $register[5],
                        'status' => $register[20]
                    ]);
                } else {
                    
                    Client::create([
                        'first_names' => $register[8],
                        'last_names_1' => $register[7],
                        'last_names_2' => $register[6],
                        'dni' => $register[5],
                        'status' => $register[20]
                    ]);
                }
            }
        }
    }
}