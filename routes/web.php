<?php



use Illuminate\Support\Facades\Route;



use App\Http\Controllers\DashboardController;

use App\Http\Controllers\SalesController;

use App\Http\Controllers\LeadsController;

use App\Http\Controllers\DealsController;

use App\Http\Controllers\LeadsActivityController;

use App\Http\Controllers\DealsActivityController;

use App\Http\Controllers\UserController;

use App\Http\Controllers\ContractController;

use App\Http\Controllers\GroupController;

use App\Http\Controllers\HostingController;

use App\Http\Controllers\ProjectController;

use App\Http\Controllers\ClientController;

use App\Http\Controllers\LeaveManagementController;

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HrController;
use App\Http\Controllers\SeoServiceController;
use App\Http\Controllers\DocuSignController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Mail;



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider and all of them will

| be assigned to the "web" middleware group. Make something great!

|

*/



Auth::routes();

Route::get("/docusign-page",[DealsActivityController::class,'docusignpage']);
Route::post('/send-envelope',[DealsController::class,'sendenvelope']);
Route::post('/create-envelope',[DocuSignController::class,'createEnvelope']);
// New Routes Added
Route::post('/view-sender',[DocuSignController::class,'viewSender']);
Route::get('/envelope-status/{envelopeId}', [DocusignController::class, 'getRecipientStatus']);
Route::get('/docusign/callback', function () {return view('docusign.callback'); 
})->name('docusign.callback');


Route::post("/add-deal-product",[DealsController::class,'addProduct']);

Route::post('/docusign-sent',[DealsActivityController::class,'docusignsent']);

