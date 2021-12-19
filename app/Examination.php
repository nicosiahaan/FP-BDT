<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    protected $table = 'examination';
    public $timestamps = false;

    protected $fillable = [
        'examination_name','examination_max_question','examination_datetime'
    ];
}