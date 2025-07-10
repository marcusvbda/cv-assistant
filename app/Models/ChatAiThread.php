<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatAiThread extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'messages',
    ];

    public $casts = [
        'messages' => 'array',
    ];
}
