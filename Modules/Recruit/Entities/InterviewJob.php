<?php

namespace Modules\Recruit\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InterviewJob extends Model
{
    protected $table = 'jobs';
    protected $guarded = [];

    public static function get_list_interviewJob($page)
    {
        $active_job = 1;
        $result = InterviewJob::query()
            ->selectRaw('jobs.id,jobs.name,jobs.content,jobs.active,jobs.start_date,jobs.end_date,jobs.updated_at,COUNT(candidates.id) as num_candides')
            ->leftJoin('candidates', 'jobs.id', '=', 'candidates.jobID')
            ->groupBy('jobs.id')
            ->where('jobs.Active', $active_job)
            ->orderByDesc('jobs.start_date')
            ->paginate($page);
        return $result;
    }

    public static function get_interviewJob_by_id($id)
    {
        if ($id == 0) {
            $result = InterviewJob::query()
                ->select('id', 'name')
                ->where('Active', 1)
                ->orderByDesc('jobs.id')
                ->get();
        } else {
            $result = InterviewJob::query()
                ->select('id', 'name')
                ->where('id', $id)
                ->where('Active', 1)
                ->orderByDesc('jobs.id')
                ->get();
        }
        return $result;
    }
    public static function get_name($id)
    {
        if ($id != null) {
            $job = InterviewJob::select('name')->where('id', $id)->first();
            if ($job != null) {
                return $job->name;
            }
        }
    }
}
