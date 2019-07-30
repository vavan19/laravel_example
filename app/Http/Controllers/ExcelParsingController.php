<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ExcelParserService;
use App\Repositories\SheetRepository;
use Illuminate\Support\Facades\Input;

class ExcelParsingController extends Controller
{
    protected $excelParserService;

    public function __construct(
        ExcelParserService $excelParserService,
        SheetRepository $sheetRepository
    )
    {
        $this->excelParserService = $excelParserService;
        $this->sheetRepository = $sheetRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->excelParserService->index();

        return View('excel_parser.index', compact('data'));
    }

    public function getExcelSheets(Request $request)
    {
        $id = $request->input('id');
        $sheet_path = $request->input('sheet_path');
        $excel_sheets = $this->excelParserService->getExcelSheets($id, $sheet_path);
        $selected_sheet_mapping = $this->sheetRepository->getSelectedSheetTitleMapping($id);

        if(!in_array($selected_sheet_mapping, $excel_sheets)){
            $selected_sheet_mapping = '';
        }else{
            unset( $excel_sheets[array_search($selected_sheet_mapping, $excel_sheets)] );
        }
        return response()->json([
            'selected_sheet_mapping' => $selected_sheet_mapping,
            'excel_sheets' => $excel_sheets
        ]);
    }

    public function getExcelHeadersWithDatabaseFields(Request $request)
    {
        $id = $request->input('sheet_id');
        $sheet_name = $request->input('sheet_name');
        $sheet_path = $request->input('sheet_path');

        // if($mapping = $this->sheetRepository->find($id)->mapping){
        //     return response()->json([
        //         'mapping' => $mapping,
        //     ]);
        // }

        $excel_columns = $this->excelParserService->getExcelColumns($id, $sheet_name, $sheet_path);

        $database_columns = $this->excelParserService->getDatabaseColumns();

        $mapping = $this->excelParserService->getFieldsMapping($id);

        return response()->json([
            'excel_columns' => $excel_columns,
            'database_columns' => $database_columns,
            'mapping' => $mapping,
        ]);
    }

    public function parseSheet(Request $request){
        $sheet_id = $request->input('sheet_id');
        $mapping = $request->input('sheet_column');
        $sheet_name = $request->input('sheet_name');
        $product_group_id = $request->input('product_group_id');
        $count = $this->excelParserService->parseSheet($sheet_id, $sheet_name, $mapping, $product_group_id);
        return response()->json([
            'message' => 'Successfully parsed '. $count .' products.',
        ]);
    }
}
