<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\SeoService;
use App\Models\Sales\DealsModel;
use Illuminate\Validation\Rule;
use App\Constants\Enum;

class SeoServiceController extends Controller

{
    public function index()
    {
        $totalClients = SeoService::count();
        $activePlans = SeoService::where('status', 'Active')->count();
        $inactivePlans = SeoService::where('status', 'Inactive')->count();
        $renewSoon = SeoService::where('renewal_date', '>=', now())->where('renewal_date', '<=', now()->addDays(7))->count();
        $overduePlans = SeoService::where('renewal_date', '<', now())->whereNotNull('renewal_date')->get();
        return view('seo.dashboard', compact('totalClients','activePlans','inactivePlans','renewSoon','overduePlans'));
    }

    public function getSeoList()
    {
        $seoServices = SeoService::latest()->paginate(10);
        return view('seo.index', compact('seoServices'));
    }

    public function create()
    {
        $clients = DealsModel::all();
        return view('seo.create', compact('clients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|integer',
            'website_url' => 'required|url|max:255',
            'industry' => 'required|string|max:255',
            // 'seo_package' => 'required|in:basic,advanced,premium',
            'seo_package' => ['required', 'string', Rule::in(array_keys(Enum::SEO_PACKAGES))],
            'start_date' => 'required|date',
            'renewal_date' => 'required|date|after_or_equal:start_date',
            // 'status' => 'required|in:active,inactive,pending',
            'status' => ['required', 'string', Rule::in(array_keys(Enum::SEO_STATUSES))],
        ]);
        try {
        $seoService = SeoService::create($validated);
        $this->updateSeoStatus($seoService->id);
        return redirect()->route('seo.list')->with('success', 'SEO Service added successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $seo = SeoService::findOrFail($id);
        $clients = DealsModel::all();
        return view('seo.edit', compact('seo', 'clients'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'client_id' => 'required|integer|exists:deals,id',
            'website_url' => 'required|url|max:255',
            'industry' => 'required|string|max:255',
            'seo_package' => ['required', 'string', Rule::in(array_keys(Enum::SEO_PACKAGES))],
            'start_date' => 'required|date',
            'renewal_date' => 'required|date|after_or_equal:start_date',
            'status' => ['required', 'string', Rule::in(array_keys(Enum::SEO_STATUSES))],
        ]);
        try {
            $seo = SeoService::findOrFail($id);
            $seo->update($validated);
            $this->updateSeoStatus($seo->id);
            return redirect()->route('seo.list')->with('success', 'SEO Service updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Update failed: ' . $e->getMessage());
        }
    }
    
    public function updateSeoStatus($id)
    {
        $seo = SeoService::findOrFail($id);
        $renewalDate = \Carbon\Carbon::parse($seo->renewal_date);
        $now = \Carbon\Carbon::now();
        $daysUntilRenewal = $now->diffInDays($renewalDate, false);
        if ($renewalDate->isPast()) {
            $status = 'inactive';
        } elseif ($daysUntilRenewal >= 0 && $daysUntilRenewal <= 7) {
            $status = 'pending';
        } else {
            $status = 'active';
        }
        $seo->status = $status;
        $seo->save();
    }
    
    public function reactivate(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date',
            'renewal_date' => 'required|date|after_or_equal:start_date',
            'seo_package' => ['required', 'string', Rule::in(array_keys(Enum::SEO_PACKAGES))],
        ]);
    
        try {
            $seo = SeoService::findOrFail($id);
            $seo->start_date = $request->start_date;
            $seo->renewal_date = $request->renewal_date;
            $seo->seo_package = $request->seo_package;  // Save selected package
            $seo->save();
            $this->updateSeoStatus($seo->id);
            return redirect()->route('seo.list')->with('success', 'SEO plan reactivated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Failed to reactivate plan: ' . $e->getMessage());
        }
    }

    public function upcomingRenewals()
    {
        $upcomingPlans = SeoService::where('renewal_date', '>=', now())
            ->where('renewal_date', '<=', now()->addDays(7))
            ->whereNotNull('renewal_date')
            ->get();
        return view('seo.upcoming-renewals', compact('upcomingPlans'));
    }
    
    public function renewPlan(Request $request, $id)
    {
        $request->validate([
            'start_date' => 'required|date',
            'renewal_date' => 'required|date|after_or_equal:start_date',
            'seo_package' => ['required', 'string', Rule::in(array_keys(Enum::SEO_PACKAGES))],
        ]);
    
        $plan = SeoService::findOrFail($id);
        $plan->start_date = $request->start_date;
        $plan->renewal_date = $request->renewal_date;
        $plan->seo_package = $request->seo_package;  // Save selected package
        $plan->status = 'active';
        $plan->save();
        return redirect()->route('seo.list')->with('success', 'Plan renewed successfully.');
    }
    
    public function destroy($id)
    {
        try {
            $seo = SeoService::findOrFail($id);
            $seo->delete();
            return redirect()->route('seo.list')->with('success', 'SEO Service deleted successfully!');

        } catch (\Exception $e) {

            return back()->with('error', 'Delete failed: ' . $e->getMessage());

        }

    }

}

