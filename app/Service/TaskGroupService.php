<?php

namespace App\Service;

use App\Models\UserTaskGroup;

class TaskGroupService {

    public function attachUserFromTaskGroup($task_group_id, $user_id) {

        $already_exist = UserTaskGroup::where("task_group_id", $task_group_id)->where("user_id", $user_id)->exists();
        if ($already_exist) {
            return false;
        }
        $userTaskGroup = UserTaskGroup::create([
            "user_id"=> $user_id,
            "task_group_id"=> $task_group_id
        ]);

        return $userTaskGroup;
    }

    public function detachUserFromTaskGroup($task_group_id, $user_id) {
        $user_group_task = UserTaskGroup::where("task_group_id", $task_group_id)->where("user_id", $user_id)->delete();
        return $user_group_task;
    }
}