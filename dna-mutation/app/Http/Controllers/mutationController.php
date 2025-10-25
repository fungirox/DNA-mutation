<?php

namespace App\Http\Controllers;

use App\Models\Mutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class mutationController extends Controller
{
    public function mutation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dna_input' => 'required|array|size:6',
            'dna_input.*' => 'required|string|size:6|regex:/^[ATCG]+$/i'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Please introduce valid DNA sequence'], 403);
        }

        $dna = $request->input('dna_input');

        $isMutant = $this->hasMutation($dna);
        //$this->store($inputString,$isMutant);


        return response()->json(['result' => $isMutant], $isMutant ? 200 : 403);
    }

    protected function hasMutation($dna)
    {
        $matrix = [];
        $found = false;
        foreach ($dna as $i => $row) {
            $matrix[$i] = str_split($row);
        }
        $rows = count($matrix);
        $cols = count($matrix[0]);

        for($i = 0 ; $i < $rows ; $i++){
            for($j = 0 ; $j < $cols ; $j++){
                if($i == 0){
                    if($j == 0){

                    }
                    elseif($j == $cols-1){

                    }
                    else{

                    }
                }
                elseif($i == $rows-1){

                }
                else{

                }
            }
        }

        return true;
    }

    // Se llaman x y y porque son coordenadas de la matriz
    function adyacente($x , $y, $rows, $cols){

    }

    protected function store($dna, $isMutant)
    {
        // Al llamar a esta función almanacenamos en la base de datos nuestro registro (sin importar el resultado), unicamente enviamos los datos
        // del input y el resultado obtenido, el resto de datos se calculan en la base de datos
        $row = Mutation::create([
            'dna' => $dna,
            'isMutant' => $isMutant
        ]);

        return response()->json($row, 201);
    }

    public function stats()
    {
        // Consulta en la base de datos el total de registros CON mutaciones (count_mutations) y el total de registros SIN mutaciones (count_no_mutations)
        $result = DB::table('dna')
            ->selectRaw('
                SUM(CASE WHEN isMutant = 1 THEN 1 ELSE 0 END) as count_mutations,
                SUM(CASE WHEN isMutant = 0 THEN 1 ELSE 0 END) as count_no_mutations
            ')
            ->first();

        // Aquí verifica que existan registros en la base de datos
        if (!$result) {
            return response()->json(['message' => 'No hay registros'], 204);
        }

        // En caso de que existan registros realizará el calculo del rate
        // Primero verifica que los registros SIN mutaciones sea mayor a cero, en caso de ser mayor a 0 dividirá el total de registros CON mutaciones entre el total 
        // de registros SIN mutaciones. En caso contrario, se establece como 0. 
        $rate = (int)($result->count_no_mutations) > 0 ? (int)($result->count_mutations) / (int)($result->count_no_mutations) : 0;

        // Aquí se almacena en $data ambos registros y el $rate para retornarlos junto al status
        $data = [
            'count_mutations' => $result->count_mutations,
            'count_no_mutations' => $result->count_no_mutations,
            'rate' => $rate
        ];

        return response()->json($data, 200);
    }

    public function list()
    {
        // Aquí se consultan a la tabla dna los campos 'dna': el input original, 'isMutant': el resultado obtenido de hasMutation() y 'created_at': la fecha de 
        // creación del registo. Se ordenan de acuerdo al campo 'created_at' de forma descendiente para obtener siempre los ultimos 10 registros (especificado por limit(10))
        $result = DB::table('dna')
            ->select('dna', 'isMutant', 'created_at')
            ->orderByRaw('created_at DESC')
            ->limit(10)
            ->get();

        // Verifica si existen registros en la base de datos
        if (!$result) {
            return response()->json(['message' => 'No hay registros'], 204);
        }

        // Se almacenan los datos en un array los registros obtenidos para el formato correcto del JSON de salida
        $data = [];
        foreach ($result as $row) {
            $data[] = [
                'dnaString' => $row->dna,
                'isMutant' => $row->isMutant,
                'createdDate' => $row->created_at
            ];
        }

        return response()->json($data, 200);
    }
}
