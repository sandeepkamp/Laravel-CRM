<?php
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Client\LoginController;
use App\Http\Controllers\Client\ProjectsController;
use App\Http\Controllers\Client\DashboardController;
use App\Http\Controllers\Client\Auth\ForgotPasswordController;
use App\Http\Controllers\Client\Auth\ResetPasswordController;

// Client Authentication Routes
Route::redirect('/clients', '/clients/login')->name('clients.redirect');
Route::group(['prefix' => 'clients', 'as' => 'clients.'], function(){
    Route::controller(LoginController::class)->group(function(){
        Route::get('login', 'showLoginForm')->name('login');
        Route::post('login', 'login')->name('login-post');
        Route::post('logout', 'logout')->name('logout'); 
        // Route::get('password-request', 'passwordRequest')->name('password.request'); 
        // Route::post('password-email', 'passwordEmail')->name('password.email'); 
    });

    Route::controller(ForgotPasswordController::class)->group(function(){
        Route::get('password/reset', 'showLinkRequestForm')->name('password.request');
        Route::post('password/email', 'sendResetLinkEmail')->name('password.email');
    });

    Route::controller(ResetPasswordController::class)->group(function(){
        Route::get('password/reset/{token}', 'showResetForm')->name('password.reset');
        Route::post('password/reset', 'reset')->name('password.update');
    });
    
    Route::middleware('clients')->group(function(){
        Route::controller(DashboardController::class)->group(function(){
            Route::get('/dashboard', 'dashboard')->name('dashboard');
            Route::get('/profile', 'profile')->name('profile');
            Route::post('/update/profile', 'updateProfile')->name('update-profile');
            Route::get('/project-evaluation', 'projectEvaluation')->name('project-evaluation');
            Route::get('/ongoing-projects', 'ongoingProjects')->name('ongoing-projects');
        });
        
        Route::controller(ProjectsController::class)->group(function(){
            // project routes
            Route::get('/projects', 'index')->name('projects');
            Route::get('/projects-list', 'projectsList')->name('projects-list');
            Route::get('/project/{id}/detail', 'projectDetails')->name('projects-details');
            // task routes
            Route::get('/project/{id}/tasks', 'projectsTaskList')->name('projects-task-list');
            Route::post('/feedback-submission', 'feedbackSubmission')->name('feedback-submission');
            Route::get('/project/{id}/tasks/{task_id}', 'projectTaskDetail')->name('project-task-detail');
            Route::get('/project/{id}/tasks/{task_id}/download/{file_id}', 'downloadTaskFile')->name('task-file-download');
            // milestone routes
            Route::get('/project/{id}/milestone', 'projectMilestoneList')->name('project-milestone-list');
            Route::get('/project/{id}/milestone/{milestone_id}', 'projectMilestoneDetail')->name('project-milestone-detail');
            // project bugs
            Route::get('/project/{id}/bugs', 'projectBugList')->name('project-bug-list');
            Route::get('/project/{id}/bug/{bug_id}', 'projectBugDetail')->name('project-bug-detail');
        });
    });
});
