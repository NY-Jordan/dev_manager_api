<?php


namespace App\Service;

use App\Models\Notification;
use App\Models\Project;
use App\Models\ProjectInvitaion;
use Illuminate\Support\Facades\DB;

class ProjectService {

    public static function deleteProjectInvitationAndNotification(Project $project) : bool {
        try {
            DB::beginTransaction();
            $invitations =   ProjectInvitaion::where('project_id', $project->id)->get();
            foreach ($invitations as $key => $invitation) {
                Notification::where('notifiable_contentt_id', $invitation->uuid)->delete();
                $invitation->delete();
            }
            Notification::where('notifiable_contentt_id', $project->id)->delete();
            DB::commit();
            return true;
        } catch (\Throwable $th) {
            DB::commit();
            return false;
        }
     
    }

   
}