<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Resume extends Model
{
    public $table = "resumes";
    use SoftDeletes;
    public $fillable = [
        "name"
    ];
}
