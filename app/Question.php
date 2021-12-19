<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'question';
    public $timestamps = false;

    protected $fillable = [
        'question_name','question_description','question_max_score'
    ];
}