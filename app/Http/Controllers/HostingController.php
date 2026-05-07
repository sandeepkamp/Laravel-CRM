<?php

namespace App\Http\Controllers;

use App\Models\HostingDetails;
use App\Models\HostingProvider;
use App\Models\Sales\DealsModel;
use Illuminate\Http\Request;
use App\Mail\HostingPlanMail;
use Illuminate\Support\Facades\Queue; 
use Illuminate\Support\Facades\Mail;

class HostingController extends Controller
{
    
    public function hostingmanagemet()
    {
        // Count the total number of users
        // $totalUsersCount = DealsModel::count();
        $totalUsersCount = HostingDetails::distinct('client_id')->count('client_id');
    
        // Count users with an active plan
        $activePlanCount = HostingDetails::whereHas('client', function ($query) {
        $query->where('status', 1); // Ensure client status is active
        })
        ->where('status', HostingDetails::STATUS_ACTIVE) // Ensure plan is active
        ->where(function ($query) {
            $query->whereNull('expiry_date') // Plans with no expiry date
                ->orWhere('expiry_date', '>', now()); // Or plans that are not expired
        })
        ->count();
    
        // Count users who have purchased a hosting plan
        $inactivePlanCount = HostingDetails::whereHas('client', function($query) {
            $query->where('status', 0);
        })->count();
        // dd($purchasedPlanUsersCount);
        
        // Count users with plans that need to be renewed
        $renewPlanCount = HostingDetails::where('status', HostingDetails::STATUS_RENEW)
        ->orWhere(function ($query) {
            $query->whereNotNull('expiry_date')
                ->where('expiry_date', '<', now()); // Plans that are expired
        })
        ->count();
        
        // Fetch inactive purchased plans (status = 0) and not yet activated
        $inactivePlans = HostingDetails::where('status', 0) // Purchased but inactive plans
        ->whereNull('activation_date') // Plans that have not been activated
        ->get();
        
        return view('hosting.dashboard', compact('totalUsersCount', 'activePlanCount', 'inactivePlanCount','renewPlanCount', 'inactivePlans'));
    }

    public function index()
    {
        $hostingDetails = HostingDetails::with('hostingProvider', 'client')->get();
        // dd($hostingDetails);
        // exit();
        return view('hosting.index', compact('hostingDetails'));
    }

    public function create()
    {
        $providers = HostingProvider::all();
        $clients = DealsModel::all();
        // dd($clients);
        return view('hosting.create', compact('providers', 'clients'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|integer',
            'hosting_provider_id' => 'required|integer',
            'date_of_purchase' => 'nullable|date',
            'activation_date' => 'nullable|date',
            'expiry_date' => 'nullable|date',
            'plan_details' => 'required|string',
            'cost' => 'required|numeric',
        ]);
    
        // Set date_of_purchase to the current date
        $date_of_purchase = $request->date_of_purchase ?? now()->format('Y-m-d');
    
        // Set the status to false (0)
        $status = $request->status ?? 0;
    
        // Create the HostingDetails record
        HostingDetails::create([
            'client_id' => $request->client_id,
            'hosting_provider_id' => $request->hosting_provider_id,
            'plan_details' => $request->plan_details,
            'date_of_purchase' => $date_of_purchase,
            'activation_date' => $request->activation_date,
            'expiry_date' => $request->expiry_date,
            'cost' => $request->cost,
            'status' => (bool)$status,  // Ensure it's a boolean (false or 0)
        ]);
    
        return redirect()->route('hosting.list')->with('success', 'Hosting details and configurations added successfully.');
    }
    
    public function activate(Request $request, $id)
    {
        // Find the hosting detail by ID
        $hosting = HostingDetails::findOrFail($id);
    
        // Get the number of months from the request and cast to an integer
        $months = (int) $request->input('months');
    
        // Validate months input
        if ($months <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Invalid number of months.']);
        }
    
        // Calculate new activation and expiry dates
        $activationDate = now()->format('Y-m-d'); // Always set the activation date to the current date
        $expiryDate = now()->addMonths($months)->format('Y-m-d'); // Calculate the expiry date based on the number of months
    
        // Update the hosting details
        $hosting->activation_date = $activationDate;
        $hosting->expiry_date = $expiryDate;
    
        // Update status based on expiry date
        $hosting->status = now()->lessThanOrEqualTo($expiryDate)
            ? HostingDetails::STATUS_ACTIVE
            : HostingDetails::STATUS_RENEW;
    
        $hosting->save();
    
        // Prepare data for the email
        $emailData = [
            'client_name' => $hosting->client->first_name . ' ' . $hosting->client->last_name,
            'provider_name' => $hosting->hostingProvider->provider_name,
            'plan_details' => $hosting->plan_details,
            'activation_date' => $hosting->activation_date,
            'expiry_date' => $hosting->expiry_date,
            'cost' => $hosting->cost,
            'action' => 'activated/renewed',
        ];
    
        // Send the email
        \Log::info('Preparing to send email for Hosting Plan Activation/Renewal', $emailData);
        Mail::to($hosting->client->email)->send(new HostingPlanMail($emailData));
        \Log::info('Email sent successfully for client: ' . $hosting->client->email);
    
        return response()->json(['status' => 'success', 'message' => 'Plan updated successfully.']);
    }

    public function edit($id)
    {
        $hostingDetail = HostingDetails::findOrFail($id);
        // dd($hostingDetail);
        $providers = HostingProvider::all();
        // dd($providers);
        $clients = DealsModel::all();

        return view('hosting.edit', compact('hostingDetail', 'providers', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $hostingDetail = HostingDetails::findOrFail($id);
    
        $request->validate([
            'client_id' => 'required|integer',
            'hosting_provider_id' => 'required|integer',
            'date_of_purchase' => 'nullable|date',
            'plan_details' => 'required|string',
            'cost' => 'required|numeric',
        ]);
    
        // Update the hosting details (excluding readonly fields)
        $hostingDetail->client_id = $request->client_id;
        $hostingDetail->hosting_provider_id = $request->hosting_provider_id;
        $hostingDetail->date_of_purchase = $request->date_of_purchase ?? $hostingDetail->date_of_purchase;
        $hostingDetail->plan_details = $request->plan_details;
        $hostingDetail->cost = $request->cost;
    
        // Save the updated details
        $hostingDetail->save();
    
        return redirect()->route('hosting.list')->with('success', 'Hosting details updated successfully.');
    }

    public function destroy($id)
    {
        $hostingDetail = HostingDetails::findOrFail($id);
        $hostingDetail->delete();

        return redirect()->route('hosting.list')->with('success', 'Hosting detail deleted successfully.');
    }
}
