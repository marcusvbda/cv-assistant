<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    public $table = "companies";
    use SoftDeletes;
    public $fillable = [
        "name"
    ];

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }
}
