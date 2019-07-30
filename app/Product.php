<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'ean', 'title', 'weight', 'price', 'shelf_life', 'storage_temp', 'sheet_id', 'producer_id', 'product_group_id'
    ];


    public function producer()
    {
        return $this->hasOne('App\Producer');
    }

    public function getFillable()
    {
        return $this->fillable;
    }
}
