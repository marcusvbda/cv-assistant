<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'date',
    ];

    public $casts = [
        'date' => 'date',
    ];

    public function certificateable()
    {
        return $this->morphTo();
    }
}
