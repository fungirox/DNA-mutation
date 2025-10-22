<?php

namespace App\Http\Controllers;
use App\Models\Mutation;
use Illuminate\Http\Request;

class mutationController extends Controller
{
    public function mutation(Request $request){
        $dna = $request -> input('input');
        if (!$dna){
            $dna = array('ATGCGA','CAGTGC','TTATGT','AGAAGG','CCCCTA','TCACTG');
        }

        $isMutant = $this->hasMutation($dna);

        return response()->json([
            'isMutant' => $isMutant
        ]);
    }

    public function stats(){
        return 'stats';
    }

    public function list(){
        $dna = Mutation::all();

        if($dna->isEmpty()){
            $data = [
                'message' => 'No hay registros',
                'status' => '200'
            ];
            return response() -> json($data, 200);
        }
        
        $data = [
            'dna' => $dna,
            'status' => '200'
        ];

        return response() -> json($data, 200);
    }

    protected function hasMutation($dna){
        return $dna;
    }
}