Route::middleware(['auth'])->group(function(){

    Route::get('profile', [UserController::class, 'my_profile'])->name('profile');

    Route::post('profile/update', [UserController::class, 'my_profile_update'])->name('update-profile');
    Route::get('/roles-by-department/{id}', [UserController::class, 'getRolesByDepartment'])->name('roles.by.department');

    Route::get("/get-deal-product/{id}",[DealsController::class,'getProduct']);

    Route::get('/get-docusign-notification', [DealsController::class, 'getNotification'])->name('notification.get');

    Route::post('create-docusign-token',[DealsController::class,'createdocusigntoken']);

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::controller(DashboardController::class)->name('dashboard.')->group(function(){
        Route::get('/active-completed-projects', 'activeCompletedProjects')->name('project-list');
        Route::get('/overdue-tasks', 'overdueTasks')->name('overdue-tasks');
        Route::get('/unassigned-resource', 'unassignedResource')->name('unassigned-resource');
        Route::get('/assigned-task-list', 'assignedTaskList')->name('assigned-task-list');
        Route::get('/resource-allocation', 'resourceAllocation')->name('resource.allocation');
        Route::get('/upcoming-deadline-tasks', 'upcomingDeadlineTasks')->name('upcoming-deadline-tasks');
        Route::get('/employee-productivity-list', 'employeeProductivityList')->name('employee-productivity-list');
        Route::get('/projects-monthly-completed', 'getMonthlyCompletedProjects')->name('projects-monthly-completed');
        Route::get('/tasks-monthly-completed', 'getMonthlyCompletedTasks')->name('tasks.monthly.completed');
        Route::get('/idle-time-report', 'idleTimeReport')->name('idle.time.report');
        Route::get('/billable-non-billable-tasks', 'billableNonBillableTask')->name('billable-non-billable-tasks');
        Route::get('/get-task-time/{taskId}', 'getTaskTime')->name('get.task.time');
        Route::post('/update-task-time/{taskId}', 'updateTaskTime')->name('update.task.time');
    });

    Route::get('/bar-chart', [DashboardController::class, 'bar'])->name('dashboard.bar');

    Route::get('/pie-chart', [DashboardController::class, 'pie'])->name('dashboard.pie');

    Route::get('/line-chart', [DashboardController::class, 'line'])->name('dashboard.line');

    Route::get('/area-chart', [DashboardController::class, 'area'])->name('dashboard.area');

    Route::resource('users', UserController::class, [

        'names' => [

            'index' => 'user.list',

            'create' => 'user.create',

            'store' => 'user.add',

            'edit' => 'user.edit',

            'update' => 'user.update',

            'destroy' => 'user.delete'

        ],

        'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']

    ])->middleware('role:1,3');

    Route::resource('groups', GroupController::class, [

        'names' => [

            'index' => 'group.list',

            'create' => 'group.create',

            'store' => 'group.add',

            'edit' => 'group.edit',

            'update' => 'group.update',

            'destroy' => 'group.delete'

        ],

        'only' => ['index', 'create', 'store', 'edit', 'update', 'destroy']

    ]);

    Route::group(['prefix' => 'sales', 'middleware' => 'role:1,2,3'], function(){

        Route::get('/', [SalesController::class, 'sales_dash'])->name('sales_dashboard');

        Route::group(['prefix' => 'lead/{id}'], function(){

            Route::get('convert', [LeadsController::class, 'convert'])->name('lead.convert');

            Route::resource('activity', LeadsActivityController::class,[

                'names' => [

                    'create' => 'lead.activity.new',

                    'store' => 'lead.activity.add',

                    'edit' => 'lead.activity.edit',

                    'update' => 'lead.activity.update',

                    'destroy' => 'lead.activity.delete',

                ],

                'only' => ['create', 'store', 'edit', 'update', 'destroy']

            ]);

            Route::get('activity/done/{act}', [LeadsActivityController::class, 'mark_as_done'])->name('lead.activity.done');

            Route::get('activity/todo/{act}', [LeadsActivityController::class, 'mark_as_todo'])->name('lead.activity.todo');

            Route::get('update-info', [LeadsController::class, 'update_info'])->name('lead.update-info');

            Route::post('submit-update-info', [LeadsController::class, 'ajax_update_info'])->name('lead.submit-update-info');

            Route::get('convert-lead', [LeadsController::class, 'convert_lead'])->name('lead.convert-lead');

            Route::post('submit-convert-lead', [LeadsController::class, 'ajax_convert_lead'])->name('lead.submit-convert-lead');

        });

        Route::resource('leads', LeadsController::class, [

            'names' => [

                'index' => 'lead.list',

                'create' => 'lead.new',

                'store' => 'lead.add',

                'edit' => 'lead.edit',

                'update' => 'lead.update',

                'destroy' => 'lead.delete',

                'show' => 'lead.show'

            ]

        ]);

        Route::group(['prefix' => 'deal/{id}'], function(){

            Route::get('convert', [DealsController::class, 'convert'])->name('deal.convert');

            Route::resource('activity', DealsActivityController::class,[

                'names' => [

                    'create' => 'deal.activity.new',

                    'store' => 'deal.activity.add',

                    'edit' => 'deal.activity.edit',

                    'update' => 'deal.activity.update',

                    'destroy' => 'deal.activity.delete',

                ],

                'only' => ['create', 'store', 'edit', 'update', 'destroy']

            ]);

            Route::get('activity/done/{act}', [dealsActivityController::class, 'mark_as_done'])->name('deal.activity.done');

            Route::get('/contract',[ContractController::class, 'create'])->name('contract.create');

            Route::post('/contract',[ContractController::class, 'send'])->name('contract.send');

        });

        // In routes/web.php

        Route::put('/deal/{deal}/update-close-date', [DealsController::class, 'updateCloseDate'])->name('deal.updateCloseDate');

        Route::resource('deals', DealsController::class,[

            'names' => [

                'index' => 'deal.list',

                'create' => 'deal.new',

                'store' => 'deal.add',

                'edit' => 'deal.edit',

                'update' => 'deal.update',

                'destroy' => 'deal.delete',

                'show' => 'deal.show'

            ]

        ]);

    });

    Route::post('/deal/update-stage', [DealsController::class, 'updateStage'])->name('deal.update-stage');

    Route::post('deals_transfer_owner',[DealsController::class,'transfer_owner'])->name('deals.transfer_owner');

    // Route::post('leads_transfer_owner', [LeadsController::class, 'transfer_owner'])->name('leads.transfer_owner');

    Route::post('/lead/transfer-owner', [LeadsController::class, 'transfer_owner'])->name('lead.transfer.owner');

    

    Route::group(['prefix' => 'hostingmanagement'], function(){

        Route::get('/', [HostingController::class, 'hostingmanagemet'])->name('hosting_dashboard');

        Route::post('/hosting/activate/{id}', [HostingController::class, 'activate'])->name('hosting.activate');

        Route::resource('hosting', HostingController::class, [

            'names' => [

                'index' => 'hosting.list',

                'create' => 'hosting.new',

                'store' => 'hosting.add',

                'edit' => 'hosting.edit',

                'update' => 'hosting.update',

                'destroy' => 'hosting.delete',

                'show' => 'hosting.show'

            ],

        ]);

    });



    // Project Management

    Route::controller(ProjectController::class)->name('project.')->prefix('project')->group(function(){

        Route::get('/', 'dashboard')->name('dashboard');
        Route::get('/assigned-project-list', 'assignedProjectList')->name('assigned-project-list');
        Route::get('/milestones-list', 'milestonesList')->name('milestones-list');
        Route::get('/member-task-list', 'memberTaskList')->name('member-task-list');
        Route::get('/daily-task-update', 'dailyTaskUpdate')->name('daily-task-update');
        Route::get('/task-deadline-priority', 'taskDeadlinePriority')->name('task-deadline-priority');
        Route::get('/today-task-deadlines', 'todayTaskDeadlines')->name('today-task-deadlines');
        
        Route::get('list', 'list')->name('list');

        Route::get('add', 'add')->name('add');

        Route::post('add', 'store')->name('store');

        Route::get('edit/{id}', 'edit')->name('edit');

        Route::post('edit/{id}', 'update')->name('update');

        Route::delete('delete/{id}', 'destroy')->name('delete');

        Route::get('{id}/detail', 'details')->name('details');

        Route::post('assign/{id}', 'assign')->name('assign');

        Route::post('{id}/remove/assign/{user_id}', 'remove_assign')->name('remove_assign');



        Route::group(['prefix' => '{id}/tasks'], function(){

            Route::get('/', 'task_list')->name('task.list');

            Route::get('add', 'task_create')->name('task.add');

            Route::post('add', 'task_store')->name('task.add.submit');

            Route::get('{task_id}', 'task_detail')->name('task.detail');

            Route::post('delete/{task_id}', 'task_delete')->name('task.delete');

            Route::post('assign/{task_id}', 'task_assign')->name('task.assign');

            Route::post('{task_id}/remove/assign/{user_id}', 'remove_task_assign')->name('task.remove_assign');

            Route::get('{task_id}/status/{status_id}', 'change_task_status')->name('task.status.change');

            Route::post('/{task_id}/add-comment', 'task_comment_store')->name('task.comment.submit');

            Route::get('{task_id}/download/{file_id}', 'download_task_file')->name('task.download');

        });



        Route::group(['prefix' => '{id}/bugs'], function(){

            Route::get('/', 'bug_list')->name('bug.list');

            Route::get('add', 'bug_create')->name('bug.add');

            Route::post('add', 'bug_store')->name('bug.add.submit');

            Route::get('{bug_id}', 'bug_detail')->name('bug.detail');

            Route::get('/{bug_id}/edit', 'bug_edit')->whereNumber('bug_id')->name('bug.edit');

            Route::post('/{bug_id}/update', 'bug_update')->whereNumber('bug_id')->name('bug.update');

            Route::delete('delete/{bug_id}', 'bug_delete')->name('bug.delete');

            Route::post('assign/{bug_id}', 'bug_assign')->name('bug.assign');

            Route::post('{bug_id}/remove/assign/{user_id}', 'remove_bug_assign')->name('bug.remove_assign');

            Route::get('{bug_id}/status/{status_id}', 'change_bug_status')->name('bug.status.change');

            Route::post('/{bug_id}/add-comment', 'bug_comment_store')->name('bug.comment.submit');

            Route::get('{bug_id}/download/{file_id}', 'download_bug_file')->name('bug.download');

        });



        Route::group(['prefix' => '{id}/milestones'], function(){

            Route::get('/', 'milestones_list')->name('milestones.list');

            Route::get('add', 'milestones_create')->name('milestones.add');

            Route::post('add', 'milestones_store')->name('milestones.add.submit');

            Route::get('{milestones_id}', 'milestones_detail')->name('milestones.detail');
            
            Route::get('/{milestone_id}/edit', 'milestone_edit')->whereNumber('milestone_id')->name('milestone.edit');

            Route::post('/{milestone_id}/update', 'milestone_update')->whereNumber('milestone_id')->name('milestone.update');

            Route::delete('delete/{milestones_id}', 'milestones_delete')->name('milestones.delete');

        });



        // ajax calls

        Route::get('get-users', 'get_users')->name('get_users');

        Route::get('get-project-users', 'get_project_users')->name('get_project_users');

        Route::get('bug-project-users', 'bug_project_users')->name('bug_project_users');

    });



    // client management

    Route::controller(ClientController::class)->name('client.')->prefix('client')->group(function(){

        Route::get('/', 'list')->name('list');

        Route::get('add', 'create')->name('add');

        Route::post('add', 'store')->name('store');

        Route::get('edit/{id}', 'edit')->name('edit');

        Route::post('edit/{id}', 'update')->name('update');

        Route::post('delete/{id}', 'destroy')->name('delete');

        Route::get('details/{id}', 'details')->name('details');



        // ajax calls

        Route::get('get-client', 'get_clients')->name('get_clients');

    });



    // leave management

    Route::controller(LeaveManagementController::class)->name('leave.')->prefix('leave')->group(function(){

        Route::get('/', 'list')->name('list');

        Route::get('request', 'leave_request')->name('request');

        Route::post('request/submit', 'submit_request')->name('submit_request');

        Route::post('delete/leave/{id}', 'destroy')->name('delete');

        Route::post('request/approve/{id}', 'approve_request')->name('approve');

        Route::post('request/reject/{id}', 'reject_request')->name('reject');

        Route::post('request/hold/{id}', 'hold_request')->name('hold');

        Route::get('request/edit/{id}', 'edit_request')->name('edit');

        Route::post('request/update/{id}', 'update_request')->name('update');

        // Adding the new routes here

        Route::delete('destroy/{leave}', 'destroy')->name('destroy');

        Route::post('upload-attachment', 'upload_attachment')->name('upload.attachment');

        Route::post('delete-attachment', 'delete_attachment')->name('delete.attachment');

    });

    // resource management
    Route::middleware(['auth'])->group(function () {
        Route::get('/employees', [EmployeeController::class, 'index'])->name('employee.list');
        Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employee.create');
        Route::post('/employees', [EmployeeController::class, 'store'])->name('employee.add');
        Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employee.edit');
        Route::put('/employees/{id}', [EmployeeController::class, 'update'])->name('employee.update');
        Route::patch('/employee/inline-update/{id}', [EmployeeController::class, 'inlineUpdate'])->name('employee.inline.update');
        Route::get('/employees/{id}/profile', [EmployeeController::class, 'show'])->name('employee.profile');
        Route::get('/employees/{id}/orgchart', [EmployeeController::class, 'getOrgChart'])->name('employee.orgchart');
        Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employee.delete');
        Route::get('/projects/task-completion/{empId?}', [EmployeeController::class, 'projectsTaskCompletion'])->name('projects.taskCompletion');
        Route::get('/employee/leave-records', [EmployeeController::class, 'getLeaveRecords'])->name('employee.leaveRecords');
        Route::get('/employee/task-completion-chart', [EmployeeController::class, 'taskCompletionChart'])->name('employee.taskCompletionChart');
        Route::get('/employee/assigned-completed-task', [EmployeeController::class, 'assignedCompletedTasks'])->name('employee.assignedCompletedTasks');
        Route::get('/employee/{empId}/incentives', [EmployeeController::class, 'getIncentives'])->name('employee.incentives');
        Route::get('/projects/monthly-completed', [EmployeeController::class, 'getMonthlyCompletedProjects'])->name('employee.projects.monthly.completed');
        Route::get('/tasks/monthly-completed', [EmployeeController::class, 'getMonthlyCompletedTasks'])->name('employee.tasks.monthly.completed');
        Route::get('/get-reporting-persons/{department_id}', [EmployeeController::class, 'getReportingPersons']);
    });

    Route::controller(HrController::class)->name('hr.')->prefix('hr')->group(function(){
        Route::get('/', 'index')->name('dashboard');
        Route::get('/employees', 'getEmployees')->name('employees');
        Route::get('/employee-availability', 'employeeAvailability')->name('employee-availability');
        Route::get('/resource-utilization', 'resourceUtilization')->name('resource-utilization');
        Route::get('/leave-statistics', 'getLeaveStatistics')->name('leave-statistics');
        Route::get('/monthly-leaves', 'monthlyLeavesChart')->name('monthly-leaves');
        Route::get('/attendance/ajax', 'attendanceAjax')->name('attendance.ajax');
        Route::get('/employee/attendance-calendar-events', 'getCalendarEvents')->name('calendar.events');
        Route::get('/employee/attendance-calendar-grid', 'attendanceCalendarGrid');
        Route::get('/attendance/fetch', 'fetchAttendance')->name('attendance.fetch');

        $singleRoutes = [ 'configuration', 'attendance', 'recruitment', 'employee-management', 'payroll-management', 'exit-process', 'holiday-management', 'employee-history', 'employee-feedback' ];
        $index = [ 'job-posting', 'interview', 'leave-balance', 'salary-management', 'performance-appraisal', 'bank-statement', 'leave-type', 'applicant', 'offer-letter', 'resignation', 'asset-handover', ];
        $create = [ 'job-posting', 'interview', 'salary-management', 'applicant', 'employee-history', 'employee-feedback', 'holiday-management', 'performance-appraisal', 'bank-statement', 'leave-type', 'applicant', 'offer-letter', 'resignation', 'asset-handover', ];
        $store = [ 'job-posting', 'interview', 'salary-management', 'applicant', 'employee-history', 'employee-feedback', 'holiday-management', 'performance-appraisal', 'bank-statement', 'leave-type', 'applicant', 'offer-letter', 'resignation', 'asset-handover', ];
        $show = [];
        $edit = [ 'job-posting', 'interview', 'holiday-management', 'bank-statement', 'offer-letter', 'leave-type', ];
        $update = [ 'job-posting', 'interview', 'holiday-management', 'bank-statement', 'offer-letter', 'leave-type', ];
        $updateStatus = [ 'applicant', 'offer-letter', 'resignation', 'asset-handover', ];
        $delete = [ 'job-posting', 'interview', 'holiday-management', 'leave-type', 'applicant', 'offer-letter', ];
        
        foreach ($singleRoutes as $value) { 
            Route::get("$value", method($value))->name(str_replace('-', '.', $value)); 
        }
        foreach ($index as $value) { 
            Route::get("$value/index", method($value)."Index")->name(str_replace('-', '.', $value).".index"); 
        }
        foreach ($create as $value) { 
            Route::get("$value/create", method($value)."Create")->name(str_replace('-', '.', $value).".create"); 
        }
        foreach ($store as $value) { 
            Route::post("$value/store", method($value)."Store")->name(str_replace('-', '.', $value).".store"); 
        }
        foreach ($edit as $value) { 
            Route::get("$value/edit/{id}", method($value)."Edit")->name(str_replace('-', '.', $value).".edit"); 
        }
        foreach ($update as $value) { 
            Route::put("$value/update/{id}", method($value)."Update")->name(str_replace('-', '.', $value).".update"); 
        }
        foreach ($updateStatus as $value) { 
            Route::patch("$value/update/{id}/status", method($value)."UpdateStatus")->name(str_replace('-', '.', $value).".update.status"); 
        }
        foreach ($delete as $value) { 
            Route::delete("$value/delete/{id}", method($value)."Delete")->name(str_replace('-', '.', $value).".delete"); 
        }
    }); // hr block closed
    
    // SEO Management
    Route::middleware(['auth'])->group(function () {
        Route::get('/seo/dashboard', [SeoServiceController::class, 'index'])->name('seo.dashboard');
        Route::get('/seo', [SeoServiceController::class, 'getSeoList'])->name('seo.list');
        Route::get('/seo/create', [SeoServiceController::class, 'create'])->name('seo.create');
        Route::post('/seo', [SeoServiceController::class, 'store'])->name('seo.add');
        Route::get('/seo/{id}/edit', [SeoServiceController::class, 'edit'])->name('seo.edit');
        Route::put('/seo/{id}', [SeoServiceController::class, 'update'])->name('seo.update');
        Route::post('/seo/reactivate/{id}', [SeoServiceController::class, 'reactivate'])->name('seo.reactivate');
        Route::get('/seo/upcoming-renewals', [SeoServiceController::class, 'upcomingRenewals'])->name('seo.upcoming-renewals');
        Route::post('/seo/renew-plan/{id}', [SeoServiceController::class, 'renewPlan'])->name('seo.renew');
        Route::delete('/seo/{id}', [SeoServiceController::class, 'destroy'])->name('seo.delete');
    });

    Route::get('/clear-cache', function () {

        Artisan::call('cache:clear');

        Artisan::call('config:clear');

        Artisan::call('route:clear');

        Artisan::call('view:clear');
        Artisan::call('config:cache');

        return 'Cache cleared!';

    });

    Route::get('/migrate', function () {
        Artisan::call('migrate');
        return 'Migration completed!';
    });

    Route::get('/seed/{table}/{seeder}', function ($table, $seeder) {
        $allowedSeeders = ['EmployeeBreakSeeder', 'UserSeeder',];

        if (!in_array($seeder, $allowedSeeders)) {
            abort(404, 'Seeder not found or not allowed.');
        }

        if (Schema::hasTable($table)) {
            DB::table($table)->truncate();
        } else {
            return "Table '$table' does not exist.";
        }

        Artisan::call('db:seed', ['--class' => $seeder]);
        return "$seeder executed successfully!";
    });
    

});