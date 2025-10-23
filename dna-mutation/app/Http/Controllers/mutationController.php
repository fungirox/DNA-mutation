<?php

namespace App\Http\Controllers;
use App\Models\Mutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class mutationController extends Controller
{
    public function mutation(Request $request){
        $dna = $request -> input('input');
        if (!$dna){
            $dna = array('ATGCGA','CAGTGC','TTATGT','AGAAGG','CCCCTA','TCACTG');
        }

        // validator aquí deberiamos validar que si haya dna pero usando validator
        $inputString = "";
        foreach($dna as $row){
            $inputString = $inputString . $row;
        }

        $isMutant = $this->hasMutation($dna);
        $this->store($inputString,$isMutant);

        if($isMutant){
            return response()->json($isMutant,200);
        }
        return response()->json($isMutant,403);
    }

    protected function hasMutation($dna){
        return false;
    }

    protected function store($dna,$isMutant){
        // Al llamar a esta función almanacenamos en la base de datos nuestro registro (sin importar el resultado), unicamente enviamos los datos
        // del input y el resultado obtenido, el resto de datos se calculan en la base de datos
        $row = Mutation::create([
            'dna' => $dna,
            'isMutant' => $isMutant
        ]);

        return response() -> json($row, 201);
    }

    public function stats(){
        // Consulta en la base de datos el total de registros CON mutaciones (count_mutations) y el total de registros SIN mutaciones (count_no_mutations)
        $result = DB::table('dna')
            -> selectRaw('
                SUM(CASE WHEN isMutant = 1 THEN 1 ELSE 0 END) as count_mutations,
                SUM(CASE WHEN isMutant = 0 THEN 1 ELSE 0 END) as count_no_mutations
            ')
            -> first();
        
        // Aquí verifica que existan registros en la base de datos
        if(!$result){
            return response() -> json('No hay registros', 200);
        }

        // En caso de que existan registros realizará el calculo del rate
        // Primero verifica que los registros SIN mutaciones sea mayor a cero, en caso de ser mayor a 0 dividirá el total de registros CON mutaciones entre el total 
        // de registros SIN mutaciones. En caso contrario, se establece como 0. 
        $rate = (int)($result-> count_no_mutations) > 0 ? (int)($result-> count_mutations) / (int)($result-> count_no_mutations) : 0;

        // Aquí se almacena en $data ambos registros y el $rate para retornarlos junto al status
        $data = [
            'count_mutations' => $result->count_mutations,
            'count_no_mutations' => $result->count_no_mutations,
            'rate' => $rate
        ];

        return response() -> json($data, 200);
    }

    public function list(){
        // Aquí se consultan a la tabla dna los campos 'dna': el input original, 'isMutant': el resultado obtenido de hasMutation() y 'created_at': la fecha de 
        // creación del registo. Se ordenan de acuerdo al campo 'created_at' de forma descendiente para obtener siempre los ultimos 10 registros (especificado por limit(10))
        $result = DB::table('dna')
            -> select('dna', 'isMutant', 'created_at')
            -> orderByRaw('created_at DESC')
            -> limit(10)
            -> get();

        // Verifica si existen registros
        if(!$result){
            return response() -> json('No hay registros', 200);
        }
        
        // Aquí almacenamos en un array los registros obtenidos para regresar en el JSON de salida
        $data = [];
        foreach ($result as $row){
            $data[] = [
                'dnaString' => $row->dna,
                'isMutant' => $row->isMutant,
                'createdDate' => $row->created_at
            ];
        }
        
        return response() -> json($data, 200);
    }

}
