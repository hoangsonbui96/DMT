<?php

namespace Modules\Recruit\Entities;

use App\MasterData;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Candidate extends Model
{
    protected $table = 'candidates';
    protected $guarded = [];

    public static function getCandidateByJobId($jobId, $paginate)
    {
        $candidate = Candidate::query()
            ->select('candidates.FullName', 'candidates.Email', 'candidates.Tel', 'candidates.CVpath', 'candidates.id', 'candidates.JobID', 'candidates.Experience', 'candidates.Note','candidates.Status', 'candidates.updated_at', 'jobs.Name', 'interviews.InterviewDate', 'interviews.Evaluate', 'interviews.Approve', 'interviews.id as interview_id')
            ->leftjoin('jobs', 'jobs.id', '=', 'candidates.jobID')
            ->leftjoin('interviews', 'interviews.CandidateID', '=', 'candidates.id');
        if ($jobId != null) {
            $candidate = $candidate->where('candidates.jobID', $jobId);
        }
        $candidate = $candidate->orderByDesc('candidates.id')->paginate($paginate);

        return $candidate;
    }
}
