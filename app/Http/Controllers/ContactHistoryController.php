<?php

namespace App\Http\Controllers;

use App\Models\ContactHistory;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class ContactHistoryController extends Controller
{
    //
    private $NAME_CONTROLLER = 'ContactHistoryController';

    public function store(Request $request)
    {
        try{
            $rules =  [
                'idSolicitudFk' => 'required',
                'idUserFk' => 'required',
                'fechaContacto' => 'required',
                // 'colaborador_id' => 'required',
                'proposito' => 'required',
                'metodoContacto' => 'required',
                'resultado' => 'required',
                'comentario' => 'required',
                'fechaPtp' => 'required',
                'montoPtp' => 'required',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {

                return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $historyContact = new ContactHistory($request->all());
            $historyContact->fechaContacto = date("Y-m-d H:m:s",strtotime($request->fechaContacto));
            $historyContact->fechaPtp = date("Y-m-d H:m:s",strtotime($request->fechaPtp));
            $historyContact->save();

            return response()->json("Registro guardado satisfactoriamente", Response::HTTP_CREATED);

        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }

    public function getUserContactsByRequest(Request $request){
        try{


        }catch (\Exception $e) {
            Log::error('Ha ocurrido un error en '.$this->NAME_CONTROLLER.': '.$e->getMessage().', Linea: '.$e->getLine());
            return response()->json([
                'message' => 'Ha ocurrido un error al tratar de guardar los datos.',
            ], 500);
        }
    }


}
