<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\SheetRepository;
use Illuminate\Support\Facades\Input;

class SheetsController extends Controller
{
    /**
     * Use it to make simple operations with data.
     *
     * @var [SheetRepository]
     */
    protected $sheetRepository;

    public function __construct(SheetRepository $sheetRepository)
    {
        $this->sheetRepository = $sheetRepository;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

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
    public function update(Request $request, $id)
    {
        //
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


    public function upload(Request $request)
    {
        $sheet = $this->sheetRepository->find($request['sheet_id']);
        if($sheet){
            $file = $request->file('excel_file');
            if (file_exists('uploads/tmp/'.$file->getClientOriginalName())) {
                unlink('uploads/tmp/'.$file->getClientOriginalName());
            }

            $path = $file->move('uploads/tmp/', $file->getClientOriginalName())->getRealPath();

            $this->sheetRepository->update($request['sheet_id'], ['file_path' => $path]);
            return response()->json([
                'sheet_path' => $path,
            ], 200);
        }

    }
}
