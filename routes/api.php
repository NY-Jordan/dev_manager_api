<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskGroupController;
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

  // project root
  Route::group(['prefix' => 'project'], function () {
    Route::get('user', [ProjectController::class, 'getUserProjects'])->name('project.user');
    Route::post('create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('update/{id}', [ProjectController::class, 'update'])->name('project.update');
    Route::post('delete/{projectId}', [ProjectController::class, 'delete'])->name('project.delete');
    Route::get('search/{projectId}', [ProjectController::class, 'searchUser'])->name('project.searchUser');
    

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


    // task group
    Route::group(['prefix' => 'task'], function () {
      Route::post('create', [TaskController::class, 'create'])->name('task.create');
      Route::post('update/{taskId}', [TaskController::class, 'update'])->name('task.update');
      Route::delete('delete/{taskId}', [TaskController::class, 'delete'])->name('task.delete');

      Route::group(['prefix' => 'file'], function () {
        Route::post('create', [TaskController::class, 'attatchFileToTask'])->name('taskFile.create');
        Route::post('update/{id}', [TaskController::class, 'updateFileTask'])->name('taskFile.update');
      });

      Route::get('{id}/file', [TaskController::class, 'getAllFilesTask'])->name('taskFile.getAll');

      Route::group(['prefix' => 'tag'], function () {
        Route::post('create', [TaskController::class, 'attatchFileToTask'])->name('taskFile.create');
        Route::post('update/{id}', [TaskController::class, 'updateFileTask'])->name('taskFile.update');
      });
    });

  });

});
