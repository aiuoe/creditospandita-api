<?php

namespace App\Http\Controllers;

use App\Models\Financiera;
use App\Models\Calculadora;
use App\Models\Basica;
use App\Models\Referencias;
use App\Models\User;
use App\Models\Filtrado;
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
use App\Repositories\AtributosRepositoryEloquent;

class FiltradoController extends Controller
{
    private $NAME_CONTROLLER = 'FiltradoController';
    /**
 * @var $repository
 */
protected $repository;
public function __construct(AtributosRepositoryEloquent $repository)
{
    $this->repository = $repository;
}
// Obtener todos los usuarios //
function getAll(Request $request){
    try{
        $request->validate([
            'per_page'      =>  'nullable|integer',
            'page'          =>  'nullable|integer',
            'search'        =>  'nullable|string',
            'orderBy'       =>  'nullable|string',
            'sortBy'        =>  'nullable|in:desc,asc',
            'until'         =>  'nullable|date_format:Y-m-d',
            'since'         =>  'nullable|date_format:Y-m-d'
        ]);  
        $per_page = (!empty($request->per_page)) ? $request->per_page : Filtrado::count();
        
        $result = Filtrado::orderBy('variable','asc')->paginate($per_page);

 

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
        $users = Filtrado::
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

      
        $user = Filtrado::create([
            'variable'    => $request->variable,
            'valor'     => $request->valor,
            'signo'     => $request->signo,

           
     
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

       $user = Filtrado::find($request->id);
  
       $user->variable  = $request->variable;
       $user->valor = $request->valor;
       $user->signo = $request->signo;

       
     

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
        $user = Filtrado::find($request->id);
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
