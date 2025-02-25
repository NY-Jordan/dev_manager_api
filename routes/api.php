<?php

use App\Http\Controllers\Api\AppController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\GithubAuth2Controller;
use App\Http\Controllers\Api\Auth\GoogleAuth2Controller;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskGroupController;
use App\Http\Controllers\Api\DailyTaskController;
use App\Http\Controllers\Api\TicketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
Route::group(['prefix' => 'auth2'], function ()  {
    Route::get('/google', [GoogleAuth2Controller::class, 'redirectToAuth']);
    Route::get('/google/callback', [GoogleAuth2Controller::class, 'handleAuthCallback']);

    Route::get('/github', [GithubAuth2Controller::class, 'redirectToAuth']);
    Route::get('/github/callback', [GithubAuth2Controller::class, 'handleAuthCallback']);
});


Route::group(['prefix' => 'auth'], function () {

    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('email/verification', [AuthController::class, 'emailVerification'])
    ->middleware(['auth:sanctum', 'ability:limited'])->name('email.verification');
    Route::post('password/update', [ForgotPasswordController::class, 'updatePassword'])
    ->middleware(['auth:sanctum', 'ability:password_reset'])
    ->name('password.forgot');
    Route::post('password/reset', [ForgotPasswordController::class, 'forgotPassword'])->name('password.reset');
});

Route::group(['middleware' => 'auth:sanctum', 'ability:*'], function() {
  Route::get('logout', [AuthController::class, 'logout']);
  Route::get('user/{id}', [AuthController::class, 'user']);
  Route::get('notifications', [NotificationController::class, 'show']);

  Route::get('/stats', [AppController::class, 'fetchStats']);
  Route::get('/collaborators', [AppController::class, 'fetchCollaborators']);
  Route::get('/activities', [AppController::class, 'fetchActivities']);

  // project root
  Route::group(['prefix' => 'project'], function () {
    Route::get('user', [ProjectController::class, 'getUserProjects'])->name('project.user');
    Route::get('details/{projectId}', [ProjectController::class, 'details'])->name('project.details');
    Route::post('create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('update/{id}', [ProjectController::class, 'update'])->name('project.update');
    Route::delete('delete/{projectId}', [ProjectController::class, 'delete'])->name('project.delete');
    Route::get('search/{projectId}', [ProjectController::class, 'searchUser'])->name('project.searchUser');
    Route::get('invitations/{projectId}', [ProjectController::class, 'invitations'])->name('project.invitations');
    Route::get('collaborators/{projectId}', [ProjectController::class, 'getCollaborators'])->name('project.collaborators');


    // project invitation
    Route::group(['prefix' => 'invite'], function () {
        Route::post('{userId}/user/{projectId}', [ProjectController::class, 'InviteUserOnProject'])->name('project.inviteUser')
        ->whereNumber('userId')
        ->whereNumber('projectId');
      Route::post('user/accept/{uuid}', [ProjectController::class, 'acceptInvitation'])->name('project.acceptInvitation');
      Route::post('user/reject/{uuid}', [ProjectController::class, 'rejectInvitation'])->name('project.rejectInvitation');
      Route::post('user/cancel/{uuid}', [ProjectController::class, 'cancelInvitation'])->name('project.cancelInvitation');
      Route::get('{uuid}', [ProjectController::class, 'getInvitation'])->name('project.getInvitation');
    });


    Route::post('user/remove/{userId}/{projectId}/{invitationId}', [ProjectController::class, 'removeUser'])->name('project.removeUser');


    // task group
    Route::group(['prefix' => 'taskgroup'], function () {
      Route::get('/{projectId}', [TaskGroupController::class, 'getByProject'])->name('project.getTaskgroup');
      Route::post('create', [TaskGroupController::class, 'create'])->name('project.getTaskgroup');
        Route::post('update/name/{taskGroupId}', [TaskGroupController::class, 'updateName'])->name('taskgroup.updateName');
        Route::post('update/status/{taskGroupId}', [TaskGroupController::class, 'updateStatus'])->name('taskgroup.updateStatus');
        Route::delete('delete/{taskGroupId}', [TaskGroupController::class, 'delete'])->name('taskgroup.delete');
        Route::post('attach/user', [TaskGroupController::class, 'attachUserToTaskGroup'])->name('taskgroup.attachUser');
        Route::post('detach/user', [TaskGroupController::class, 'detachUserFromTaskGroup'])->name('taskgroup.detachUser');
    });


    // tasks
    Route::group(['prefix' => 'tasks'], function () {
      Route::get('fetch/{projectId}', action: [TaskController::class, 'fetchTasks'])->name('task.fetch');
      Route::get('fetch/{projectId}/{userId}', action: [TaskController::class, 'fetchCollaboratorTasks'])->name('task.user.fetch');
      Route::post('reschedule/{projectId}/{taskId}', action: [TaskController::class, 'rescheduleUsersTask'])->name('task.user.reschedule');
      Route::post('create', [TaskController::class, 'create'])->name('task.create');
      Route::post('update/{taskId}', [TaskController::class, 'update'])->name('task.update');
      Route::delete('delete/{taskId}', [TaskController::class, 'delete'])->name('task.delete');
      Route::post('assign/{projectId}', [TaskController::class, 'assignTask'])->name('project.assignTask');
      Route::post('update/phase/{taskId}', [TaskController::class, 'updatePhase'])->name('project.updatePhase');

      Route::group(['prefix' => 'file'], function () {
        Route::post('create', [TaskController::class, 'attatchFileToTask'])->name('taskFile.create');
        Route::post('update/{id}', [TaskController::class, 'updateFileTask'])->name('taskFile.update');
      });

      Route::group(['prefix' => 'ticket'], function () {
        Route::post('create', [TicketController::class, 'create'])->name('task.ticket.create');
        Route::get('fetch/{taskId}', [TicketController::class, 'fetch'])->name('task.ticket.fetch');
        Route::post('status/update/{taskId}', [TicketController::class, 'changeStatus'])->name('task.ticket.status');
        Route::delete('delete/{taskId}', [TicketController::class, 'delete'])->name('task.ticket.delete');
      });

      Route::group(['prefix' => 'daily'], function () {
        Route::post('create', [DailyTaskController::class, 'create'])->name('task.daily.create');
        Route::post('update/{id}', [DailyTaskController::class, 'update'])->name('task.daily.update');
        Route::post('update/phase/{id}', [DailyTaskController::class, 'updatePhase'])->name('task.daily.updatePhase');
        Route::post('reschedule/{taskId}', [DailyTaskController::class, 'rescheduleDailyTask'])->name('task.daily.rescheduleDailyTask');
        Route::get('/', action: [DailyTaskController::class, 'fetch'])->name('task.daily.fecth');
      });

      Route::get('{id}/file', [TaskController::class, 'getAllFilesTask'])->name('taskFile.getAll');

      Route::group(['prefix' => 'tag'], function () {
        Route::post('create', [TaskController::class, 'attatchFileToTask'])->name('taskFile.create');
        Route::post('update/{id}', [TaskController::class, 'updateFileTask'])->name('taskFile.update');
      });
    });

  });

});
