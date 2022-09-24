<?php

namespace App\Http\Controllers;

use App\Models\Financiera;
use App\Models\Contacto;
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
use App\Http\Requests\UserCreateRequest;
use App\Repositories\UserRepositoryEloquent;

class ContactoController extends Controller
{
    private $NAME_CONTROLLER = 'ContactoController';
    // Obtener todos los usuarios //
    function getAll(Request $request){
        try{
            $request->validate([
                'per_page'      =>  'nullable|integer',
                'page'          =>  'nullable|integer'
            ]);  
            $per_page = (!empty($request->per_page)) ? $request->per_page : Contacto::count();
            $result = Contacto::paginate($per_page);
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
       
            DB::beginTransaction(); // Iniciar transaccion de la base de datos
            $user = Contacto::create([


                'nombreApellidos'    => $request->nombreApellidos,
                'email'     => $request->email,
                'mensaje'  => $request->mensaje,
                'autorizacion'=>$request->autorizacion

            ]);

            $name=$request->nombreApellidos;
            $email=$request->email;
            // $data = [
            //     'Nombre' => $name,

            //     ];

                $usuarios=User::where('email',$user->email)->first();
                $contenido=Correos::where('pertenece','contacto')->first();
                // $credito=Calculadora::where('idUserFk',$usuarios->id)->orderBy('id','desc')->first();
    if($contenido->estatus=='activo'){
                // echo($contenido);
                $name=$request->nombreApellidos;
                $email=$request->email;
                $content=$contenido->contenido;
    
          
    
                $arregloBusqueda=array(
                "{{Nombre}}",
               
                "{{Email}}",
               
                );
                $arregloCambiar=array(
                    $name,
                  
                    $email,
                   
                );
              
                $cntent2=str_replace($arregloBusqueda,$arregloCambiar,$content);
                
    
          
           
                $data = [
                    'Nombre' => $name,
                    'Email'=>$email,
                  
                    'Contenido'=>$cntent2,
    
                    ];
                  
                Mail::send('Mail.contacto',$data, function($msj) use ($email,$name){
                    $msj->subject($name.',hemos recibido tu mensaje');
                    $msj->to($email);
                 });
                $msjAdmin = '<p>Mesaje de contacto recibido: <br> Nombre: '.$request->nombreApellidos.'<br> Email: '.$email.'<br> Mensaje: '.$request->mensaje.'</p>';
                $infoAdmin =[
                    'Contenido'=>$msjAdmin,
                    ];
                Mail::send('Mail.plantilla',$infoAdmin, function($msj) use ($email,$name){
                    $msj->subject('Notificacion de contacto');
                    $msj->to('info@creditospanda.com');
                    });
    }   



            // Mail::send('Mail.contacto',$data, function($msj) use ($email,$name){
            //     $msj->subject($name.', hemos recibido tu mensaje');
            //     $msj->to($email);
            //  });

            DB::commit(); // Guardamos la transaccion

          
            return response()->json('Mensaje enviado correctamente!',201);
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

    function show(Request $request){
        try{
            $users = Contacto::
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

      // Modificar usuarios
      function update(Request $request){
        try{
    
           DB::beginTransaction(); // Iniciar transaccion de la base de datos
           $user = Contacto::where('id',$request->id)->first();
           $user->nombreApellidos  = $request->nombreApellidos;
           $user->email = $request->email;
           $user->mensaje = $request->mensaje;

  
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
            $user = Contacto::find($request->id);
            $user->delete();
            DB::commit(); // Guardamos la transaccion
            return response()->json("Contacto eliminado",200);
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
