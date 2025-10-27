<?php

namespace App\Http\Controllers;

use App\Models\Mutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class mutationController extends Controller
{
    public function mutation(Request $request)
    {
        // Validación de los datos de entrada
        $validator = Validator::make($request->all(), [
            'dna_input' => 'required|array|size:6',
            'dna_input.*' => 'required|string|size:6|regex:/^[ATCG]+$/i'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Please introduce valid DNA sequence'], 400);
        }

        $dna = $request->input('dna_input');
        $isMutant = $this->hasMutation($dna);
        
        // Convierte el string del input para guardar en la base de datos
        $dnaString = "";
        foreach($dna as $row){
            $dnaString = $dnaString . '[' . $row . ']';
        }
        $this->store($dnaString,$isMutant);

        return response()->json(['result' => $isMutant], $isMutant ? 200 : 403);
    }

    protected function hasMutation($dna)
    {
        // Se separan los strings de entrada en una sola matriz de chars
        $matrix = [];
        // Esta bandera indica si se encontró 1 secuencia de 4 letras iguales
        $found = false;
        foreach ($dna as $i => $row) {
            $matrix[$i] = str_split($row);
        }
        // Se obtiene el tamaño de la matriz 
        $dimension = count($matrix);

        // Se recorre la matriz
        $result = 0;
        for ($i = 0; $i < $dimension; $i++) {
            for ($j = 0; $j < $dimension; $j++) {
                // se llama al metodo que busca los adyacentes de nuestra coordenada actual
                $result = $result + $this->findNext($j, $i, $dimension, $matrix);
                // En caso de que ya tengamos una mutación anterior y esta nueva que se 
                // almacena en result, podemos regresar true dando como valida la operación
                // o en su caso, se observen las dos mutaciones en la misma coordenada
                if($found && $result > 1){
                    return true;
                }
                // En caso de que sea nuestra primera mutación, simplemente se activa la bandera
                elseif(!$found && $result > 0){
                    $found = true;
                }
            }
        }
        // Si no se cumplio la anterior simplemente retorna false
        return false;
    }

    // Se llaman x y y porque son coordenadas de la matriz
    function findNext($x, $y, $dimension, $matrix)
    {
        $cantidad = 0;
        // Se analiza si las siguientes ubicaciones podrian ser validas, en caso de serlo se llama al siguiente
        // método que realiza un análisis en cadena
        if($x + 1 < $dimension){
            if($this->findSecuence($x, $y, ($x + 1), $y, 1, $matrix, $dimension, $dimension)){ 
                $cantidad++; // derecha 
            }
        }
        if($y+1 < $dimension){
            if($this->findSecuence($x, $y, $x, ($y + 1), 1, $matrix, $dimension, $dimension)){
                $cantidad++; // abajo
            }
            if($x+1 < $dimension){
                if($this->findSecuence($x, $y, ($x + 1), ($y + 1), 1, $matrix, $dimension)){
                    $cantidad++; // diagonal
                }
            }
            if($x > 0){
                if($this->findSecuence($x, $y, ($x - 1), ($y + 1), 1, $matrix, $dimension)){
                    $cantidad++; // diagonal inverso
                }
            }
        }
        // Si ningún adyacente es valido o ninguno completa una secuenta, se regresa 0
        return $cantidad;
    }

    function findSecuence($xOld, $yOld, $x, $y, $n, $matrix, $dimension){
        // Aquí verificamos la longitud de nuestra cadena de caracteres, cuando esta
        // llegue a 4 quiere decir que es una mutación y podemos regresar true
        // Al llamarlo la primera vez desde el método anterior se envia 1 como parametro 
        // porque contamos con 1 caracter
        if($n == 4){
            return true;
        }
        // Aquí verificamos que los valores en las posiciones sean iguales
        if($matrix[$yOld][$xOld] == $matrix[$y][$x]){
            // Sumamos 1 a la longitud de nuestra cadena y calculamos la distancia para calcular nuestra
            // siguiente posición
            $n++;
            $xDif = $x - $xOld;
            $yDif = $y - $yOld;
            // Se evalua si estas nuevas coordenadas son validas dentro de la matriz, en caso
            // de no serlo se regresa false
            if($x + $xDif >= $dimension || $x + $xDif < 0){
                return false;
            }
            if($y + $yDif >= $dimension){
                return false;
            }
            // Se vuelve a llamar al método con nuevos valores para buscar el siguiente en
            // la cadena y se retorna el resultado obtenido
            return $this->findSecuence($x, $y, ($x + $xDif),($y + $yDif), $n, $matrix, $dimension);
        }
        // en caso de no cumplir se retorna false
        return false;
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
