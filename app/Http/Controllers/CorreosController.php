<?php

namespace App\Http\Controllers;

use App\Models\Financiera;
use App\Models\Calculadora;
use App\Models\Basica;
use App\Models\Referencias;
use App\Models\User;
use App\Models\Correos;
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

class CorreosController extends Controller
{
    private $NAME_CONTROLLER = 'CorreosController';
    // Obtener todos los usuarios //
    function getAll(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
            $per_page = (!empty($request->per_page)) ? $request->per_page : Correos::count();
            $result = Correos::paginate($per_page);
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

    function getAllCorreos(Request $request){
        try{
            $users = Correos::
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

            $descrip = explode('Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a>', $request->contenido);

            $user = Correos::create([
                
                'contenido'  => $descrip[0],
                'pertenece' => $request->pertenece
         
            ]);
           
              


                // $name=$userEnviado->first_name;
                // $email=$user->email;
                // $contenido=$request->contenido;
                // $titulo=$request->titulo;
                // $data = [
                //     'Nombre' => $name,
                //     'Contenido'=>$contenido
    
                //     ];
                // Mail::send('Mail.plantilla',$data, function($msj) use ($email,$name,$titulo){
                //     $msj->subject($name.','.$titulo);
                //     $msj->to($email);
                //  });


         


            DB::commit(); // Guardamos la transaccion

          
if($request->destinatario==true){

    $usuarios=User::where('email',$request->para)->first();
    $contenido=Correos::where('pertenece',$request->pertenece)->first();
    $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();
            // echo($contenido);
             if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();

          

                    if($credito->tipoCredito=="m"){
                        $tipo="Panda meses";
                    }else{
                        $tipo="Panda dias";
                    }
                    $monto=$credito->totalPagar;
                    $numerCredito=$credito->numero_credito;
                    $expedicion=$credito->created_at;
            }else{
                $numerCredito='No posee';
                $monto='No posee';
                $expedicion='No posee';
                $tipo='No posee';
               
            }

            $name=$usuarios->first_name;
            $last_name=$usuarios->last_name;
            $email=$usuarios->email;
            $cedula=$usuarios->n_document;
            $content=$contenido->contenido;

      

            $arregloBusqueda=array(
            "{{Nombre}}",
            "{{Apellido}}",
            "{{Email}}",
            "{{Cedula}}",
            "{{Ncredito}}",
            "{{Monto}}",
            "{{Expedicion}}",
            "{{TipoCredito}}",
            );
            $arregloCambiar=array(
                $name,
                $last_name,
                $email,
                $cedula,
                $numerCredito,
                $monto,
                $expedicion,
                $tipo,
            );
          
            $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);
            

      
       
            $data = [
                'Nombre' => $name,
                'Email'=>$email,
                'Apellido'=>$last_name,
                'Cedula'=>$cedula,
                'Contenido'=>$cntent2,

                ];
              
            Mail::send('Mail.plantilla',$data, function($msj) use ($email,$name){
                $msj->subject($name.',Creditos panda');
                $msj->to($email);
             });
}   
           
            return response()->json([
                'message' => 'Plantilla guardada!.',
            ], 200);
           
              $message = 'Correo enviado con exito!';
            //  return response()->json($user,201);
        }catch (\Exception $e) {
            if($e instanceof ValidationException) {
                return response()->json($e->errors(),402);
            }
            DB::rollback(); // Retrocedemos la transaccion
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message'   =>  $message,
            ]);
        }
    }

      // Modificar usuarios
      function update(Request $request){
        try{

           DB::beginTransaction(); // Iniciar transaccion de la base de datos
         
           $user = Correos::find($request->id);
           $descrip = explode('Powered by <a href="https://www.froala.com/wysiwyg-editor?pb=1" title="Froala Editor">Froala Editor</a>', $request->contenido);

           $user->pertenece  = $request->pertenece;
           $user->contenido=$descrip[0];

         
  
           $user->save();
           DB::commit(); // Guardamos la transaccion

           if($request->destinatario==true){

            $usuarios=User::where('email',$request->para)->first();
            $contenido=Correos::where('pertenece',$request->pertenece)->first();
            $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();
                    // echo($contenido);
                     if(Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->exists()){
                        $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();
        
                  
        
                            if($credito->tipoCredito=="m"){
                                $tipo="Panda meses";
                            }else{
                                $tipo="Panda dias";
                            }
                            $monto=$credito->totalPagar;
                            $numerCredito=$credito->numero_credito;
                            $expedicion=$credito->created_at;
                    }else{
                        $numerCredito='No posee';
                        $monto='No posee';
                        $expedicion='No posee';
                        $tipo='No posee';
                       
                    }
        
                    $name=$usuarios->first_name;
                    $last_name=$usuarios->last_name;
                    $email=$usuarios->email;
                    $cedula=$usuarios->n_document;
                    $content=$contenido->contenido;
        
              
        
                    $arregloBusqueda=array(
                    "{{Nombre}}",
                    "{{Apellido}}",
                    "{{Email}}",
                    "{{Cedula}}",
                    "{{Ncredito}}",
                    "{{Monto}}",
                    "{{Expedicion}}",
                    "{{TipoCredito}}",
                    );
                    $arregloCambiar=array(
                        $name,
                        $last_name,
                        $email,
                        $cedula,
                        $numerCredito,
                        $monto,
                        $expedicion,
                        $tipo,
                    );
                  
                    $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);
                    
        
              
               
                    $data = [
                        'Nombre' => $name,
                        'Email'=>$email,
                        'Apellido'=>$last_name,
                        'Cedula'=>$cedula,
                        'Contenido'=>$cntent2,
        
                        ];
                      
                    Mail::send('Mail.plantilla',$data, function($msj) use ($email,$name){
                        $msj->subject($name.',Creditos panda');
                        $msj->to($email);
                     });
        }   

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

         // Modificar usuarios
         function cambioEstatus(Request $request){
            try{
    
               DB::beginTransaction(); // Iniciar transaccion de la base de datos
             
               $user = Correos::find($request->id);
    
               $user->estatus  = $request->estatus;
            
    
             
      
               $user->save();
               DB::commit(); // Guardamos la transaccion
    
               return response()->json('Acción realizada con exito',200);
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
            $user = Correos::find($request->id);
            $user->delete();
            DB::commit(); // Guardamos la transaccion
            return response()->json("plantilla eliminado",200);
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
