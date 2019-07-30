<?php
/**
 * Very good class
 *
 * @package \Controller\Parser\Excel
 * @author  Ivan <vavan194@gmail.com>
 * @license http://vk.com MIT
 */

namespace App\Services;

use Illuminate\Http\Request;
use App\Repositories\ProductRepository;
use App\Repositories\ProductGroupRepository;
use App\Repositories\ProducerRepository;
use App\Repositories\SheetRepository;
use Psy\Exception\ErrorException;

/**
 * ExcelParserService class
 * Use it to make any logic for your data.
 *
 * @package \Controller\Parser\Excel
 */
class ExcelParserService
{
    /**
     * Repository for products
     *
     * @var [ProductRepository]
     */
    protected $productRepository;
    /**
     * Repository for product groups
     *
     * @var [ProductGroupRepository]
     */
    protected $productGroupRepository;
    /**
     * Repository for producers
     *
     * @var [ProducerRepository]
     */
    protected $producerRepository;
    /**
     * Repository for sheets
     *
     * @var [SheetRepository]
     */
    protected $sheetRepository;

    /**
     * Constructor
     *
     * @param ProductRepository $productRepository
     * @param ProductGroupRepository $productGroupRepository
     * @param ProducerRepository $producerRepository
     * @param SheetRepository $sheetRepository
     */
    public function __construct(
        ProductRepository $productRepository,
        ProductGroupRepository $productGroupRepository,
        ProducerRepository $producerRepository,
        SheetRepository $sheetRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productGroupRepository = $productGroupRepository;
        $this->producerRepository = $producerRepository;
        $this->sheetRepository = $sheetRepository;
    }

    /**
     * Collect data from repositories.
     * Get producers with sheets,
     *
     * @return array
     */
    public function index()
    {
        $data = [];
        $data['producers'] = $this->producerRepository->all();
        $data['product_groups'] = $this->productGroupRepository->all();
        return $data;
    }

    public function getExcelSheets($id, $sheet_path)
    {


        $this->sheetRepository->update($id, ['file_path' => $sheet_path]);

        $sheet_extension = \PhpOffice\PhpSpreadsheet\IOFactory::identify($sheet_path);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($sheet_extension);
        $worksheetNames = $reader->listWorksheetNames($sheet_path);

        return $worksheetNames;
    }

