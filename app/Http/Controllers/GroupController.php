<?php



namespace App\Http\Controllers;



use App\Http\Controllers\Controller;

use App\Models\Group;

use App\Models\GroupUsers;

use App\Models\User;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Illuminate\Support\Facades\Validator;



class GroupController extends Controller

{

    /**

     * Display a listing of the resource.

     */

    public function index()

    {

        

        $groups = Group::all();



        return view('groups.list', compact('groups'));

    }



    /**

     * Show the form for creating a new resource.

     */

    public function create()

    {

        //

        $users = User::all();

        $user_ids = GroupUsers::pluck('user_id')->toArray();

        $members = User::whereNotIn('id', $user_ids)->get();



        // $members = 

        return view('groups.add', compact('users', 'members'));

    }



    /**

     * Store a newly created resource in storage.

     */

    public function store(Request $request)

    {

        DB::beginTransaction();

        try {

            $owner = $request->owner;

            $group = new Group;

            $group->name = $request->name;





            //check if the request has the owner or not

            if ($owner !== null) {

                $group->owner_user_id = $owner;

            }



            $group->save();





            if (($owner !== null) && ($request->members !== null)) {

                foreach ($request->members as $member) {

                    // dd($member);

                    // exit();

                    // insert into the group_user table 

                    GroupUsers::create([

                        'group_id' => $group->id,

                        'user_id' => $member,

                        'owner_user_id' =>$owner,

                    ]);

                }

            }

            DB::commit();

            return redirect()->route('group.list')->withSuccess('Group Added Successfully.');

        } catch (\Exception $e) {

            //throw $th;

            Log::info("Rolling back the group id|name - $group->id | $group->name | ");

            Log::error("Error : while creating the group and assigning the users " . $e->getMessage());

            DB::rollBack();

            return redirect()->route('group.list')->with('error', 'An error occurred.');

        }

    }



    /**

     * Display the specified resource.

     */

    public function show(string $id)

    {

        //



    }



    /**

     * Show the form for editing the specified resource.

     */

    public function edit(string $id)

    {



        $group = Group::find($id);

        if ($group) {

            $users = User::all();



            $user_ids = GroupUsers::whereNot('group_id', $id)->pluck('user_id')->toArray();



            $members = User::whereNotIn('id', $user_ids)->get();



            $group_users = GroupUsers::where('group_id', $group->id)->pluck('user_id')->toArray();

            // dd($group_users);

            return view('groups.edit', compact('group', 'users', 'group_users', 'members'));

        } else {

            return redirect()->route('group.list')->with('error', 'Group not found.');

        }

    }



    /**

     * Update the specified resource in storage.

     */

    public function update(Request $request, string $id)

    {

        // dd($request->all());

        $validator = Validator::make($request->all(), [

            'name' => 'required',

        ]);



        if ($validator->fails()) {

            return redirect()->back()->withErrors($validator)->withInput();

        }



        $group = Group::find($id);

        if ($group) {

            DB::beginTransaction();

            try {

                $owner = $request->owner;



                // $group = new Group;

                $group->name = $request->name;





                //check if the request has the owner or not

                if ($owner !== null) {

                    $group->owner_user_id = $owner;

                }



                $group->save();





                if (($owner !== null) && ($request->members !== null)) {
                    $currentUsers = DB::table('group_users')->where('group_id',$id)->pluck('user_id')->toArray();
                    $currentUsers = collect($currentUsers);
                    $updatedUsers = collect($request->members);

                    $usersToAdd = $updatedUsers->diff($currentUsers);
                    $usersToRemove = $currentUsers->diff($updatedUsers);
                    
                    if ($usersToAdd->isNotEmpty()) {
                        foreach ($usersToAdd as $userId) {
                            GroupUsers::create([
                                'group_id' => $id,
                                'user_id' => $userId,
                                'owner_user_id' => $owner
                            ]);
                        }
                    }
                    
                    if ($usersToRemove->isNotEmpty()) {
                        DB::table('group_users')
                        ->where('group_id', $id)
                        ->whereIn('user_id', $usersToRemove)
                        ->delete();
                    }
                    
                }
                DB::commit();

                return redirect()->route('group.list')->withSuccess('Group Updated Successfully.');

            } catch (\Exception $e) {

                //throw $th;

                Log::info("Rolling back the group id|name - $group->id | $group->name | ");

                Log::error("Error : while updating the group and assigning the users " . $e->getMessage());

                DB::rollBack();

                return redirect()->route('group.list')->with('error', 'An error occurred.');

            }

        } else {

            return redirect()->route('group.list')->with('errors', 'Group not found.');

        }

    }



    /**

     * Remove the specified resource from storage.

     */

    public function destroy(string $id)

    {

        // verify the group id is present or not in the db

        DB::beginTransaction();

        try {

            $group = Group::find($id);



            if ($group !== null) {

                // first remove all the records from the groupuser table for group->id

                GroupUsers::where('group_id', '=', $group->id)->delete();

                Group::where('id', '=', $group->id)->delete();

                DB::commit();

                return redirect()->route('group.list')->withSuccess("Group deleted successfully.");

            } else {



                return redirect()->route('group.list')->with('error', 'Group not found.');

            }

        } catch (\Exception $e) {

            //throw $th;

            Log::error($e->getMessage());

            DB::rollBack();

            return redirect()->route('group.list')->with('error', 'An error occurred.');

        }

    }



    public function get_descendants($id)

    {

        {

            $query = "WITH RECURSIVE Descendants AS (

                -- Base case: start with the user of interest

                SELECT 

                    u.id AS user_id,

                    u.first_name AS user_name,

                    g.id AS group_id,

                    g.name AS group_name

                FROM 

                    users u

                JOIN 

                    `groups` g ON g.owner_user_id = u.id

                WHERE 

                    u.id = $id

                

                UNION ALL

                

                -- Recursive case: find groups owned by users in the previous step

                SELECT 

                    u.id AS user_id,

                    u.first_name AS user_name,

                    g.id AS group_id,

                    g.name AS group_name

                FROM 

                    `groups` g

                JOIN 

                    group_users gu ON g.id = gu.group_id

                JOIN 

                    users u ON gu.user_id = u.id

                JOIN 

                    Descendants d ON d.user_id = g.owner_user_id

            )



            -- Select all unique user ids from the recursive CTE, excluding the initial user

            SELECT DISTINCT

                d.user_id,

                d.user_name

            FROM 

                Descendants d

            WHERE 

                d.user_id != $id

            ORDER BY

                d.user_id;";

        }



        $results = DB::select($query);

        return $results;

    }

    

    public function get_ancestor($id)

    {

        $query = "WITH RECURSIVE Ancestors AS (

            -- Base case: start with the user's group and its owner

            SELECT g.owner_user_id AS id, u.name

            FROM Users u

            JOIN Group_Users gu ON u.id = gu.user_id

            JOIN Groups g ON gu.group_id = g.id

            WHERE u.id = $id 

            -- Replace :user_id with the actual user ID



            UNION ALL



            -- Recursive case: find the owner of the current level of ancestors

            SELECT g.owner_user_id AS id, u.name

            FROM Users u

            JOIN Groups g ON u.id = g.owner_user_id

            JOIN Ancestors a ON g.owner_user_id = a.id

        )

        SELECT * FROM Ancestors WHERE id IS NOT NULL;

                        ";

                

        $results = DB::select($query);

        return $results;



    }



    

    

   



}

