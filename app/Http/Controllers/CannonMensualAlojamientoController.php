<?php

namespace App\Http\Controllers;

use App\Models\Financiera;
use App\Models\Calculadora;
use App\Models\Basica;
use App\Models\Referencias;
use App\Models\User;
use App\Models\CannonMensualAlojamiento;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CannonMensualAlojamientoController extends Controller
{
    private $NAME_CONTROLLER = 'CannonMensualAlojamientoController';
    // Obtener todos los usuarios //
    function all(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
            $per_page = (!empty($request->per_page)) ? $request->per_page : CannonMensualAlojamiento::count();
            $result = CannonMensualAlojamiento::paginate($per_page);
            $response = $result;  

            if($result->isEmpty()){
                return response()->json([
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

    function get(Request $request){
        try{
            $users = CannonMensualAlojamiento::
              where('id','=',$request->id)
             ->first();
            
            return response()->json($users);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    function getEstratoAlojamiento(Request $request){
        try{
            
             if(CannonMensualAlojamiento::
                where('estrato','=',$request->estrato)
                ->where('alojamiento','=',$request->alojamiento)
                ->exists()){
                    $result = CannonMensualAlojamiento::
                    where('estrato','=',$request->estrato)
                    ->where('alojamiento','=',$request->alojamiento)
                    ->first();
                }else{
                    $result = CannonMensualAlojamiento::
                    where('estrato','=',$request->estrato)
                    ->where('alojamiento','=','OTRAS')
                    ->first();
                }
            
            return response()->json($result);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    function create(Request $request){
        try{
       
            DB::beginTransaction(); // Iniciar transaccion de la base de datos

            $user = CannonMensualAlojamiento::create([
                'estrato'    => $request->estrato,
                'alojamiento'  => $request->alojamiento,
                'monto'  => $request->monto,
         
            ]);
            DB::commit(); // Guardamos la transaccion
            return response()->json($user,201);
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

      // Modificar usuarios
      function update(Request $request){
        try{

           DB::beginTransaction(); // Iniciar transaccion de la base de datos
         
           $user = CannonMensualAlojamiento::find($request->id);

           $user->estrato  = $request->estrato;
           $user->alojamiento=$request->alojamiento;
           $user->monto = $request->monto;
         
           $user->save();
           DB::commit(); // Guardamos la transaccion

           return response()->json($user,200);
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

     // Eliminar usuarios
     function delete(Request $request){
        try{

            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $user = CannonMensualAlojamiento::find($request->id);
            $user->delete();
            DB::commit(); // Guardamos la transaccion
            return response()->json("Cannon Mensual Alojamiento eliminado",200);
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
