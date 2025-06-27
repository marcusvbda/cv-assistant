<?php

namespace App\Models;

use App\Enums\JobDescriptionAnalysisStatusEnum;
use App\Jobs\ProcessJobDescriptionAnalysisJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Auth;
use DB;

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

    protected static function booted()
    {
        if (Auth::check()) {
            $user = Auth::user();
            static::created(fn($item) => dispatch(new ProcessJobDescriptionAnalysisJob($item, $user, 'start_processing')));
            static::saved(function ($item) use ($user) {
                DB::table('job_description_analyses')->where('id', $item->id)->update(['status' => JobDescriptionAnalysisStatusEnum::PENDING->name]);
                dispatch(new ProcessJobDescriptionAnalysisJob($item, $user, 'start_processing'));
            });
        }
        parent::boot();
    }

    public function addressable()
    {
        return $this->morphTo();
    }
}
