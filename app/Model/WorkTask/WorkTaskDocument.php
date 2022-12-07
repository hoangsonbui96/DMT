<?php

namespace App\Model\WorkTask;

use App\WorkTask;
use Illuminate\Database\Eloquent\Model;

class WorkTaskDocument extends Model
{
    //
    protected $table = "work_task_documents";
    protected $fillable = ["TaskID", "Note", "DocPath", "UserID", "DocName"];

    public function work_task(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(WorkTask::class, "WorkTaskID")->orderByDesc("id");
    }
}
