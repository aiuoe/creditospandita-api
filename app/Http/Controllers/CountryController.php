<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\CountryRepositoryEloquent;

class CountryController extends Controller
{
    /**
     * @var $repository
     */
    protected $repository;

    /**
     * @var $responseCode
     */
    protected $responseCode = 200;

    public function __construct(CountryRepositoryEloquent $repository)
    {
        $this->repository = $repository;
    }
    /**
     * Display a listing of the resource.
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $resp = $this->repository->orderBy('name', 'ASC')->all();
        return response()->json($resp,$this->responseCode);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $country = [];
        try{
            $country = $this->repository->find($id);

        }catch(\Exception $e){
            $this->responseCode = 404;
        }
        return response()->json($country,$this->responseCode);
    }

}
