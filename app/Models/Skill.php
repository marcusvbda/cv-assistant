<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'value',
    ];

    public $casts = [
        'value' => 'array'
    ];

    public function phoneable()
    {
        return $this->morphTo();
    }
}
