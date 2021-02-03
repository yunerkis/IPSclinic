<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Session;

class SessionsExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {  
        date_default_timezone_set('America/Bogota');

        $sessions = [];

        $collections = Session::with(['client', 'doctor'])->get();

        foreach ($collections as $key => $collection) {

            if (isset($collection->doctor->first_names)) {

                $doctor = $collection->doctor->first_names.' '.$collection->doctor->last_names;
            } else {

                $doctor = '';
            }

            $sessions[] = [
                'Fecha' => $collection->date, 
                'Doctor' => $doctor, 
                'Horario' => date('h:i a', strtotime($collection->time)), 
                'Cédula' => $collection->client->dni, 
                'Nombres y apellidos' => $collection->client->first_names.' '.$collection->client->last_names_1.' '.$collection->client->last_names_2
            ];
        }
        
        return collect($sessions);
    }

    public function headings(): array
    {   
        $headers = [
            'Fecha', 'Doctor', 'Horario', 'Cédula', 'Nombres y apellidos'
        ];

        return $headers;
    }  
}