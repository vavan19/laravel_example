<?php
namespace App\Repositories;

use App\Sheet;
use function GuzzleHttp\json_decode;

class SheetRepository
{

    protected $sheet;

    public function __construct(Sheet $sheet)
    {
        $this->sheet = $sheet;
    }
    public function create($attributes)
    {
        return $this->sheet->create($attributes);
    }

    public function all()
    {
        return $this->sheet->all();
    }

    public function find($id)
    {
        return $this->sheet->find($id);
    }

    public function update($id, array $attributes)
    {
        return $this->sheet->find($id)->update($attributes);
    }

    public function delete($id)
    {
        return $this->sheet->find($id)->delete();
    }

    public function getFieldsMapping($id){
        $sheet = $this->sheet->find($id);
        $mapping = [];
        if($sheet->mapping)
            $mapping = json_decode( $sheet->mapping );

        return $mapping;
    }

    public function getSelectedSheetTitleMapping($id)
    {
        return $this->sheet->find($id)->excel_sheet_title;
    }
}
