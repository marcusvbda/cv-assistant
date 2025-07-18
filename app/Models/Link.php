<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Link extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'value',
    ];

    public function phoneable()
    {
        return $this->morphTo();
    }
}
