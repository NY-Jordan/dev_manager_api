<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Project\CreateProjectRequest;
use App\Http\Resources\Project\GetProjectResource;
use App\Models\Project;
use GuzzleHttp\Psr7\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct()
    {
        
    } 

    public function getUserProjects(Project $project) : GetProjectResource{
        $projects =  $project->getProjectOfUser();
        return GetProjectResource::make($projects);
    }


    public function create(CreateProjectRequest $request, Project $project)
    {
        $project = $project->createNewProject($request->name);
        
    } 


    public function update()
    {
        
    }
    
    
    public function delete($id, Project $project) : JsonResponse
    {
        $project->getProjectOfUser($id)->delete();
        return response()->json(['message' => "project deleted successfully", 'status' => true], 200);
    }


    public function InviteUserOnProject(){

    }



}
