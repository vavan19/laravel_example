<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\ProducerRepository;
use Symfony\Component\Console\Input\Input;

class ProducerController extends Controller
{

    /**
     * ProducerRepository
     *
     * @var [ProducerRepository]
     */
    protected $producerRepository;

    public function __construct(ProducerRepository $producerRepository)
    {
        $this->producerRepository = $producerRepository;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->all();
        // print_r($data); die;

        return response()->json([
            'message' => $this->producerRepository->update($data),
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getSheetsById(Request $request)
    {
        $data = $request->all();
        $sheets = $this->producerRepository->find($data['id'])->sheets()->get()->toArray();

        return response()->json([
            'sheets' => $sheets,
        ], 200);
    }

}
