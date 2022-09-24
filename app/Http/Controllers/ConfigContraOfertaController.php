<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfigContraOferta;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class ConfigContraOfertaController extends Controller
{
    private $NAME_CONTROLLER = 'ConfigContraOfertaController';

    public function all(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
        $per_page = (!empty($request->per_page)) ? $request->per_page : ConfigContraOferta::count();
        $result = ConfigContraOferta::paginate($per_page);
             $response = $result;

        if($result->isEmpty()){
            $response = [
                'config_caluladora' => [],
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

    public function get(Request $request){
        try{
            $result = ConfigContraOferta::where('id','=',$request->id)->first();
            $response = $result;
            if(empty($result)){
                return response()->json([
                    'result' => [],
                    'msj' => 'No se encontraron registros.',
                ], 200); 
            }
           
            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }
    public function getTipo(Request $request){
        try{
            $result = ConfigContraOferta::where('tipo_credito','=',$request->tipo)->first();
            $response = $result;
            if(empty($result)){
                return response()->json([
                    'result' => [],
                    'msj' => 'No se encontraron registros.',
                ], 200); 
            }
           
            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }
    public function store(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $req = json_decode($request->getContent(), true);
            $result = ConfigContraOferta::create([
                'monto_maximo'    => $req['monto_maximo'],
                'monto_minimo'    => $req['monto_minimo'],
                'tipo_credito'    => $req['tipo_credito'],
            ]);
            DB::commit(); // Guardamos la transaccion
            return response()->json($result,201);
        }catch (\Exception $e) {
            if($e instanceof ValidationException) {
                return response()->json($e->errors(),402);
            }
            DB::rollback(); // Retrocedemos la transaccion
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    public function update(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
          
            $config = ConfigContraOferta::find($request->id);
            $config->monto_maximo  = $request->monto_maximo;
            $config->monto_minimo = $request->monto_minimo;
            $config->tipo_credito = $request->tipo_credito;
            $config->save();

            DB::commit(); // Guardamos la transaccion
 
            return response()->json($config,200);

        }catch (\Exception $e) {
            if($e instanceof ValidationException) {
                return response()->json($e->errors(),402);
            }
            DB::rollback(); // Retrocedemos la transaccion
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    public function delete(Request $request){
        try{
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $config = ConfigContraOferta::find($request->id);
            $config->delete();
            DB::commit(); // Guardamos la transaccion
            return response()->json("Configuracion eliminada",200);
        }catch (\Exception $e) {
            if($e instanceof ValidationException) {
                return response()->json($e->errors(),402);
            }
            DB::rollback(); // Retrocedemos la transaccion
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }
}
