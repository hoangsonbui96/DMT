<?php

namespace App\Http\Controllers\Module;

use App\Http\Controllers\Controller;
use App\RoleScreenDetail;
use Modules\Event\Entities\Question;
use Nwidart\Modules\Laravel\Module;

class ModuleController extends Controller
{
    /*
     *  This function for module Event
     * */

    // Return event, result event and question
    public static function eventNotification(): array
    {
        if (!Module::isModuleEnabled("Event")) {
            return [];
        }
        if (auth()->user()->cant("action", RoleScreenDetail::where("alias", "EventList")->first())) {
            return [];
        }
        return Question::query()
            ->select('questions.*', 'users.FullName')
            ->selectRaw('(SELECT CASE WHEN COUNT(event_results.AID) > 0 THEN 1 WHEN COUNT(event_results.AID) = 0 THEN 0 END FROM event_results INNER JOIN answers ON event_results.AID = answers.id WHERE answers.QID = questions.id and event_results.UID = ' . auth()->id() . ') AS StatusA')
            ->leftJoin('users', 'questions.CreateUID', '=', 'users.id')
            ->where('users.Active', '=', 1)
            ->where('questions.Status', '=', 1)
            ->where('questions.SDate', '<=', date("Y-m-d"))
            ->where('questions.EDate', '>=', date("Y-m-d"))
            ->orderBy('questions.id')
            ->get()
            ->toArray();
    }

}
