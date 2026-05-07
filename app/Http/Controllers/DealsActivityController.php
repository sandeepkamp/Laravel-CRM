<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales\DealsActivityModel;
use Illuminate\Support\Facades\Validator;
use App\Models\Sales\DealsModel;
use App\Models\Sales\LeadsActivityModel;
use App\Models\Sales\DealsActivityFilesModel;
use File;
use App\Models\User;
use App\Models\Sales\DealStages;
use App\Models\Sales\DealStagesList;
use Illuminate\Support\Carbon;

class DealsActivityController extends SalesController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $id)
    {
        if ($id) {
             $validator = Validator::make($request->all(), [
            'type' => 'required_if:form_type,activity|in:1,2,3,4,5',
            'note' => 'max:5000|required_if:form_type,note',
            'document' => 'required_if:form_type,file',
            'document.*' => 'mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx,csv|max:204800',
            // 'from_date' => 'required_if:form_type,activity|date',
            // 'from_time' => 'required_if:form_type,activity|date_format:H:i',
            // 'to_date' => 'required_if:form_type,activity|date|after_or_equal:from_date', // Custom validation rule
            // 'to_time' => 'required_if:form_type,activity|date_format:H:i|after_or_equal:from_time', // Custom validation rule for time
            ]);
    
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            } else {
                $activity = new DealsActivityModel();
                $activity->deal_id = $id;
    
                // Handle form type for activity, note, or file
                $formType = $request->input('form_type', 'activity');
                if ($formType === 'note') {
                    // dd($formType);
                    $activity->type = 6; // Set type as 6 for note
                    $activity->description = $request->input('note', '');
                    // dd($activity);
                    $activity->done = 1; // Mark as done for notes
                    $activity->status = 1; // Set status as completed
                } elseif ($formType === 'file' && $request->file('document')) {
                    $activity->type = 7; // Set type as 7 for file
                    $activity->description = $request->input('file_note', '');
                    $activity->done = 1;
                    $activity->status = 1;
    
                    $file = $request->file('document');
                    $name = $file->getClientOriginalName();
                    $new_name = $file->hashName();
                    $extension = $file->extension();
                    $file_type = $file->getMimeType();
                    $file->move(public_path('uploads/sales/deals'), $new_name);
                } else {
                    $activity->type = $request->input('type', 1); // Default to type 1
                    $activity->title = $request->input('title', '');
                    $activity->description = $request->input('activity_note', ''); // Assign description
                    $activity->time_from = date('Y-m-d H:i', strtotime($request->input('from_date') . ' ' . $request->input('from_time')));
                    $activity->time_to = date('Y-m-d H:i', strtotime($request->input('to_date') . ' ' . $request->input('to_time')));
                    $activity->done = $request->input('mark_done', 0);
                    $activity->status = 0; // Mark as not completed
                }
    
                $activity->created_at = Carbon::now('Europe/London');
                $activity->updated_at = Carbon::now('Europe/London');
                $activity->save();
    
                // Save file details if form type is file
                if ($formType === 'file') {
                    $activityFiles = new DealsActivityFilesModel();
                    $activityFiles->activity_id = $activity->id;
                    $activityFiles->file_name = $new_name;
                    $activityFiles->original_name = $name;
                    $activityFiles->extension = $extension;
                    $activityFiles->file_type = $file_type;
                    $activityFiles->created_at = Carbon::now('Europe/London');
                    $activityFiles->updated_at = Carbon::now('Europe/London');
                    $activityFiles->save();
                }
    
                return redirect()->back()->withSuccess('Activity added');
            }
        } else {
            return redirect()->back()->withError('Deal not found');
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
    public function edit(string $deal_id, string $id)
    {
        $deal = DealsModel::where('id', $deal_id)->first();
        $update_activity = DealsActivityModel::where('id', $id)->first();
        $persons = User::all();  // Fetch all users
         // Fetch Lead Activities from 'leads_activity' table
        $leadActivities = LeadsActivityModel::where('lead_id', $deal->lead_id)
            ->orderBy('time_from', 'asc')
            ->get();
        // Fetch the stages from DealStagesList model
        $stages = DealStagesList::all();  
    
        if ($update_activity) {
            if ($deal) {
                return view('sales.deals.detail', [
                    'deal' => $deal,
                    'activity' => DealsActivityModel::where('deal_id', $deal_id)->where('done', 0)->orderBy('time_from', 'desc')->get(),
                       'leadActivities' => $leadActivities, // Pass Lead Activities
                    'done_activity' => DealsActivityModel::where('deal_id', $deal_id)->where('done', 1)->orderBy('time_from', 'desc')->get(),
                    'update' => $update_activity,
                    'persons' => $persons,  // Pass the users to the view
                    'stages' => $stages  // Pass the stages to the view
                ]);
            } else {
                return redirect(route('deal.list'))->withError('Deal not found');
            }
        } else {
            return redirect()->back()->withError('Activity not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $deal_id, string $id)
    {
       if($id){
        //   dd($id);
            $validator = Validator::make($request->all(), [
                'type' => 'required_if:form_type,activity|in:1,2,3,4,5',
                'note' => 'max:1000|required_if:form_type,note',
                // 'document' => 'required_if:type,7|file',
                // 'document.*' => 'mimes:jpeg,png,jpg,pdf,doc,docx,xls,xlsx,csv|max:204800',
                'from_date' => 'required_if:type,1,type,2,type,3,type,4,type,5',
                'from_time' => 'required_if:type,1,type,2,type,3,type,4,type,5',
                'to_date' => 'required_if:type,1,type,2,type,3,type,4,type,5',
                'to_time' => 'required_if:type,1,type,2,type,3,type,4,type,5'
            ]);
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }else{
                $activity = DealsActivityModel::where('id', $id)->first();
                $activity->deal_id = $deal_id;
                $activity->title = $request->input('title', '');
                $activity->type = $request->input('type', 1);
                $activity->description = $request->input('activity_note', '');
                $activity->time_from = $request->input('from_date') ? date('Y-m-d H:i', strtotime($request->input('from_date').' '.$request->input('from_time'))) : '';
                $activity->time_to = $request->input('to_date') ? date('Y-m-d H:i', strtotime($request->input('to_date').' '.$request->input('to_time'))) : '';
                if($request->input('type', '') == 6){
                    $activity->done = 1;
                }else{
                    $activity->done = $request->input('mark_done', 0);
                }
                $activity->status = 0;
                $activity->updated_at = now();
                $activity->save();

                return redirect(route('deal.show', [$deal_id]))->withSuccess('Activity updated');
            }
        }else{
            return redirect()->back()->withError('Lead not found');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $deal_id, string $id)
    {
        $activity = DealsActivityModel::where('deal_id', $deal_id)->where('id', $id)->first();
        
        if ($activity) {
            $file = DealsActivityFilesModel::where('activity_id', $activity->id)->first();
            
            // Check if the file exists before deleting
            if ($file) {
                File::delete(public_path('uploads/sales/deals') . '/' . $file->file_name);
            }
    
            $activity->delete();
    
            return redirect()->back()->withSuccess('Activity deleted');
        } else {
            return redirect(route('lead.list'))->withError('Activity not found');
        }
    }

    
    public function docusignsent(Request $request){
        \Log::info($request->recipients);
        $envelopeId = $request->data['envelopeId'];
        DealsActivityModel::where('envelop_id', $envelopeId)->update([
            "done"=>1
            ]);
    }
     public function docusignpage(){

        return view("docusign-page");
    }
    
    public function mark_as_done(string $deal_id, string $id)
    {
        // Find the activity that has not been marked as done
        $activity = dealsActivityModel::where('deal_id', $deal_id)
        ->where('id', $id)
        ->where('done', 0) // Ensure the activity is not already marked as done
        ->first();
    
        if ($activity) {
            // Mark the activity as done
            $activity->done = 1;
            $activity->save();
    
            // Redirect back to the deal show page with a success message
            return redirect(route('deal.show', $deal_id))->withSuccess('Activity marked as done');
        } else {
            // If the activity wasn't found or was already done
            return redirect(route('deal.list'))->withError('Activity not found or already marked as done');
        }
    }
}
