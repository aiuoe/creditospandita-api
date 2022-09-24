<?php

namespace App\Http\Controllers;

use App\Models\RecordHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class RecordHistoryController extends Controller
{

    public function getUuid()
    {
        $uuid = Str::uuid();
        $recordHistory = new RecordHistory();
        $recordHistory->uuid = $uuid;
        $recordHistory->save();

        return response()->json([
            'uuid' => $uuid,
        ], 200);

    }

    public function verifyTodayVisit(Request $request)
    {
        $recordHistory = RecordHistory::where('uuid', $request->uuid)
            ->where('fecha_registro', date('Y-m-d'))->first();

            return $recordHistory;
        if($recordHistory){
            $uuid = $recordHistory->uuid;
        }else{
            $uuid = Str::uuid();
            $recordHistory = new RecordHistory();
            $recordHistory->uuid = $uuid;
            $recordHistory->save();
        }

        return response()->json([
            'uuid' => $uuid
        ], 200);
    }

    public function update($uuid, Request $request)
    {
        $recordHistory = RecordHistory::where('uuid', $uuid)
            ->where('fecha_registro', date('Y-m-d'))->first();
        // $recordHistory->fill($request->all());
        $recordHistory->estatus = $request->estatus;
        $recordHistory->update();

        return response()->json([
            'message' => 'Registro actualizado',
        ], 200);
    }
}