    public function getExcelColumns($id, $sheet_name, $sheet_path='')
    {
        if(!$sheet_path){
            $sheet = $this->sheetRepository->find($id);
            $sheet_path = $sheet->file_path;
        }
        $sheet_extension = \PhpOffice\PhpSpreadsheet\IOFactory::identify($sheet_path);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($sheet_extension);
        $reader->setLoadSheetsOnly($sheet_name);
        $columns = [];

        $spreadsheet = $reader->load($sheet_path);
        $rowCount = $spreadsheet->getActiveSheet()->getHighestRow();

        /**
         * Also you can get column property and convert it into int this way
         * \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($Column);
         */


        for ($i=0; $i < $rowCount; $i++) {
            if(
                $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $i)->getValue() &&
                $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, $i)->getValue() &&
                $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $i)->getValue() &&
                $spreadsheet->getActiveSheet()->getCellByColumnAndRow(4, $i)->getValue() &&
                $spreadsheet->getActiveSheet()->getCellByColumnAndRow(5, $i)->getValue() &&
                $spreadsheet->getActiveSheet()->getCellByColumnAndRow(6, $i)->getValue()
            ){
                $columnIndex = 1;
                $columnsWithNoData = 0;
                $startEmptyRows = 0;
                while($columnsWithNoData != 10){
                    $column = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($columnIndex, $i)->getValue();
                    $columns[$columnIndex] = $this->getNameFromNumber($columnIndex);
                    $columnIndex++;
                    if($column){
                        $columnsWithNoData = 0;
                        $startEmptyRows = 0;
                    }else {
                        if($startEmptyRows == 0 && $columnsWithNoData == 0)
                            $startEmptyRows = $columnIndex;
                        $columnsWithNoData++;
                    }
                }
                $columns = array_slice($columns, 0, $startEmptyRows-2);
                break;
            }
        }

        return $columns;
    }

    public function getDatabaseColumns()
    {
        return $this->productRepository->getFillable();
    }

    public function getFieldsMapping($sheet_id){
        $mapping = $this->sheetRepository->getFieldsMapping($sheet_id);

        return $mapping;
    }

    public function parseSheet($sheet_id, $sheet_name='', $mapping, $product_group_id)
    {
        $sheet = $this->sheetRepository->find($sheet_id);
        if(!$sheet_name){
            $sheet_name = $sheet->excel_sheet_title;
        }

        $this->sheetRepository->update($sheet_id, ['mapping' => json_encode($mapping)]);
        if($mapping)
            $mapping = array_filter($mapping);
        else {
            return ErrorException('No mapping passed');
        }
        $parsedRowsCount = 0;
        $sheet_extension = \PhpOffice\PhpSpreadsheet\IOFactory::identify($sheet->file_path);
        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($sheet_extension);
        $reader->setLoadSheetsOnly($sheet_name);

        $spreadsheet = $reader->load($sheet->file_path);
        $rowCount = 100000;
        // find head row
        for ($i=1; $i < $rowCount; $i++) {
            if(
                $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $i)->getValue() &&
                $spreadsheet->getActiveSheet()->getCellByColumnAndRow(2, $i)->getValue() &&
                $spreadsheet->getActiveSheet()->getCellByColumnAndRow(3, $i)->getValue() &&
                $spreadsheet->getActiveSheet()->getCellByColumnAndRow(4, $i)->getValue() &&
                $spreadsheet->getActiveSheet()->getCellByColumnAndRow(5, $i)->getValue() &&
                $spreadsheet->getActiveSheet()->getCellByColumnAndRow(6, $i)->getValue()
            ){
                break;
            }
        }
        $i++;

        $rowsWithoutData = 0;
        // Go through files
        for ($i=$i; $i < $rowCount; $i++) {
            if(! $spreadsheet->getActiveSheet()->getCellByColumnAndRow(1, $i)->getValue()){
                $rowsWithoutData++;
                if($rowsWithoutData > 3)
                    break;
            }else{
                $rowsWithoutData = 0;
            }
            $attributes = [];
            foreach ($mapping as $index => $database_column) {
                $index++;
                $attributes[$database_column] = $spreadsheet->getActiveSheet()->getCellByColumnAndRow($index, $i)->getValue();
                if( $attributes[$database_column] instanceof PhpOffice\PhpSpreadsheet\RichText\RichText ){
                    $attributes[$database_column] = $attributes[$database_column]->getPlainText();
                }
            }
            if($attributes['ean'] && ctype_digit((string)$attributes['ean'])){
                $sheet = $this->sheetRepository->find($sheet_id);
                $attributes['sheet_id'] = $sheet->id;
                $attributes['producer_id'] = $sheet->producer_id;
                $attributes['product_group_id'] = $product_group_id;
                if( $product_id = $this->productRepository->existsWhere([ 'ean' => $attributes['ean'] ]) ){
                    $product = $this->productRepository->updateFromParse($product_id, $attributes);
                }else {
                    $product = $this->productRepository->create($attributes);
                }


                $parsedRowsCount++;
            }
        }

        $this->sheetRepository->update($sheet_id, ['excel_sheet_title' => $sheet_name]);
        return $parsedRowsCount;
    }

    private function getNameFromNumber($num) {
        $numeric = ($num - 1) % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval(($num - 1) / 26);
        if ($num2 > 0) {
            return getNameFromNumber($num2) . $letter;
        } else {
            return $letter;
        }
    }
}
