<?php

namespace App\Providers;

use App\Menu;
use App\Policies\MenuPolicy;
use App\Policies\ProjectPolicy;
use App\Policies\RolePolicy;
use App\Project;
use App\RoleScreenDetail;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Menu::class => MenuPolicy::class,
        RoleScreenDetail::class => RolePolicy::class,
        Project::class => ProjectPolicy::class,
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
        Gate::define('view', 'App\Policies\MenuPolicy@view');
        Gate::define('viewmore', 'App\Policies\MenuPolicy@viewMore');
        Gate::define('update', 'App\Policies\MenuPolicy@update');
        Gate::define('create', 'App\Policies\MenuPolicy@create');
        Gate::define('delete', 'App\Policies\MenuPolicy@delete');
        Gate::define('admin', 'App\Policies\MenuPolicy@admin');
        Gate::define('action', 'App\Policies\RolePolicy@action');

        /* Create by Long Le
        *  Policy for project
        */
        Gate::define('viewAny-project', 'App\Policies\ProjectPolicy@viewAny');
        Gate::define('viewAll-project', 'App\Policies\ProjectPolicy@viewAll');
        Gate::define('export-project', 'App\Policies\ProjectPolicy@export');

        /* Create by Long Le
        *  Policy for work-task
        */
        Gate::define('view-task', 'App\Policies\WorkTaskPolicy@view');
        Gate::define('viewAll-task', 'App\Policies\WorkTaskPolicy@viewAll');
        Gate::define('viewAny-task', 'App\Policies\WorkTaskPolicy@viewAny');
        Gate::define('create-task', 'App\Policies\WorkTaskPolicy@create');
        Gate::define('export-task', 'App\Policies\WorkTaskPolicy@export');
        Gate::define('review-task', 'App\Policies\WorkTaskPolicy@review');
        Gate::define('edit-task', 'App\Policies\WorkTaskPolicy@update');
        Gate::define('delete-task', 'App\Policies\WorkTaskPolicy@delete');

        Passport::routes();
    }
}
