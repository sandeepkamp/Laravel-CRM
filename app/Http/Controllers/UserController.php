<?php

namespace App\Http\Controllers;
use App\Mail\UserCreatedMail;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Models\UserRoleModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\Sales\LeadsModel;
use App\Models\Sales\DealsModel;
use App\Models\Department;

class UserController extends Controller

{

    /**

     * Display a listing of the resource.

     */

    public function index()

    {

        $users = User::orderBy('id', 'DESC')->get();

        return view('users.list', ['users' => $users]);

    }



    /**

     * Show the form for creating a new resource.

     */

    public function create()

    {

        // return view('users.add', ['roles' => UserRoleModel::all()]);
        return view('users.add', ['roles' => UserRoleModel::all(),'departments' => Department::all()]);

    }



    /**

     * Store a newly created resource in storage.

     */

    // public function store(Request $request)

    // {

    //     $validator = Validator::make($request->all(), [

    //         'first_name' => 'required|max:255',

    //         'last_name' => 'required|max:255',

    //         'email' => 'required|email:rfc,dns|max:255|unique:users,email',

    //         'role' => 'required',

    //         'status' => 'required',

    //         'password' => 'required|string|min:8|same:confirm_password',

    //         'confirm_password' => 'required'

    //     ]);



    //     if ($validator->fails()) {

    //         return redirect()->back()->withErrors($validator)->withInput();

    //     }else{

    //         $user = new User;

    //         $user->first_name = $request->input('first_name', 'Digitex');

    //         $user->last_name = $request->input('last_name', 'Technologies');

    //         $user->email = $request->input('email');

    //         $user->phone = $request->input('phone');

    //         $user->role = $request->input('role');

    //         $user->status = $request->input('status');

    //         $user->password = Hash::make($request->input('password'));

    //         $user->date_registered = now();

    //         $user->save();



    //         return redirect(route('user.list'))->withSuccess('User added successfully');

    //     }

    // }

    

    public function store(Request $request)

