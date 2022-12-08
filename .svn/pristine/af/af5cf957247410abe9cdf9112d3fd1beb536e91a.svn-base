<?php

namespace App\Policies;

use App\Members;
use App\Project;
use App\RoleUserScreenDetailRelationship;
use App\User;
use App\WorkTask;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkTaskPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any work tasks.
     *
     * @param \App\User $user
     * @return mixed
     */
    // In leader of project
    public function viewAny(User $user)
    {
        //
        return RoleUserScreenDetailRelationship::permission('TaskWorking')->exists();
    }

    /**
     * Determine whether the user can view the work task.
     *
     * @param \App\User $user
     * @param \App\WorkTask $workTask
     * @return mixed
     */
    public function view(User $user, WorkTask $workTask)
    {
        //
        if ($user->role_group != 2) {
            return $workTask->project()->Leader([auth()->id()])->exists() || $user->id == Members::query()->where('WorkTaskID', $workTask->id)->first()->UserID;
        }
        return true;
    }

    /**
     * Determine whether the user can create work tasks.
     *
     * @param \App\User $user
     * @param Project $project
     * @return mixed
     */
    public function create(User $user, Project $project)
    {
        return RoleUserScreenDetailRelationship::permission('TaskWorkingAdd')->exists() || $user->role_group == 2 ||
            in_array($user->id, explode(',', $project->Leader));
    }

    /**
     * Determine whether the user can update the work task.
     *
     * @param \App\User $user
     * @param WorkTask $task
     * @return mixed
     */
    public function update(User $user, WorkTask $task)
    {
        //
        if ($user->role_group != 2) {
            return in_array($user->id, explode(',', $task->project()->first()->Leader)) || (RoleUserScreenDetailRelationship::permission('TaskWorkingEdit')->exists() && !is_null($task->members()->first()) && $task->members()->first()->UserID == $user->id);
        }
        return true;
    }

    /**
     * Determine whether the user can delete the work task.
     *
     * @param \App\User $user
     * @param WorkTask $task
     * @return mixed
     */
    public function delete(User $user, WorkTask $task)
    {
        //
        if ($user->role_group != 2) {
            return in_array($user->id, explode(',', $task->project()->first()->Leader)) || (RoleUserScreenDetailRelationship::permission('TaskWorkingDelete')->exists() && !is_null($task->members()->first()) && $task->members()->first()->UserID == auth()->id());
        }
        return true;
    }

    /**
     * Determine whether the user can restore the work task.
     *
     * @param \App\User $user
     * @param \App\WorkTask $workTask
     * @return mixed
     */
    public function restore(User $user, WorkTask $workTask)
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the work task.
     *
     * @param \App\User $user
     * @param \App\WorkTask $workTask
     * @return mixed
     */
    public function forceDelete(User $user, WorkTask $workTask)
    {
        //
    }

    //In admin group
    public function viewAll(User $user, $id)
    {
        if ($user->role_group != 2) {
            return Project::query()->where('id', $id)->Leader([$user->id])->exists();
        }
        return true;
    }

    public function export()
    {
        return true;
    }

    public function review(User $user, WorkTask $task)
    {
        if ($user->role_group != 2) {
            return RoleUserScreenDetailRelationship::permission('TaskWorkingReview')->exists() || in_array($user->id, explode(',', $task->project()->first()->Leader));
        }
        return true;
    }
}
