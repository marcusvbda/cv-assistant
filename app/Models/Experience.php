<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Experience extends Model
{
    public $table = "experiences";
    use SoftDeletes;
    public $fillable = [
        "position",
        "company_id"
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
