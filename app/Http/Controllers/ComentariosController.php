<?php

namespace App\Http\Controllers;

use App\Models\Financiera;
use App\Models\Calculadora;
use App\Models\Basica;
use App\Models\Referencias;
use App\Models\User;
use App\Models\Comentarios;
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

class ComentariosController extends Controller
{
        
    private $NAME_CONTROLLER = 'TestimonioController';
    // Obtener todos los usuarios //
    function getAll(Request $request){
        try{
        	$request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
            $per_page = (!empty($request->per_page)) ? $request->per_page : Comentarios::count();
            $result = Comentarios::paginate($per_page);
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

    function getAllEvaluacion(Request $request){
        try{
        	$request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
            $per_page = (!empty($request->per_page)) ? $request->per_page : Comentarios::count();
            $result = Comentarios::where('evaluacionFk',$request->evaluacionFk)->where('tab',$request->tab)->paginate($per_page);
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

    function getSolicitud(Request $request){
        try{
        	$request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
            $per_page = (!empty($request->per_page)) ? $request->per_page : Comentarios::count();
            $result = Comentarios::where('idSolicitudFk',$request->idSolicitudFk)
            ->paginate($per_page);
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

    function all(Request $request){
        try{ 
            // $per_page = (!empty($request->per_page)) ? $request->per_page : Comentarios::count();
            $result = Comentarios::get();
            $response = [
                'testimonio'=>$result
            ];

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

    function getAllBlog(Request $request){
        try{
            $users = Comentarios::
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

            if($request->imagen!=''){
          
                $resized_image = Image::make($request->imagen)->stream('jpg', 60);
    // then use Illuminate\Support\Facades\Storage
    Storage::disk('local')->put('\\public\\'.$request->nombre_imagen, $resized_image); // check return for success and failure
               
                $nombreImagen=$request->nombre_imagen;
                // $user->anversoCedula=$nombreAnverso;
            }else{
                $nombreImagen='';
                // $user->anversoCedula=$user->anversoCedula;
               
            }
      
   
       
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            
            $user = Comentarios::create([
                'comentario'    => $request->comentario,
                'archivo'     => $nombreImagen,
                'evaluacionFk'  => $request->evaluacionFk,
                'tab'  => $request->tab,
                'usuario'  => $request->usuario,
         
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

    function createSolicitud(Request $request){
        try{

            if($request->imagen!=''){
          
                $resized_image = Image::make($request->imagen)->stream('jpg', 60);
    // then use Illuminate\Support\Facades\Storage
    Storage::disk('local')->put('\\public\\'.$request->nombre_imagen, $resized_image); // check return for success and failure
               
                $nombreImagen=$request->nombre_imagen;
                // $user->anversoCedula=$nombreAnverso;
            }else{
                $nombreImagen='';
                // $user->anversoCedula=$user->anversoCedula;
               
            }
      
   
       
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            
            $user = Comentarios::create([
                'comentario'    => $request->comentario,
                'archivo'     => $nombreImagen,
                'idSolicitudFk'  => $request->idSolicitudFk,
                'usuario'  => $request->usuario,
         
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
         
           $user = Comentarios::find($request->id);
           if($request->imagen!=''){
            Storage::disk('local')->put('\\public\\'.$request->nombre_imagen, base64_decode($request->imagen));
            $nombreImagen=$request->nombre_imagen;
            $user->archivo = $nombreImagen;
            }else{
                // $nombreImagen=$user->archivo; 
            }
           $user->comentario  = $request->comentario;
        //    $user->archivo = $nombreImagen;
        //    $user->evaluacionFk=$request->evaluacionFk;
        //    $user->tab= $request->tab;
         
  
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
            $user = Comentarios::find($request->id);
            $user->delete();
            DB::commit(); // Guardamos la transaccion
            return response()->json("Testimonio eliminado",200);
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
