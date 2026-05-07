<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales\LeadLabels;
use App\Models\Sales\LeadsModel;
use App\Models\Sales\LeadLabelList;
use App\Models\Sales\OrganisationModel;
use App\Models\Sales\DealsModel;
use App\Models\Sales\DealStages;
use App\Models\User;
use App\Models\Sales\LeadsActivityModel;
use Illuminate\Support\Facades\Validator;
use Auth;
use Illuminate\Support\Facades\Auth as FacadesAuth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\DataTables\Sales\LeadsDataTable;
use App\Models\Sales\DealStagesList;

class LeadsController extends SalesController
{
    /**
     * Display a listing of the resource.
     */
    public function index(LeadsDataTable $dataTable)
    {
        return $dataTable->render('sales.leads.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();
        // $childs = $user->all_childs();
        // $parents = $user->all_parents();
        // $childs = $user->all_parents();
        // foreach ($descendants as $d) {
        //     dump($d->first_name);
        // }
        // dd($user->first_name,$descendants);
        // return view('sales.leads.add', ['labels' => LeadLabelList::where('status', 1)->orderBy('name')->get(), 'users' => User::whereIn('role', [1,2])->get()]);
        return view('sales.leads.add', ['parents' => User::whereIn('role', [1,2])->get(),'labels' => LeadLabelList::where('status', 1)->orderBy('name')->get(), 'users' =>  User::whereIn('role', [1,2])->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            // 'last_name' => 'required|max:255',
            'email' => 'required|email:rfc,dns|max:255',
            'company_name' => 'required',
            'lead_value' => 'decimal:2'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            if($request->get('website', '') != ''){
                $another_validator = Validator::make($request->all(), [
                    'website' => 'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
                ]);

                if ($another_validator->fails()) {
                    return redirect()->back()->withErrors($another_validator)->withInput();
                }
            }
            if($request->get('phone_number', '') != ''){
                $another_validator = Validator::make($request->all(), [
                    'phone_number' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20'
                ]);

                if ($another_validator->fails()) {
                    return redirect()->back()->withErrors($another_validator)->withInput();
                }
            }
            $org = OrganisationModel::where('name', 'like', $request->input('company_name', ''))->first();
            // dd($org);
            if(!$org){
                $org = new OrganisationModel;
                $org->name = $request->input('company_name', '');
                $org->save();
            }
            $lead = new LeadsModel;
            $lead->first_name = trim($request->input('first_name', ''));
            $lead->last_name = trim($request->input('last_name', ''));
            $lead->email = trim($request->input('email', ''));
            $lead->phone = trim($request->input('phone_number', ''));
            $lead->title = trim($request->input('designation', ''));
            $lead->organisation = $org->id;
            $lead->website = trim($request->input('website', ''));
            $lead->value = trim($request->input('lead_value', ''));
            $lead->currency = trim($request->input('currency', ''));
            // $lead->close_date = trim($request->input('close_date', '') ? date('Y-m-d', strtotime($request->input('close_date', ''))) : null);
            $lead->close_date = $request->input('close_date', '') ? date('Y-m-d', strtotime($request->input('close_date'))) : null;
            $lead->owner = trim($request->input('owner', $request->user()->id));
            $lead->visible_to = trim($request->input('visible_to', $request->user()->id));
            $lead->converted = 0;
            $lead->created_at = now();
            $lead->updated_at = now();
            $lead->save();

            if($request->input('labels')){
                foreach($request->input('labels') as $label){
                    $labels = new LeadLabels;
                    $labels->lead_id = $lead->id;
                    $labels->lead_label = $label;
                    $labels->created_at = now();
                    $labels->updated_at = now();
                    $labels->save();
                }
            }
            return redirect(route('lead.show', $lead->id))->withSuccess('Lead has been created successfully');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
        $lead = LeadsModel::where(function($query){
            if(auth()->user()->role != 1){
                $query->where('visible_to', auth()->user()->id)->orWhere('visible_to', null);
            }
        })->where('converted', 0)->where('id', $id)->first();
        if($lead){
            $activity = LeadsActivityModel::where('lead_id', $id)->where('done', 0)->orderBy('time_from', 'asc')->get();
            $users = User::all();
            return view('sales.leads.detail', ['lead' => $lead, 'activity' => $activity, 'done_activity' => LeadsActivityModel::where('lead_id', $id)->where('done', 1)->orderBy('updated_at', 'desc')->get(), 'update' => '','users' => $users]);
        }else{
            return redirect(route('lead.list'))->withError('Lead not found');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
        $lead = LeadsModel::where('id', $id)->where('converted', 0)->first();
        if($lead){
            $pre_label = $lead->labels;
            if($pre_label->count() > 0){
                foreach($pre_label as $l){
                    $previous_label[] = $l->lead_label;
                }
            }else{
                $previous_label = [];
            }
            $user = User::where('id', $lead->owner)->first();
            $owners =[];
            // dd($owners);
            // return view('sales.leads.edit', ['lead' => $lead, 'pre_label' => $previous_label, 'labels' => LeadLabelList::where('status', 1)->orderBy('name')->get(), 'users' => User::whereIn('role', [1,2])->get(), 'pre_user' => $user]);
            return view('sales.leads.edit', ['owners'=>$owners,'lead' => $lead, 'pre_label' => $previous_label, 'labels' => LeadLabelList::where('status', 1)->orderBy('name')->get(), 'users' => User::whereIn('role', [1,2])->get(), 'pre_user' => $user]);
        }else{
            return redirect(route('lead.list'))->withError('Lead not found');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            // 'last_name' => 'required|max:255',
            'email' => 'required|email:rfc,dns|max:255',
            'company_name' => 'required',
            'lead_value' => 'decimal:2'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            if($request->get('website', '') != ''){
                $another_validator = Validator::make($request->all(), [
                    'website' => 'regex:/^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.-]*)*\/?$/',
                ]);

                if ($another_validator->fails()) {
                    return redirect()->back()->withErrors($another_validator)->withInput();
                }
            }
            if($request->get('phone_number', '') != ''){
                $another_validator = Validator::make($request->all(), [
                    'phone_number' => 'regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:20'
                ]);

                if ($another_validator->fails()) {
                    return redirect()->back()->withErrors($another_validator)->withInput();
                }
            }
            $org = OrganisationModel::where('name', 'like', $request->input('company_name', ''))->first();
            if(!$org){
                $org = new OrganisationModel;
                $org->name = $request->input('company_name', '');
                $org->save();
            }

            $lead = LeadsModel::where('id', $id)->first();
            $lead->first_name = trim($request->input('first_name', ''));
            $lead->last_name = trim($request->input('last_name', ''));
            $lead->email = trim($request->input('email', ''));
            $lead->phone = trim($request->input('phone_number', ''));
            $lead->title = trim($request->input('designation', ''));
            $lead->organisation = $org->id;
            $lead->website = trim($request->input('website', ''));
            $lead->value = trim($request->input('lead_value', ''));
            $lead->currency = trim($request->input('currency', ''));
            // $lead->close_date = trim($request->input('close_date', '') ? date('Y-m-d', strtotime($request->input('close_date', ''))) : null);
            $lead->close_date = $request->input('close_date', '') ? date('Y-m-d', strtotime($request->input('close_date'))) : null;
            $lead->owner = trim($request->input('owner', $request->user()->id));
            $lead->visible_to = trim($request->input('visible_to', $request->user()->id));
            $lead->converted = 0;
            $lead->created_at = now();
            $lead->updated_at = now();
            $lead->save();

            if($request->input('labels')){
                LeadLabels::where('lead_id', $lead->id)->delete();
                foreach($request->input('labels') as $label){
                    $labels = new LeadLabels;
                    $labels->lead_id = $lead->id;
                    $labels->lead_label = $label;
                    $labels->created_at = now();
                    $labels->updated_at = now();
                    $labels->save();
                }
            }
            return redirect(route('lead.show', $lead->id))->withSuccess('Lead has been updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lead = LeadsModel::where('id', $id)->where('converted', 0)->first();
        if($lead){
            $lead->delete();
            return redirect()->back()->withSuccess('Lead has been deleted');
        }else{
            return redirect()->back()->withError('Lead not found');
        }
    }

    public function convert(Request $request,string $id)
    {
        // dd(568);
       
            //code...
            // validation of inputs
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email',
                'phone_number' => 'required',
                'designation' => 'required',
                'website' => 'required|url', 
                'lead_value' => 'required',
                'currency' => 'required',
                'close_date' => 'required|date',
                'owner' => 'required',
                'lead_id' => $lead->id,
            ]);
            if($validator->fails())
            {
                // dd($validator);
                $validator->errors()->add('modal',true);
                return redirect()->back()->withErrors($validator)->withInput();
            }
        try {
            DB::beginTransaction();
            if($id){
                $lead = LeadsModel::where('id', $id)->first();
                if($lead){
    
                    if($lead->converted == 0){
                        $deal = new DealsModel;
                        $deal->first_name = $lead->first_name ?? $request->input('first_name');
                        $deal->last_name = $lead->last_name ?? $request->input('last_name');
                        $deal->email = $lead->email ?? $request->input('email');
                        $deal->phone = $lead->phone ?? $request->input('phone_number');
                        $deal->title = $lead->title ?? $request->input('designation');
                        $deal->organisation = $lead->organisation ;//?? $request('company_name');
                        $deal->website = $lead->website ?? $request->input('website');
                        $deal->value = $lead->value ?? $request->input('lead_value');
                        $deal->currency = $lead->currency ?? $request->input('currency');
                        $deal->close_date = $lead->close_date ?? $request->input('close_date');
                        $deal->owner = $lead->owner ?? $request->input('owner');
                        $deal->converted = 0;
                        $deal->created_at = now();
                        $deal->updated_at = now();
                        $deal->save();
    
                        $dealstage = new DealStages;
                        $dealstage->deal_id = $deal->id;
                        $dealstage->stage = 1;
                        $dealstage->created_at = now();
                        $dealstage->updated_at = now();
                        $dealstage->save();
    
                        $lead->converted = 1;
                        $lead->save();

                        DB::commit();
    
                        return redirect(route('lead.list'))->withSuccess('Lead converted successfully');
                    }else{
                        return redirect()->back()->withError('This lead already converted');
                    }
                }else{
                    return redirect()->back()->withError('Lead not found');
                }
            }else{
                return redirect()->back()->withError('Invalid action');
            }

        } catch (\Exception $e) {
            DB::rollback();
            // Log the error
            Log::error('Lead conversion failed: ' . $e->getMessage());
            return redirect()->back()->withError('An error occurred. Please try again later.');
        }
    }
    public function transfer_owner(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'user_id' => 'required|exists:users,id',
        ]);
    
        $user = User::find($request->user_id);
        $lead = LeadsModel::find($request->lead_id);
    
        if (!$user || !$lead) {
            return redirect()->back()->withErrors('Lead or User not found.');
        }
    
        // Update owner and visible_to fields
        $lead->owner = $user->id;
        $lead->visible_to = $user->id;
        $lead->save();
    
        return redirect()->back()->withSuccess('Ownership has been transferred successfully.');
    }

    public function update_info(Request $request)
    {
        if($request->ajax()){
            $lead = LeadsModel::where('id', $request->get('id', ''))->where('converted', 0)->first();
            $pre_label = $lead->labels;
            if($pre_label->count() > 0){
                foreach($pre_label as $l){
                    $previous_label[] = $l->lead_label;
                }
            }else{
                $previous_label = [];
            }
            $labels = LeadLabelList::where('status', 1)->orderBy('name')->get();
            if($lead){
                switch ($request->get('action', '')) {
                    case 'labels':
                        return view('sales.leads.partials.modals.change_labels', compact('labels', 'previous_label'));
                        break;
                    case 'value':
                        return view('sales.leads.partials.modals.change_value', compact('lead'));
                        break;
                    case 'owner':
                        break;
                    case 'close_date':
                        return view('sales.leads.partials.modals.change_close_date', compact('lead'));
                        break;
                    
                    default:
                        return 'Invalid action provided';
                        break;
                }
            }
        }
    }

    public function ajax_update_info(Request $request)
    {
        if($request->ajax()){
            switch ($request->get('modal-type', '')) {
                case 'labels':
                    LeadLabels::where('lead_id', $request->get('lead-id', ''))->delete();
                    if($request->input('labels')){
                        foreach($request->input('labels') as $label){
                            $labels = new LeadLabels;
                            $labels->lead_id = $request->get('lead-id', '');
                            $labels->lead_label = $label;
                            $labels->created_at = now();
                            $labels->updated_at = now();
                            $labels->save();
                        }
                    }
                    break;
                case 'value':
                    $lead = LeadsModel::where('id', $request->get('lead-id', ''))->first();
                    $lead->value = trim($request->get('lead_value', ''));
                     $lead->updated_at = now();
                     $lead->save();
                    break;
                case 'owner':
                    break;
                case 'close_date':
                    $lead = LeadsModel::where('id', $request->get('lead-id', ''))->first();
                    $lead->close_date = trim($request->get('close_date', '') ? date('Y-m-d', strtotime($request->get('close_date', ''))) : null);
                    $lead->updated_at = now();
                    $lead->save();
                    break;
            }
            return redirect()->back()->withSuccess('Labels updated');
        }else{
            return redirect()->back()->withError('Something went wrong');
        }
    }

    public function convert_lead(Request $request)
    {
        if($request->ajax()){
            $lead = LeadsModel::where('id', $request->get('id', ''))->where('converted', 0)->first();
            $stages = DealStagesList::all();
            return view('sales.leads.partials.modals.convert_lead', compact('lead', 'stages'));
        }
    }

    public function ajax_convert_lead(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required',
            'designation' => 'required',
            'website' => 'nullable|url', 
            'lead_value' => 'required',
            'currency' => 'required',
            'close_date' => 'required|date',
            'stage' => 'required', // Make sure stage is validated
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        try {
            DB::beginTransaction();
    
            $lead = LeadsModel::where('id', $request->id)->where('converted', 0)->first();
    
            if (!$lead) {
                return response()->json(['error' => 'Lead not found or already converted'], 404);
            }
    
            // Create the deal
            $deal = new DealsModel();
            $deal->lead_id = $lead->id; 
            $deal->first_name = $request->input('first_name');
            $deal->last_name = $request->input('last_name');
            $deal->email = $request->input('email');
            $deal->phone = $request->input('phone_number');
            $deal->title = $request->input('designation');
            $deal->organisation = $lead->organisation;
            $deal->website = $request->input('website');
            $deal->value = $request->input('lead_value');
            $deal->currency = $request->input('currency');
            $deal->close_date = $request->input('close_date');
            $deal->owner = $lead->owner;
            $deal->converted = 0;
            $deal->created_at = now();
            $deal->updated_at = now();
            $deal->save();
    
            // Create deal stage
            $dealStage = new DealStages();
            $dealStage->deal_id = $deal->id;
            $dealStage->stage = $request->input('stage');
            $dealStage->created_at = now();
            $dealStage->updated_at = now();
            $dealStage->save();
    
            // Update the lead as converted
            $lead->converted = 1;
            $lead->save();
    
            DB::commit();
    
            return response()->json(['success' => true, 'message' => 'Lead converted successfully']);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error converting lead: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while converting the lead. Please try again later.'], 500);
        }
    }

}
