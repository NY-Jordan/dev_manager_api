<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\ProjectController;
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
  Route::get('user', [AuthController::class, 'user']);
  // project root
  Route::group(['prefix' => 'project'], function () {
    Route::get('user/get', [ProjectController::class, 'getUserProjects'])->name('project.user');
    Route::post('create', [ProjectController::class, 'create'])->name('project.create');
    Route::post('update', [ProjectController::class, 'update'])->name('project.update');
    Route::post('delete', [ProjectController::class, 'delete'])->name('project.delete');
    Route::post('invite/{userId}/user/{projectId}', [ProjectController::class, 'InviteUserOnProject'])->name('project.inviteUser')
      ->whereNumber('userId')
      ->whereNumber('projectId');
    Route::post('invite/user/accept/{uuid}', [ProjectController::class, 'acceptInvitation'])->name('project.acceptInvitation');
    Route::post('invite/user/reject/{uuid}', [ProjectController::class, 'rejectInvitation'])->name('project.rejectInvitation');
    Route::post('invite/user/cancel/{uuid}', [ProjectController::class, 'cancelInvitation'])->name('project.cancelInvitation');

  });

  Route::group(['prefix' => 'task'], function () {
  });
});
