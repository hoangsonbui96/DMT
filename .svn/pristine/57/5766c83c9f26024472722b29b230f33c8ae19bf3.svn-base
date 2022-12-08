<?php
namespace Modules\ProjectManager\Http\Services;

use App\Http\Controllers\Admin\AdminController;
use DateTime;
use Modules\ProjectManager\Http\Repositories\CommonRepo;
use Modules\ProjectManager\Http\Repositories\UserRepo;

class CommonService extends AdminController{
    public $commonRepo;
    public $userRepo;

    public function __construct(
        CommonRepo $commonRepo,
        UserRepo $userRepo
        )
    {
        $this->commonRepo = $commonRepo;
        $this->userRepo = $userRepo;
    }
    public function getPhaseTypes(){
        return $this->commonRepo->getPhaseTypes();
    }

    public function getPriorities(){
        return $this->commonRepo->getPriorities();
    }

    public function getUsers(){
        return $this->userRepo->get();
    }

    public function calculateProgress($tasks,$totalDuration){
        if($totalDuration == 0){
            return 0;
        }
        $progress = 0;
        foreach ($tasks as $key => $task) {
            $progress += $task->Progress*($task->Duration / $totalDuration);
        }
        if($progress != 0){
            return number_format($progress,2);
        }
        return 0;
    }

    public function formatDatetime($datetime){
        if(!$datetime){
            return;
        }
        $newDatetime = explode(' ',$datetime);
        if(str_contains($newDatetime[0],'/')){
            $newDate = explode('/',$newDatetime[0]);
            $datetime = $newDate[2].'-'.$newDate[1].'-'.$newDate[0].' ';
            if(isset($newDatetime[1])){
                $datetime .= substr($newDatetime[1],0,5);
            }
        }else{
            $newDate = explode('-',$newDatetime[0]);
            $datetime = $newDate[2].'/'.$newDate[1].'/'.$newDate[0].' ';
            if(isset($newDatetime[1])){
                $datetime = substr($newDatetime[1],0,5).' '.$datetime;
            };
        }
        return $datetime;
        
    }
}
