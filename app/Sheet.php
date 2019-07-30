<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sheet extends Model
{
    protected $fillable = [
        'title', 'fields', 'mapping', 'file_path', 'excel_sheet_title'
    ];

    // protected $with = ['producer'];
    // protected $appends = ['producer'];


    public function producer()
    {
        $this->belongsTo('App\Producer');
    }
}
