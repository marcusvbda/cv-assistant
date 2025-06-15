<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'start_date',
    ];

    public $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function projectable()
    {
        return $this->morphTo();
    }
}
