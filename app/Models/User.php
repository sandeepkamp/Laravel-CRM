<?php



namespace App\Models;



// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;

use Laravel\Sanctum\HasApiTokens;

use App\Models\Sales\LeadsModel;

use App\Models\Sales\DealsModel;

use App\Models\ReportingPerson;

class User extends Authenticatable

{

    use HasApiTokens, HasFactory, Notifiable;



    /**

     * The attributes that are mass assignable.

     *

     * @var array<int, string>

     */

    protected $table = 'users';

    

    protected $fillable = [

        'first_name',

        'last_name',

        'date_registered',

        'email',

        'emp_id',

        'password',

        'status',

        'department_id',
        'role',

    ];



    /**

     * The attributes that should be hidden for serialization.

     *

     * @var array<int, string>

     */

    protected $hidden = [

        'password',

        'remember_token',

    ];



    /**

     * The attributes that should be cast.

     *

     * @var array<string, string>

     */

    protected $casts = [

        'email_verified_at' => 'datetime',

        'password' => 'hashed',

    ];

    protected $with = ['role_details', 'reportingPerson', 'subordinates'];


    // protected $with = ['role_details'];

    protected $appends = ['full_name', 'analytics_data'];



    public function role_details()

    {

        return $this->hasOne(UserRoleModel::class, 'id', 'role');

    }



    public function getFullNameAttribute()

    {

        return $this->first_name.' '.$this->last_name;

    }



    public function getAnalyticsDataAttribute()

    {

        $leads = LeadsModel::where('owner', $this->id)->where('converted', 0)->get();

        $deals = DealsModel::where('owner', $this->id)->where('converted', 0)->get();

        return $this->attributes['analytics'] = [

            'leads' => $leads ? $leads->count() : 0,

            'deals' => $deals ? $deals->count() : 0,

            'projects' => 0

        ];

    }



    public function children()

    {

        return $this->hasMany(User::class, 'parent_id');

    }



    public function parent()

    {

        return $this->belongsTo(User::class, 'parent_id');

    }



    public function all_childs()

    {

        $groups = Group::where('owner_user_id', $this->id)->get();

        $child_users = [];

        foreach ($groups as $group) {

            $user = GroupUsers::where('group_id', $group->id)->pluck('user_id')->toArray();

            $child_users = array_merge($child_users, $user);

        }



       return User::whereIn('id', $child_users)->get();

    }



    public function all_parents()

    {

        $rec = GroupUsers::where('user_id',$this->id)->first();

        // dd($group_id);



        $group = Group::find($rec->group_id);

        // dd($group);

        return User::where('id',$group->owner_user_id)->get();

    }

    

    public function all_user(){



        return User::all();

    }

    

    public function meta()

    {

        return $this->hasMany(UserMeta::class, 'user_id');

    }


    public function projectAssigned()
    {
        return $this->hasMany(Projects\ProjectAssignModel::class, 'user_id');
    }

    public function projectTaskAssignModel() 
    {
        return $this->hasMany(Projects\ProjectTaskAssignModel::class, 'user_id', 'id');
    }

    public function assignedTasks() 
    {
        return $this->belongsToMany(Projects\ProjectTasksModel::class, 'task_assigns', 'user_id', 'task_id');
    }
    public function projectModel()
    {
        return $this->belongsToMany(Projects\ProjectModel::class, 'project_assigns', 'user_id', 'project_id');
    }
    public function leaveRequests()
    {
        return $this->hasMany(LeaveManagementModel::class, 'user_id');
    }
    public function projects()
    {
        return $this->belongsToMany(Projects\ProjectModel::class, 'project_assigns', 'user_id', 'project_id');
    }
    public function userMeta()
    {
        return $this->hasMany(UserMeta::class, 'user_id');
    }

    public function reportingPerson()
    
    {
        return $this->hasOne(ReportingPerson::class, 'emp_id', 'id');
    }
    
    public function subordinates()
    
    {
        return $this->hasMany(ReportingPerson::class, 'reporting_person_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function employeeDocuments()
    {
        return $this->hasMany(EmployeeDocument::class, 'employee_id', 'id');
    }

    public function leaveBalances()
    {
        return $this->hasMany(LeaveBalance::class, 'employee_id', 'id');
    }

    public function employeeBreaks()
    {
        return $this->hasMany(EmployeeBreak::class, 'employee_id');
    }
}

