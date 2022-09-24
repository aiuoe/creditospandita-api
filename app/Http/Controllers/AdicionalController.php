<?php

namespace App\Http\Controllers;

use App\Models\Financiera;
use App\Models\Calculadora;
use App\Models\Basica;
use App\Models\Referencias;
use App\Models\User;
use App\Models\Adicional;
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
use Image;

class AdicionalController extends Controller
{
    private $NAME_CONTROLLER = 'AdicionalController';
    // Obtener todos los usuarios //
    

    function getAll(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
            $per_page = (!empty($request->per_page)) ? $request->per_page : Adicional::count();
            $result = Adicional::where('idUserfk',$request->id)->paginate($per_page);
            $response = $result;  

            if($result->isEmpty()){
                return response()->json([
                    'data'=>[],
                    'total'=>0,
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

    function getAllBlog(Request $request){
        try{
            $users = Adicional::
            // join('tb_status','tb_status.idStatus', '=', 'tb_users.idStatusKf')
            // ->join('tb_profiles','tb_profiles.idProfile', '=', 'tb_users.idProfileKf')
            // ->join('tb_companies','tb_companies.idCompany', '=', 'tb_users.idCompanyKf')
              where('id','=',$request->id)
             ->get();
            if($users->isEmpty()){
                return response()->json([
                    'data'=>[],
                    'total'=>0,
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
            // var_dump($request);
            if($request->imagen!=''){
                if($request->tipo == 'imagen'){
                    $resized_image = Image::make($request->imagen)->stream('jpg', 60);
                    Storage::disk('local')->put('\\public\\'.$request->nombre_imagen, $resized_image);
                    $nombreImagen=$request->nombre_imagen;
                }else{
                    $resized_image = base64_decode($request->imagen);
                    Storage::disk('local')->put('\\public\\'.$request->nombre_imagen, $resized_image);
                    $nombreImagen=$request->nombre_imagen;
                }
            }else{
                $nombreImagen=''; 
            }
       
           

            $user = Adicional::create([
                'nombre'     => $nombreImagen,
                'nombreDocumento' => $request->nombreDocumento,
                'idUserFk'  => $request->id,
                'tipo' => $request->tipo
         
            ]);
            $usuario = User::find($request->id);
            $msjAdmin = '<p>El siguiente usuario a subido archivos adicionales solicitados: <br> Nombre: '.$usuario->first_name.' '.$usuario->last_name.'<br> Cedula: '.$usuario->n_document.'<br> Email: '.$usuario->email.'<br> Nombre de documento: '.$request->nombreDocumento.'</p>';
            $infoAdmin =[
                'Contenido'=>$msjAdmin,
              ];
            // Mail::send('Mail.plantilla',$infoAdmin, function($msj) {
            //     $msj->subject('Notificacion de informacion adicional');
            //     $msj->to('info@creditospanda.com');
            //  });
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
         
           $user = Adicional::find($request->id);
           if($request->imagen!=''){
            Storage::disk('local')->put('\\public\\'.$request->nombre_imagen, base64_decode($request->imagen));
            $nombreImagen=$request->nombre_imagen;
        }else{
            $nombreImagen=$user->imagen; 
        }
           $user->titulo  = $request->titulo;
           $user->imagen = $nombreImagen;
           $user->descripcion=$request->descripcion;
           $user->descripcion_larga = $request->descripcion_larga;
         
  
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
            $user = Adicional::find($request->id);
            $user->delete();
            DB::commit(); // Guardamos la transaccion
            return response()->json("Blog eliminado",200);
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
    function export(Request $request){
        $pathtoFile = public_path().'/storage/'.$request->file;
        return response()->download($pathtoFile); 
    }
}
