<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ConfigCalculadora;
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

class ConfigCalculadoraController extends Controller
{
    private $NAME_CONTROLLER = 'ConfigCalculadoraController';

    public function all(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
        $per_page = (!empty($request->per_page)) ? $request->per_page : ConfigCalculadora::count();
        $result = ConfigCalculadora::paginate($per_page);
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
            $result = ConfigCalculadora::where('id','=',$request->id)->first();
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
            $result = ConfigCalculadora::where('tipo','=',$request->tipo)->first();
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
            $result = ConfigCalculadora::create([
                'monto_minimo'    => $req['monto_minimo'],
                'monto_maximo'     => $req['monto_maximo'],
                'dias_minimo'    => $req['dias_minimo'],
                'dias_maximo'     => $req['dias_maximo'],
                'porcentaje_iva' => $req['porcentaje_iva'],
                'porcentaje_plataforma' => $req['porcentaje_plataforma'],
                'porcentaje_express' => $req['porcentaje_express'],
                'porcentaje_express_dos' => $req['porcentaje_express_dos'],
                'porcentaje_express_tres' => $req['porcentaje_express_tres'],
                'monto_restriccion'  => $req['monto_restriccion'],
                'dias_restriccion'  => $req['dias_restriccion'],
                'monto_restriccion_tooltip'  => $req['monto_restriccion_tooltip'],
                'tasa'  => $req['tasa'],
                'tipo' => $req['tipo'],
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
          
            $config = ConfigCalculadora::find($request->id);
            $config->monto_minimo  = $request->monto_minimo;
            $config->monto_maximo=$request->monto_maximo;
            $config->dias_maximo=$request->dias_maximo;
            $config->dias_minimo=$request->dias_minimo;
            $config->porcentaje_iva=$request->porcentaje_iva;
            $config->porcentaje_plataforma=$request->porcentaje_plataforma;
            $config->porcentaje_express=$request->porcentaje_express;
            $config->porcentaje_express_dos=$request->porcentaje_express_dos;
            $config->porcentaje_express_tres=$request->porcentaje_express_tres;
            $config->monto_restriccion= $request->monto_restriccion;
            $config->dias_restriccion= $request->dias_restriccion;
            $config->tipo= $request->tipo;
            $config->monto_restriccion_tooltip= $request->monto_restriccion_tooltip;
            $config->tasa= $request->tasa;
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
            $config = ConfigCalculadora::find($request->id);
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
