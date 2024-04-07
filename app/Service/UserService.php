<?php

namespace App\Service;

use App\Models\Project;
use App\Models\ProjectUser;
use App\Models\UserTaskGroup;

class UserService {

    public function isAdministratorOrCollaboratorOfTheProject($project_id, $user_id) : bool {

        $project = Project::findOrFail($project_id);
        $isACollaborator = ProjectUser::isACollaborator($project_id, $user_id);
        if ($isACollaborator or $project->isTheAdministrator($user_id)){
            return true;
        }
        return false;
    }
}