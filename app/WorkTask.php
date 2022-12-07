<?php

namespace App;

use App\Model\WorkTask\WorkTaskDocument;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed ProjectID
 */
class WorkTask extends Model
{
    //
    protected $table = 'work_tasks';
    protected $guarded = [];

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class, 'ProjectID');
    }

    public function members(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Members::class, "WorkTaskID");
    }

    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(WorkTaskDocument::class, "WorkTaskID")->orderByDesc("created_at");
    }

    public function dailyReports(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DailyReport::class, "TaskID")->orderByDesc("updated_at");
    }

    public function errorReviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ErrorReview::class, "WorkTaskID")->orderByDesc("updated_at");
    }
}
