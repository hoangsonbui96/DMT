<?php

namespace App\Http\Controllers\Admin\TaskWork;

use App\Http\Controllers\Admin\AdminController;
use App\WorkTaskHistory;
use Illuminate\Http\Request;

trait TraitTaskWorkHistory
{
    //
    private $id_task;
    private $value_keys = [
        'insert' => 1,
        'update' => 2,
        'delete' => 3,
    ];

    private $keys;

    private $fields_name = [
        'des' => 'Description',
        'note' => 'Note',
        'status' => 'Status',
        'tags' => 'Tags',
        'name' => 'Name',
        'start' => 'StartDate',
        'end' => 'EndDate',
        'member' => 'Members'

    ];
    private $insert_data = [];
    private $history_query;

    public function __construct(Request $request, $id_task, $id_user)
    {
        $this->id_task = $id_task;
        $this->history_query = WorkTaskHistory::query();
        $this->keys = array_keys($this->value_keys);
        $this->insert_data = [
            "UserID" => $id_user,
            "WorkTaskID" => $id_task,
            "TypeActionID" => null,
            "Content" => null,
            "Old" => null,
            "New" => null,
            "FieldsName" => null
        ];
    }

    public function store($type_action, $fields_name=null)
    {
        switch ($type_action) {
            case $this->keys[0]:
                #action insert history
                $this->insert_data['FieldsName'] = !is_null($fields_name) ? $fields_name : null;
                $this->insert_data['TypeActionID'] =  $this->value_keys[$this->keys[0]];
                break;
            case $this->keys[1]:
                #action update history
                $this->insert_data['TypeActionID'] = $this->value_keys[$this->keys[1]];
                break;
            case $this->keys[2]:
                #action delete history
                $this->insert_data['TypeActionID'] = $this->value_keys[$this->keys[2]];
                break;
            default:
                break;

        }
        $this->history_query->insert($this->insert_data);
    }

    public function display()
    {

    }

}
