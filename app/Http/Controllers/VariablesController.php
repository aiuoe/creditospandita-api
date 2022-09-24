<?php

namespace App\Http\Controllers;

use App\Models\Financiera;
use App\Models\Calculadora;
use App\Models\Basica;
use App\Models\Referencias;
use App\Models\User;
use App\Models\Variables;
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

class VariablesController extends Controller
{
    private $NAME_CONTROLLER = 'VariablesController';
    // Obtener todos los usuarios //
    function getAll(Request $request){
        try{
        	      $this->validate($request, [
                    'offset' => 'integer',
                    'limit'  => 'integer|min:1',
                ], [
                    'offset.integer' => 'Debe ser numérico',
                    'limit.integer'  => 'Debe ser numérico',
    
                    'offset.min' => 'Debe tener al menos un número',
                    'limit.min'  => 'Debe tener al menos un número',
                ]);   
        if($request->offset && $request->limit){
            $users = Variables::offset($request->offset)->limit($request->limit)
            // join('tb_status','tb_status.idStatus', '=', 'tb_users.idStatusKf')
            // ->join('tb_profiles','tb_profiles.idProfile', '=', 'tb_users.idProfileKf')
            // ->join('tb_companies','tb_companies.idCompany', '=', 'tb_users.idCompanyKf')
            //  where('idUserFk','=',$request->id)
             ->get();
        }else{
            $users = Variables::
            // join('tb_status','tb_status.idStatus', '=', 'tb_users.idStatusKf')
            // ->join('tb_profiles','tb_profiles.idProfile', '=', 'tb_users.idProfileKf')
            // ->join('tb_companies','tb_companies.idCompany', '=', 'tb_users.idCompanyKf')
            //  where('idUserFk','=',$request->id)
             get();  
        }
                 $users2 = Variables::
            // join('tb_status','tb_status.idStatus', '=', 'tb_users.idStatusKf')
            // ->join('tb_profiles','tb_profiles.idProfile', '=', 'tb_users.idProfileKf')
            // ->join('tb_companies','tb_companies.idCompany', '=', 'tb_users.idCompanyKf')
            //  where('idUserFk','=',$request->id)
             get();
             
             $total=$users2->count();

                 $response = [
                    'blog' => $users,
                    'total' => $total,
                ];

            // if($users->isEmpty()){
            //     return response()->json([
            //         'msj' => 'No se encontraron registros.',
            //     ], 200); 
            // }
            return response()->json($response);
        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    function getAllBlog(Request $request){
        try{
            $users = Variables::
            // join('tb_status','tb_status.idStatus', '=', 'tb_users.idStatusKf')
            // ->join('tb_profiles','tb_profiles.idProfile', '=', 'tb_users.idProfileKf')
            // ->join('tb_companies','tb_companies.idCompany', '=', 'tb_users.idCompanyKf')
              where('id','=',$request->id)
             ->get();
            if($users->isEmpty()){
                return response()->json([
                    'msj' => 'No se encontraron registros.',
                ], 200); 
            }
            return response()->json($users);
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
          
          
            $user = Variables::create([
                'variable'    => $request->variable,
                'ponderacion'     => $request->ponderacion,
                'puntosTotales'     => $request->puntosTotales,
                'cantidadCategorias'     => $request->cantidadCategorias,
               
         
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

           $user = Variables::find($request->id);

           $user->variable  = $request->variable;
           $user->ponderacion = $request->ponderacion;
           $user->puntosTotales = $request->puntosTotales;
           $user->cantidadCategorias = $request->cantidadCategorias;
           
         
  
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
            $user = Variables::find($request->id);
            $user->delete();
            DB::commit(); // Guardamos la transaccion
            return response()->json("Pregunta eliminado",200);
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
