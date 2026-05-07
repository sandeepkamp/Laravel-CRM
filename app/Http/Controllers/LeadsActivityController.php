<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Sales\LeadsActivityModel;
use App\Models\Sales\LeadsModel;
use App\Models\User;

class LeadsActivityController extends SalesController
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        if($id){            
            // Trim all input data
            $input = array_map('trim', $request->all());
            $validator = Validator::make($input, [
                'title' => 'required_if:form_type,activity|string|min:1|max:255|not_regex:/^\s*$/',
                'type' => 'required_if:form_type,activity',
                'note' => 'max:5000|required_if:form_type,note',
                // 'from_date' => 'required_if:type,1,type,2,type,3,type,4,type,5',
                // 'from_time' => 'required_if:type,1,type,2,type,3,type,4,type,5',
                // 'to_date' => 'required_if:type,1,type,2,type,3,type,4,type,5',
                // 'to_time' => 'required_if:type,1,type,2,type,3,type,4,type,5',
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{
                $activity = new LeadsActivityModel;
                $activity->lead_id = $id;
                $activity->title = $request->input('title', '');
                $activity->type = $request->get('form_type') == 'note' ? 6 : $request->input('type', 1);
                if($request->input('form_type', '') == 'note'){
                    $activity->description = $request->input('note', '');
                    $activity->done = 1;
                    $activity->status = 1;
                    $activity->time_from = now(); // Store created_at as time_from
                    $activity->time_to = now(); 
                }else{
                    $activity->time_from = date('Y-m-d H:i', strtotime($request->input('from_date').' '.$request->input('from_time')));
                    $activity->time_to = date('Y-m-d H:i', strtotime($request->input('to_date').' '.$request->input('to_time')));
                    $activity->done = $request->input('mark_done', 0);
                    $activity->status = 0;
                    $activity->description = $request->input('activity_note', '');
                }
                $activity->created_at = now();
                $activity->updated_at = now();
                $activity->save();

                return redirect()->back()->withSuccess('Activity added');
            }
        }else{
            return redirect()->back()->withError('Lead not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $lead_id, string $id)
    {
        $lead = LeadsModel::where('converted', 0)->where('id', $lead_id)->first();
        $update_activity = LeadsActivityModel::where('id', $id)->first();
        $users = User::all();
        if($update_activity){
            if($lead){
                return view('sales.leads.detail', [
                    'lead' => $lead, 
                    'activity' =>  LeadsActivityModel::where('lead_id', $lead_id)->where('done', 0)->orderBy('time_from', 'desc')->get(),
                    'done_activity' => LeadsActivityModel::where('lead_id', $lead_id)->where('done', 1)->orderBy('time_from', 'desc')->get(),
                    'update' => $update_activity,
                    'users'=> $users
                ]);
            }else{
                return redirect(route('lead.list'))->withError('Lead not found');
            }
        }else{
            return redirect()->back()->withError('Activity not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $lead_id, string $id)
    {
        if($id){
            $validator = Validator::make($request->all(), [
                'title' => 'required_if:form_type,activity|string|min:1|max:255|not_regex:/^\s*$/',
                'note' => 'max:1000|required_if:form_type,note',
                // 'from_date' => 'required_if:type,1,type,2,type,3,type,4,type,5',
                // 'from_time' => 'required_if:type,1,type,2,type,3,type,4,type,5',
                // 'to_date' => 'required_if:type,1,type,2,type,3,type,4,type,5',
                // 'to_time' => 'required_if:type,1,type,2,type,3,type,4,type,5'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{
                $activity = LeadsActivityModel::where('id', $id)->first();
                $activity->lead_id = $lead_id;
                $activity->title = $request->input('title', '');
                $activity->type = $request->get('form_type') == 'note' ? 6 : $request->input('type', 1);
                if($request->input('form_type', '') == 'note'){
                    $activity->description = $request->input('note', '');
                }else{
                    $activity->time_from = date('Y-m-d H:i', strtotime($request->input('from_date').' '.$request->input('from_time')));
                    $activity->time_to = date('Y-m-d H:i', strtotime($request->input('to_date').' '.$request->input('to_time')));
                    $activity->done = $request->input('mark_done', 0);
                    $activity->description = $request->input('activity_note', '');
                }
                $activity->updated_at = now();
                $activity->save();

                return redirect(route('lead.show', [$lead_id]))->withSuccess('Activity updated');
            }
        }else{
            return redirect()->back()->withError('Lead not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $lead_id, string $id)
    {
        $activity = LeadsActivityModel::where('lead_id', $lead_id)
                                      ->where('id', $id)
                                      ->first();
    
        if ($activity) {
            $activity->delete();
            return redirect(route('lead.show', $lead_id))->withSuccess('Activity deleted');
        } else {
            return redirect(route('lead.show', $lead_id))->withError('Activity not found');
        }
    }

    public function mark_as_done(string $lead_id, string $id)
    {
        $activity = LeadsActivityModel::where('lead_id', $lead_id)->where('id', $id)->first();
        if($activity){
            $activity->done = 1;
            $activity->save();
            return redirect(route('lead.show', $lead_id))->withSuccess('Activity marked as done');
        }else{
            return redirect(route('lead.list'))->withError('Activity not found');
        }
    }

    public function mark_as_todo(string $lead_id, string $id)
    {
        $activity = LeadsActivityModel::where('lead_id', $lead_id)->where('id', $id)->first();
        if($activity){
            $activity->done = 0;
            $activity->save();
            return redirect(route('lead.show', $lead_id))->withSuccess('Activity marked as todo');
        }else{
            return redirect(route('lead.list'))->withError('Activity not found');
        }
    }
}
