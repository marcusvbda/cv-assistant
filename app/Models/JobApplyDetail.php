<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplyDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'job_description_analysis_id',
        'resume',
        'cover_letter',
        'percentage_fit',
        'comment'
    ];

    public function jobDescriptionAnalysis()
    {
        return $this->belongsTo(JobDescriptionAnalysis::class);
    }
}
