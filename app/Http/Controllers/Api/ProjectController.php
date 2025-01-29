<?php

namespace App\Http\Controllers\Api;

use App\Enums\ProjectInvitation\InvitationStatusEnums;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\CreateProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Requests\Task\assignTaskRequest;
use App\Http\Resources\CollaboratorsResource;
use App\Http\Resources\InvitationResource;
use App\Http\Resources\Project\FecthProjectsResource;
use App\Http\Resources\Project\InviteUserOnProjectRessource;
use App\Http\Resources\Project\ProjectInvitationRessource;
use App\Http\Resources\Project\ProjectRessource;
use App\Jobs\SendEmailToUserGuestInProject;
use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectInvitaion;
use App\Models\ProjectUser;
use App\Models\User;
use App\Notifications\ProjectInvitationAcceptedNotification;
use App\Notifications\ProjectInvitationConfirmationNot;
use App\Notifications\ProjectInvitationNotification;
use App\Notifications\ProjectInvitationRefusedNotification;
use App\Service\ProjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function __construct(private ProjectService $projectService ){}

    function getInvitation($uuid) {
        $invitation = ProjectInvitaion::check_if_exist($uuid);
        abort_if(!$invitation,404,'Invitation not found');
        return ProjectInvitationRessource::make($invitation);

    }

    function getCollaborators($projectId) {
        $project = Project::findOrFail($projectId);
        $collaborators = $project->getCollaborators();
        return CollaboratorsResource::collection($collaborators);

    }

    public function getUserProjects(Project $project) : FecthProjectsResource{

        $projects = $this->projectService->fechProjectsUser();
        return FecthProjectsResource::make($projects);
    }

    public function details($projectId) : ProjectRessource{

        $project = Project::findOrFail($projectId);
        return ProjectRessource::make($project);
    }


    public function create(CreateProjectRequest $request, Project $project)
    {
        $logo = $request->file('logo');
        $project = $project->createNewProject($request->name, $logo);
        ProjectUser::create(['project_id' => $project->id, 'user_id' => auth()->id()]);
        return response()->json(['message' => "project created successfully", 'status' => true], 200);
    }


    public function update(UpdateProjectRequest $request, $id){
        $project = Project::findOrFail($id);
        if ($request->user()->cannot('update', $project)) {
            abort(403);
        }
        $project->setName($request->name);
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }


    public function delete($id,  Request $request) : JsonResponse
    {
        $project = Project::findOrFail($id);
        if ($request->user()->cannot('update', $project)) {
            abort(403);
        }
        $deleteNotificationAndInvitation = ProjectService::deleteProjectInvitationAndNotification($project);
        abort_if(!$deleteNotificationAndInvitation, 400, 'error when delete notification and invitations');
        $project->getProjectOfUser($id)->first()->delete();
        return response()->json(['message' => "project deleted successfully", 'status' => true], 200);
    }

    public function removeUser( Request $request, $userId, $projectId, $invitationId) : JsonResponse
    {
        $projectUser = ProjectUser::whereUserId($userId)->whereProjectId($projectId)->first();
        $projectInvitation = ProjectInvitaion::find($invitationId);
        abort_if(!$projectUser, 400, 'user not found');
        $projectUser->delete();
        $projectInvitation->setStatus(InvitationStatusEnums::STATUS_CANCELED);
        return response()->json(['message' => "user removed successfully", 'status' => true], 200);
    }


    public function InviteUserOnProject(Request $request,int $user_id, int $project_id){
        $receiver = User::find($user_id);
        $sender = User::find(Auth::id());
        Project::findOrFail($project_id);
        $invitation = ProjectInvitaion::check_if_user_is_invited($project_id, $user_id);
        if ($invitation) {
            return response()->json(['message' => 'Invitation already sent', 'status' => true], 412);
        }
        $invitation = (new ProjectInvitaion )->newInvitation($user_id, $project_id);
        dispatch(new SendEmailToUserGuestInProject($receiver, $invitation))->afterResponse();
        $sender->notify(new ProjectInvitationNotification($sender, $invitation->uuid));
        $receiver->notify(new ProjectInvitationConfirmationNot($receiver, $invitation->uuid));
        return InviteUserOnProjectRessource::make($invitation);
    }


    public function acceptInvitation($uuid, ProjectUser $projectUser) : JsonResponse{
        $invitation = ProjectInvitaion::check_if_exist($uuid);
        abort_if(!$invitation,404,'Invitation not found');
        abort_if(InvitationStatusEnums::statusIsCanceled($invitation->status),400,'The invitation canceled');
        $invitation->setStatus(InvitationStatusEnums::STATUS_ACCEPTED);
        $invitation->refresh();
        $projectUser->addNewUserToProject($invitation);
        $sender = User::findOrFail($invitation->sender);
        $sender->notify(new ProjectInvitationAcceptedNotification($sender,$invitation));
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }


    public function rejectInvitation($uuid) : JsonResponse {
        $invitation = ProjectInvitaion::check_if_exist($uuid);
        abort_if(!$invitation,404,'Invitation not found');
        abort_if(InvitationStatusEnums::statusIsCanceled($invitation->status),400,'The invitation canceled');
        $invitation->setStatus(InvitationStatusEnums::STATUS_REFUSED);
        $sender = User::findOrFail($invitation->sender);
        $sender->notify(new ProjectInvitationRefusedNotification($sender,$invitation));
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }


    public function cancelInvitation($uuid) : JsonResponse {
        $invitation = ProjectInvitaion::check_if_exist($uuid);
        abort_if(!$invitation,404,'Invitation not found');
        abort_if((InvitationStatusEnums::statusIsAccepted($invitation->status) or InvitationStatusEnums::statusIsRefused($invitation->status)),400,'Impossible Operation');
        Notification::where('notifiable_contentt_id', $invitation->uuid)->delete();
        $invitation->delete();
        return response()->json(['message' => "operation successfully", 'status' => true], 200);
    }


    public function searchUser(Request $request, $projectId) {
        $project = Project::findOrFail($projectId);
        abort_if(!$project->isTheAdministrator(Auth::id(), $project->id), 403, 'You are not authorized');
        if ($request->search) {
           $users = User::search($request->search);
        } else {
            $users = User::all()->except(Auth::id());
        }
        return response()->json(['users' => $users, 'status' => true], 200);
    }

    public function invitations(Request $request, $projectId)  {

        $invitations = Project::findOrFail($projectId)->getInvitations();
        return InvitationResource::collection($invitations);
    }



}
