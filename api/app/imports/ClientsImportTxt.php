<?php

namespace App\Imports;

use App\Models\Client;

class ClientsImportTxt {

    public function collectionTxt($row)
    {   

            $register = explode(';', $row);
           
            if(isset($register[5])) {
                
                $client = Client::where('dni', $register[5])->first();
                try {
                  $status =$register[20] == 'RETIRADO'?'suspendido':$register[20];
                  if ($client) {
                   
                    $client->update([
                        'first_names' => mb_convert_encoding($register[8], 'UTF-8', 'ISO-8859-1'),
                        'last_names_1' => mb_convert_encoding($register[7], 'UTF-8', 'ISO-8859-1'),
                        'last_names_2' => mb_convert_encoding($register[6], 'UTF-8', 'ISO-8859-1'),
                        'dni' => $register[5],
                        'status' => $status,
                    ]);
                } else {
                    
                    Client::create([
                        'first_names' => mb_convert_encoding($register[8], 'UTF-8', 'ISO-8859-1'),
                        'last_names_1' => mb_convert_encoding($register[7], 'UTF-8', 'ISO-8859-1'),
                        'last_names_2' => mb_convert_encoding($register[6], 'UTF-8', 'ISO-8859-1'),
                        'dni' => $register[5],
                        'status' => $status,
                    ]);
                }
                } catch (\Throwable $th) {
                  dd($th);
                }
               
            }
    }
}