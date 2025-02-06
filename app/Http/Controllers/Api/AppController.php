<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CollaboratorsResource;
use App\Models\Project;
use App\Models\Task;
use App\Service\AppService;

class AppController extends Controller
{

    public function  __construct (private AppService $appService) {

    }

    function fetchActivities()  {

        return $this->appService->getUserTasksTracking();
    }

    function fetchStats()  {

        $statistics = $this->appService->getStatistics();

        return response()->json([
            'projects' => $statistics['projects'],
            'tasks' => $statistics['tasks'],
            'links' => $statistics['links'],
            'notes' => $statistics['notes'],
        ], 200);
    }

    function fetchCollaborators()  {

        $collaborators = $this->appService->getUserCollaborators();

        return response()->json([
            'collaborators' => CollaboratorsResource::collection($collaborators)
        ], 200);
    }

}
