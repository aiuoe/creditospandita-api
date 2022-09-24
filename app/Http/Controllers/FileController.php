<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\FileCreateRequest;
use App\Repositories\FileRepositoryEloquent;

class FileController extends Controller
{
    /**
     * @var $repository
     */
    protected $repository;

    /**
     * @var $responseCode
     */
    protected $responseCode = 200;

    public function __construct(FileRepositoryEloquent $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CreateFileRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(FileCreateRequest $request)
    {
    	$file = $request->file('file');
    	$original_name = $file->getClientOriginalName();
        $original_name = time() .'_.' . $file->getClientOriginalExtension();

    	if(file_exists(storage_path().'/app/private/'.$original_name)){
    		$datos = explode('.', $original_name);
    		$original_name = $datos[0].'-'.rand(10000,90000);
    	}
    	//dd($original_name);
    	$path = $request->file('file')->storeAs( 'private',$original_name);
    	$name_file = str_replace('private/', '', $path);

        $data = $this->repository->create([
            'name'              =>  $name_file,
            'original_name'     =>  $file->getClientOriginalName(),
            'size'              =>  $file->getSize(),
            'extension'         =>  $file->getClientOriginalExtension()
        ]);
        $resp = [
            'data'          =>  $data,
            'message'       =>  'Archvio Almacenado!'
        ];

        return response()->json( $resp, 200 );

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($file)
    {
        return Storage::put($file);

    }

    /**
     * Download the specified resource in storage.
     *

     * @param  string  $file
     * @return Illuminate\Support\Facades\Storage
     */
    public function download($file)
    {

        return Storage::download('private/'.$file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $file
     * @return Illuminate\Support\Facades\Storage
     */
    public function destroy($file)
    {
        Storage::delete('private/'.$file);
        return response()->json([
            'message'       =>  'Archivo eliminado'
        ],200);
    }
}
