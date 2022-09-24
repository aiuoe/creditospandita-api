<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\Models\User;
use App\Models\Modulos;
use App\Models\ListaModulos;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
class ModulosController extends Controller
{
    //
    private $NAME_CONTROLLER = 'ModulosController';

    public function all(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
        $per_page = (!empty($request->per_page)) ? $request->per_page : ListaModulos::count();
        $result = ListaModulos::paginate($per_page);
             $response = $result;

        if($result->isEmpty()){
            $response = [
                'data' => [],
                'total' => 0,
                'msj' => 'No se encontraron registros.',
            ];
            return response()->json($response, 200); 
        }
        return response()->json($response, 200);
    }catch (\Exception $e) {
        Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
        return response()->json([
            'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
        ], 500);
    }
    }

    public function allAdmin(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
        $per_page = (!empty($request->per_page)) ? $request->per_page : ListaModulos::count();
        $result = ListaModulos::where('administrador', 1)->paginate($per_page);
             $response = $result;

        if($result->isEmpty()){
            $response = [
                'data' => [],
                'total' => 0,
                'msj' => 'No se encontraron registros.',
            ];
            return response()->json($response, 200); 
        }
        return response()->json($response, 200);
    }catch (\Exception $e) {
        Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
        return response()->json([
            'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
        ], 500);
    }
    }
}
