<?php

namespace App\Http\Controllers;
use App\Models\Referencias;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Config;
use Illuminate\Validation\ValidationException;

class ReferenciasController extends Controller
{
    private $NAME_CONTROLLER = 'ReferenciasController';
    // Obtener todos los usuarios //
    function getAll(Request $request){
        try{
            $users = Referencias::
            // join('tb_status','tb_status.idStatus', '=', 'tb_users.idStatusKf')
            // ->join('tb_profiles','tb_profiles.idProfile', '=', 'tb_users.idProfileKf')
            // ->join('tb_companies','tb_companies.idCompany', '=', 'tb_users.idCompanyKf')
            where('idUserFk','=',$request->id)
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
          
            // Validador //
            // $request->validate([
            //     'name' => 'required|max:124',
            //     'email' => 'required|email|max:124',
            //     'password' => 'required|max:124',
            //     'idProfileKf' => 'required',
            //     'idCompanyKf' => 'required|max:124',
            // ]);
            $arreglo=json_decode($request->getContent(), true);
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $user =  Referencias::create([

                'ReferenciaPersonalNombres'    => $arreglo['referencias']['nombre_personal'],
                'ReferenciaPersonalApellidos'     => $arreglo['referencias']['apellido_personal'],
                'ReferenciaPersonalCiudadFk'  => $arreglo['referencias']['ciudad_personal'],
                'ReferenciaPersonalTelefono'  => $arreglo['referencias']['tlfn_personal'],
                'ReferenciaFamiliarNombres'     => $arreglo['referencias']['nombre_familiar'],
                'ReferenciaFamiliarApellidos'  => $arreglo['referencias']['apellido_familiar'],
                'ReferenciaFamiliarCiudadFk'  => $arreglo['referencias']['ciudad_familiar'],
                'ReferenciaFamiliarTelefono' => $arreglo['referencias']['tlfn_familiar'],
                'QuienRecomendo' => $arreglo['referencias']['recomendo'],

                'relacionp' => $arreglo['referencias']['relacionp'],
                'relacionf' => $arreglo['referencias']['relacionf'],
                'emailp' => $arreglo['referencias']['emailp'],
                'emailf' => $arreglo['referencias']['emailf'],
                'iduserFk' => $arreglo['referencias']['id']

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
           if(Referencias::where('idUserFk',$request->id)->exists()){
           $user = Referencias::where('idUserFk',$request->id)->first();
           $user->ReferenciaPersonalNombres  = $request->ReferenciaPersonalNombres;
           $user->ReferenciaPersonalApellidos = $request->ReferenciaPersonalApellidos;
       
           $user->ReferenciaPersonalCiudadFk=$request->ReferenciaPersonalCiudadFk;
           $user->ReferenciaPersonalTelefono = $request->ReferenciaPersonalTelefono;
           $user->ReferenciaFamiliarNombres = $request->ReferenciaFamiliarNombres;
           $user->ReferenciaFamiliarApellidos = $request->ReferenciaFamiliarApellidos;
           $user->ReferenciaFamiliarCiudadFk = $request->ReferenciaFamiliarCiudadFk;
           $user->ReferenciaFamiliarTelefono = $request->ReferenciaFamiliarTelefono;

           $user->relacionp = $request->relacionp;
           $user->relacionf = $request->relacionf;
           $user->emailp = $request->emailp;
           $user->emailf = $request->emailf;
           $user->QuienRecomendo = $request->QuienRecomendo;
  
           $user->save();
           }else{

            $user = Referencias::create([

                'ReferenciaPersonalNombres'    => $request->ReferenciaPersonalNombres,
                'ReferenciaPersonalApellidos'     => $request->ReferenciaPersonalApellidos,
                'ReferenciaPersonalCiudadFk'  => $request->ReferenciaPersonalCiudadFk,
                'ReferenciaPersonalTelefono'  => $request->ReferenciaPersonalTelefono,
                'ReferenciaFamiliarNombres'     => $request->ReferenciaFamiliarNombres,
                'ReferenciaFamiliarApellidos'  => $request->ReferenciaFamiliarApellidos,
                'ReferenciaFamiliarCiudadFk'  => $request->ReferenciaFamiliarCiudadFk,
                'ReferenciaFamiliarTelefono' => $request->ReferenciaFamiliarTelefono,
                'QuienRecomendo' => $request->QuienRecomendo,

                'relacionp' => $request->relacionp,
                'relacionf' => $request->relacionf,
                'emailp' => $request->emailp,
                'emailf' => $request->emailf,
                'iduserFk' => $request->id

                ]);

           }
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
      function delete($idUser){
        try{

            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $user = Referencias::find($idUser);
            $user->delete();
            DB::commit(); // Guardamos la transaccion
            return response()->json("Usuario eliminado",200);
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
