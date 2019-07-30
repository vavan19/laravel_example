<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Sheet;

class Producer extends Model
{
    protected $with = ['sheets'];

    public function sheets()
    {
        return $this->hasMany("App\Sheet");
    }
}
