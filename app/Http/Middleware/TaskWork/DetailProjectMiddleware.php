<?php

namespace App\Http\Middleware\TaskWork;

use App\Project;
use App\RoleGroupScreenDetailRelationship;
use App\RoleUserScreenDetailRelationship;
use Closure;

class DetailProjectMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (auth()->user()->role_group == RoleGroupScreenDetailRelationship::roleGroup('TaskWorking')->pluck('role_group_id')->first()
            && auth()->user()->role_group == 2)
            return $next($request);
        if (RoleUserScreenDetailRelationship::permission('TaskWorking')->exists())
            return $next($request);
        if (Project::findOrFail($request->route()->parameter('id'))->inLeaderOrMember(auth()->id()))
            return $next($request);
        return redirect()->back();
    }
}
