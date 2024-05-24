<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectInvitation\InvitationEntityEnums;
use App\Enums\ProjectInvitation\InvitationStatusEnums;
use App\Enums\StatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\CreateProjectRequest;
use App\Http\Resources\Project\GetProjectResource;
use App\Http\Resources\Project\InviteUserOnProjectRessource;
use App\Jobs\SendEmailToUserGuestInProject;
use App\Models\Project;
use App\Models\ProjectInvitaion;
use App\Models\ProjectUser;
use App\Models\User;
use App\Notifications\ProjectInvitationNotification;
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
        $sender = User::find(Auth::id());
        $ifalreadyExist = ProjectInvitaion::whereReceiver($user_id)
        ->whereProjectId($project_id)
        ->where('status',StatusEnum::STATUS_ACTIVE)
        ->first();
        if ($ifalreadyExist) {
            return response()->json(['message' => 'Invitation already sent', 'status' => true], 412);
        }
        $invitation = (new ProjectInvitaion )->newInvitation($user_id, $project_id);
        dispatch(new SendEmailToUserGuestInProject($user, $invitation))->afterResponse();
        
        $sender->notify(new ProjectInvitationNotification($sender));
        return InviteUserOnProjectRessource::make($invitation);
    }


    public function acceptInvitation($uuid) : JsonResponse{
        $invitation = ProjectInvitaion::check_if_exist($uuid);
        abort_if(!$invitation,404,'Invitation not found');
        abort_if(InvitationStatusEnums::statusIsCanceled($invitation->status),400,'The invitation canceled');
        $invitation->setStatus(InvitationStatusEnums::STATUS_ACCEPTED);
        (new ProjectUser)->addNewUserToProject($invitation);
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }

    
    public function rejectInvitation($uuid) : JsonResponse {
        $invitation = ProjectInvitaion::check_if_exist($uuid);
        abort_if($invitation,404,'Invitation not found');
        abort_if(InvitationStatusEnums::statusIsCanceled($invitation->status),400,'The invitation canceled');
        $invitation->setStatus(InvitationStatusEnums::STATUS_REFUSED);
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }


    public function cancelInvitation($uuid) : JsonResponse {
        $invitation = ProjectInvitaion::check_if_exist($uuid, null, InvitationEntityEnums::TYPE_SENDER->value);
        abort_if(!$invitation,404,'Invitation not found');
        abort_if((InvitationStatusEnums::statusIsAccepted($invitation->status) or InvitationStatusEnums::statusIsRefused($invitation->status)),400,'Impossible Operation');
        $invitation->setStatus(InvitationStatusEnums::STATUS_CANCELED);
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }


    public function searchUser(Request $request, $projectId) {
        $project = Project::findOrFail($projectId);
        abort_if(!$project->isTheAdministrator(Auth::id(), $project->id), 403, 'You are not authorized');
        if ($request->search) {
           $users = User::where('name', 'like','%'.$request->search.'%')
           ->orWhere('email', 'like','%'.$request->search.'%')
           ->get()
           ->except(Auth::id());
        } else {
            $users = User::all()->except(Auth::id());
        }
        return response()->json(['users' => $users, 'status' => true], 200);

    }

}
