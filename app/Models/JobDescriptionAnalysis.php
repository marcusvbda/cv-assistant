<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobDescriptionAnalysis extends Model
{
    use SoftDeletes;

    public $table = "job_description_analyses";

    protected $fillable = [
        'name',
        'status',
        'description_type',
        'description',
    ];

    public function addressable()
    {
        return $this->morphTo();
    }
}
