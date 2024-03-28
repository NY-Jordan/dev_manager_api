<?php

namespace App\Http\Controllers\Api;

use App\Events\UserGuestInProject;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\CreateProjectRequest;
use App\Http\Resources\Project\GetProjectResource;
use App\Http\Resources\Project\InviteUserOnProjectRessource;
use App\Jobs\SendEmailToUserGuestInProject;
use App\Models\Project;
use App\Models\ProjectInvitaion;
use App\Models\User;
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
        return GetProjectResource::make( $projects);
    }


    public function create(CreateProjectRequest $request, Project $project)
    {
        $project = $project->createNewProject($request->name);
        return response()->json(['message' => "project created successfully", 'status' => true], 200);       
    } 


    public function update()
    {
        
    }
    
    
    public function delete($id, Project $project) : JsonResponse
    {
        $project->getProjectOfUser($id)->delete();
        return response()->json(['message' => "project deleted successfully", 'status' => true], 200);
    }


    public function InviteUserOnProject(Request $request,int $user_id, int $project_id){
        
        $user = User::find($user_id);
        $invitation = (new ProjectInvitaion )->newInvitation($user_id, $project_id);
        dispatch(new SendEmailToUserGuestInProject($user, $invitation))->afterResponse();
        return InviteUserOnProjectRessource::make($invitation);
    }



}
