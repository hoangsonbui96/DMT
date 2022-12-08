<?php

namespace Modules\ProjectManager\Http\Services;

use App\Http\Controllers\Admin\AdminController;
use Modules\ProjectManager\Http\Repositories\UserRepo;

class UserService extends AdminController
{

    public $userRepo;

    public function __construct(
        UserRepo $userRepo
    ) {
        $this->userRepo = $userRepo;
    }

    public function get()
    {
        return $this->userRepo->get();
    }

    public function getAll(){
        $users = $this->userRepo->getAll();
        $data['active'] = $users->where('Active',1)->whereNull('deleted_at');
        $data['inactive'] = $users->where('Active',0)->whereNull('deleted_at');
        $data['deleted'] = $users->diff($data['active'])->diff($data['inactive']);
        return $data;
    }
}