    {

        $validator = Validator::make($request->all(), [

            'first_name' => 'required|max:255',

            'last_name' => 'required|max:255',

            'email' => 'required|email:rfc,dns|max:255|unique:users,email',

            'department_id' => 'required|integer|exists:departments,id',
            'role' => 'required',

            'status' => 'required',
            'documents.*' => 'mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',

            'password' => 'required|string|min:8|same:confirm_password',

            'confirm_password' => 'required'

        ]);

    

        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();

        } else {

            $user = new User;

            $user->first_name = $request->input('first_name', 'Digitex');

            $user->last_name = $request->input('last_name', 'Technologies');

            $user->email = $request->input('email');

            $user->phone = $request->input('phone');

            $user->department_id = $request->input('department_id');
            $user->role = $request->input('role');

            $user->status = $request->input('status');

            $user->password = Hash::make($request->input('password'));

            $user->date_registered = now();

            $user->save();

            if($request->hasFile('documents')) {
                $fileData = uploadMultipleFiles($request->file('documents'), \UploadPaths::EMPLOYEE_DOCUMENTS);
                foreach($fileData as $data) {
                    $user->employeeDocuments()->create([
                        'original_name' => $data['original_name'],
                        'modified_name' => $data['modified_name'],
                        'file_type' => $data['file_type'],
                        'file_size' => $data['file_size'],
                    ]);
                }
            }

    

            // Queue the email

            Mail::to($user->email)->queue(new UserCreatedMail($user));

    

            return redirect(route('user.list'))->withSuccess('User added successfully, and an email has been queued.');

        }

    }



    /**

     * Show the form for editing the specified resource.

     */

    public function edit(string $id)

    {

        return view('users.edit', ['user' => User::where('id', $id)->first(), 'roles' => UserRoleModel::all(),'departments' => Department::all()]);

    }



    /**

     * Update the specified resource in storage.

     */

    public function update(Request $request, string $id)

    {

        $user = User::where('id', $id)->first();

        $validator = Validator::make($request->all(), [

            'first_name' => 'required|max:255',

            'last_name' => 'required|max:255',

            'email' => 'required|email|unique:users,email,'.$user->id,

            'department_id' => 'required|integer|exists:departments,id',
            'role' => 'required',

            'status' => 'required',
            'documents.*' => 'mimes:jpg,jpeg,png,pdf,doc,docx|max:10240',

            'password' => 'exclude_if:password,null|string|min:8|same:confirm_password',

            'confirm_password' => 'required_with:password'

        ]);



        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();

        }else{

            $user->first_name = $request->input('first_name', 'Digitex');

            $user->last_name = $request->input('last_name', 'Technologies');

            $user->email = $request->input('email');

            $user->phone = $request->input('phone');

            $user->department_id = $request->input('department_id');
            $user->role = $request->input('role');

            $user->status = $request->input('status');

            if($request->input('password') && $request->input('confirm_password')){

                $user->password = Hash::make($request->input('password'));

            }

            $user->save();

            if($request->hasFile('documents')) {
                $fileData = uploadMultipleFiles($request->file('documents'), \UploadPaths::EMPLOYEE_DOCUMENTS);
                foreach($fileData as $data) {
                    $user->employeeDocuments()->create([
                        'original_name' => $data['original_name'],
                        'modified_name' => $data['modified_name'],
                        'file_type' => $data['file_type'],
                        'file_size' => $data['file_size'],
                    ]);
                }
            }



            return redirect(route('user.list'))->withSuccess('User updated successfully');

        }

    }



    /**

     * Remove the specified resource from storage.

     */

    public function destroy(string $id)

    {

        $user = User::where('id', $id)->first();

        if($user){

            $leads = LeadsModel::where('owner', $id)->get();

            $deals = DealsModel::where('owner', $id)->get();
            $adminExists = User::where('id', 1)->exists();

            if($leads){

                foreach($leads as $lead){
                    
                    $lead->owner = $adminExists ? 1 : null;

                    $lead->save();

                }

            }

            if($deals){

                foreach($deals as $deal){

                    $deal->owner = $adminExists ? 1 : null;

                    $deal->save();

                }

            }



            $user->delete();

            return redirect()->back()->withSuccess('User has been deleted');

        }else{

            return redirect(route('user.list'))->withError('User not found');

        }

    }



    public function my_profile()

    {

        $user = auth()->user();

        return view('profile.edit', compact('user'));

    }



    public function my_profile_update(Request $request)

    {

        $user = auth()->user();

        $validator = Validator::make($request->all(), [

            'first_name' => 'required|max:255',

            'last_name' => 'required|max:255',

            'email' => 'required|email:rfc,dns|max:255|unique:users,email,'.$user->id,

            'password' => 'exclude_if:password,null|string|min:8|same:confirm_password',

            'confirm_password' => 'required_with:password'

        ]);



        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();

        }else{

            $user->first_name = $request->input('first_name', 'Digitex');

            $user->last_name = $request->input('last_name', 'Technologies');

            $user->email = $request->input('email');

            $user->phone = $request->input('phone');

            if($request->input('password') && $request->input('confirm_password')){

                $user->password = Hash::make($request->input('password'));

            }

            $user->save();



            return redirect()->back()->withSuccess('Profile updated successfully');

        }

    }

    

    public function showOrganizationChart()

    {

        $user = auth()->user();

    

        // Admin and HR can see all employees

        if (in_array($user->role, [1, 3])) {

            $employees = User::with('subordinates')->get();

        }

        // Reporting person sees all their linked employees

        elseif ($user->reporting_person_id) {

            $employees = $user->getAllSubordinates();

        }

        // Regular employee can view subordinates

        else {

            $employees = $user->subordinates;

        }

    

        return view('organization.chart', compact('employees'));

    }

    public function getRolesByDepartment($id)
    {
        $roles = getRolesByDepartmentId($id);
        return response()->json($roles);
    }

}

