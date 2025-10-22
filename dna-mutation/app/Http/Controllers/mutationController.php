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

        return response()->json([
            'isMutant' => $isMutant
        ]);
    }

    public function stats(){
        $result = DB::table('dna')
            -> selectRaw('
                SUM(CASE WHEN isMutant = 1 THEN 1 ELSE 0 END) as count_mutations,
                SUM(CASE WHEN isMutant = 0 THEN 1 ELSE 0 END) as count_no_mutations
            ')
            -> first();
        
        if(!$result){
            $data = [
                'message' => 'No hay registros',
                'status' => '200'
            ];
            return response() -> json($data, 200);
        }

        $rate = (int)($result-> count_no_mutations) > 0 ? (int)($result-> count_mutations) / (int)($result-> count_no_mutations) : 0;

        $data = [
            'count_mutations' => $result->count_mutations,
            'count_no_mutations' => $result->count_no_mutations,
            'rate' => $rate
        ];

        return response() -> json($data, 200);
    }

    public function list(){
        $result = DB::table('dna')
            -> selectRaw('
                dna, isMutant, created_at
            ')
            -> orderByRaw('created_at DESC')
            -> get();

        //$dna = Mutation::all();

        if(!$result){
            $data = [
                'message' => 'No hay registros',
                'status' => '200'
            ];
            return response() -> json($data, 200);
        }
        
        return response() -> json($result, 200);
    }

    protected function hasMutation($dna){
        return true;
    }

    protected function store($dna,$isMutant){
        $row = Mutation::create([
            'dna' => $dna,
            'isMutant' => $isMutant
        ]);

        $data = [
            'row' => $row,
            'status' => 201
        ];

        return response() -> json($data, 201);
    }
}
