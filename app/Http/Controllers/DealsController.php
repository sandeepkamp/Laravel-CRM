<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\GroupUsers;
use Illuminate\Http\Request;
use App\Models\Sales\LeadsModel;
use App\Models\Sales\LeadsActivityModel;
use App\Models\Sales\DealsModel;
use App\Models\User;
use App\Models\Sales\OrganisationModel;
use App\Models\Sales\ProductModel;
use App\Models\Sales\DealSubscription;
use App\Models\Sales\DealsActivityModel;
use App\Models\Sales\DealStages;
use App\Models\Sales\DealStagesList;
use Illuminate\Support\Facades\Validator;
use DocuSign\eSign\Configuration;
use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Client\ApiException;
use DocuSign\eSign\Model\Document;
use DocuSign\eSign\Model\SignHere;
use DocuSign\eSign\Model\DateSigned;
use DocuSign\eSign\Model\FullName;
use DocuSign\eSign\Model\Tabs;
use DocuSign\eSign\Model\Signer;
use DocuSign\eSign\Model\Recipients;
use DocuSign\eSign\Model\InlineTemplate;
use DocuSign\eSign\Model\CompositeTemplate;
use DocuSign\eSign\Model\EnvelopeDefinition;
use Illuminate\Support\Carbon;
use Auth;
use App\Rules\ValidWebsite;
use Yajra\DataTables\Facades\DataTables;

class DealsController extends SalesController
{
    // New updated code for user full name and search
    public function index(Request $request)
    {
        $logedInUserId = auth()->user()->id;

        if ($request->ajax()) {
            $baseQuery = DealsModel::with(['activities', 'stage', 'currency', 'owner_data', 'organisation_detail'])
                ->leftJoin('organisations', 'organisations.id', '=', 'deals.organisation')
                ->addSelect(['deals.*', 'organisations.name as organisation_name',
                    'next_activity_time' => \DB::table('deals_activity')->selectRaw('MIN(time_from)')
                    ->whereColumn('deal_id', 'deals.id')
                    ->where('done', 0)
                ]);
        
            if (auth()->user()->role != 1) {
                $baseQuery->where('owner', $logedInUserId)->where(function ($query) {
                    $query->where('visible_to', auth()->user()->id)->orWhereNull('visible_to');
                });
            }
        
            // Apply sorting: 
            // First, prioritize "today's activities" by checking if next_activity_time is today's date
            if (!request()->has('order')) {
                // First, order by whether the next activity is today
                $baseQuery->orderByRaw('DATE(next_activity_time) = CURDATE() DESC')
                          ->orderByRaw('next_activity_time IS NULL, next_activity_time ASC');
            }
        
            // Filter by user ID if provided
            if ($user_id = $request->get('user_id')) {
                $baseQuery->where('owner', $user_id);
            }
        
            return DataTables::of($baseQuery)
                ->addColumn('title', function ($deal) {
                    $orgName = $deal->organisation_detail->name ?? 'N/A';
                    return '<a href="' . route('deal.show', $deal->id) . '">' . $orgName . '</a>';
                })
                ->filterColumn('title', function ($query, $keyword) {
                    $query->where(function ($q) use ($keyword) {
                        // Filter by organisation name
                        $q->whereHas('organisation_detail', function($subQ) use ($keyword) {
                            $subQ->where('name', 'like', "%{$keyword}%");
                        });
                        // OR filter by owner name (from owner_data relationship)
                        $q->orWhereHas('owner_data', function($subQ) use ($keyword) {
                            $subQ->where('first_name', 'like', "%{$keyword}%")
                                 ->orWhere('last_name', 'like', "%{$keyword}%");
                        });
                    });
                })
                ->addColumn('latest_activity', function ($deal) {
                    // Existing logic for handling activity display
                    $activity = $deal->activities->first();
                    if ($activity) {
                        if ($activity->done == 1) {
                            $type = __('Completed');
                            $colorClass = 'bg-success';
                        } else {
                            $type = match ($activity->type) {
                                1 => __('Call'), 2 => __('Meeting'), 3 => __('Task'),
                                4 => __('Deadline'), 5 => __('Email'), 6 => __('Note'),
                                7 => __('Files'), 8 => __('DocuSign'), default => __('No activity')
                            };
                            $colorClass = match ($activity->type) {
                                1 => 'bg-info', 2 => 'bg-success', 3 => 'bg-warning',
                                4 => 'bg-danger', 5 => 'bg-primary', 6 => 'bg-secondary',
                                7 => 'bg-dark', 8 => 'bg-muted', default => 'bg-label-light'
                            };
                        }
        
                        $date = \Carbon\Carbon::parse($activity->time_from)->format('d.m.Y');
                        $time = \Carbon\Carbon::parse($activity->time_from)->format('g:i A');
                        $today = now()->format('d.m.Y');
                        $yesterday = now()->subDay()->format('d.m.Y');
                        $tomorrow = now()->addDay()->format('d.m.Y');
                        $weekAhead = now()->addWeek()->format('d.m.Y');
                        $weekAgo = now()->subWeek()->format('d.m.Y');
                        $dayDiff = now()->diffInDays($activity->time_from, false);
        
                        if ($date === $today) {
                            $date_label = 'Today' . ($time !== '12:00 AM' ? " at $time" : '');
                            $dateClass = 'text-success';
                        } elseif ($date === $yesterday) {
                            $date_label = 'Yesterday' . ($time !== '12:00 AM' ? " at $time" : '');
                            $dateClass = 'text-danger';
                        } elseif ($date === $tomorrow) {
                            $date_label = 'Tomorrow' . ($time !== '12:00 AM' ? " at $time" : '');
                            $dateClass = 'text-muted';
                        } elseif ($dayDiff > 1 && $date <= $weekAhead) {
                            $date_label = \Carbon\Carbon::parse($activity->time_from)->format('D, d.m.Y') . ($time !== '12:00 AM' ? " at $time" : '');
                            $dateClass = 'text-muted';
                        } elseif ($dayDiff > 7) {
                            $date_label = \Carbon\Carbon::parse($activity->time_from)->format('D, d.m.Y') . ($time !== '12:00 AM' ? " at $time" : '');
                            $dateClass = 'text-muted';
                        } elseif ($dayDiff < 0 && $date >= $weekAgo) {
                            $date_label = \Carbon\Carbon::parse($activity->time_from)->format('D, d.m.Y') . ($time !== '12:00 AM' ? " at $time" : '');
                            $dateClass = 'text-danger';
                        } elseif ($dayDiff < -7) {
                            $date_label = \Carbon\Carbon::parse($activity->time_from)->format('D, d.m.Y') . ($time !== '12:00 AM' ? " at $time" : '');
                            $dateClass = 'text-danger';
                        } else {
                            $date_label = \Carbon\Carbon::parse($activity->time_from)->format('D, Y-m-d');
                            $dateClass = 'text-muted';
                        }
        
                        return "<span class='badge {$colorClass}'>{$type}</span><br><small class='{$dateClass}'>{$date_label}</small>";
                    }
        
                    return '<span class="badge bg-dark">' . __('No activity') . '</span>';
                })
                ->addColumn('stage', function ($deal) {
                    // Existing logic for stage display
                    $stage = $deal->stage->stage_data;
                    $colorClass = $stage->status == 0 ? 'bg-muted' : match (strtolower($stage->name)) {
                        'prospects' => 'bg-label-danger',
                        'qualified' => 'bg-label-warning',
                        'contact made' => 'bg-label-info',
                        'demo scheduled' => 'bg-label-primary',
                        'proposal made' => 'bg-label-success',
                        'negotiations started' => 'bg-label-danger',
                        default => 'bg-label-light'
                    };
                    return '<span class="badge rounded-pill ' . $colorClass . '">' . $stage->name . '</span>';
                })
                ->addColumn('owner', function ($deal) {
                    return $deal->owner_data ? $deal->owner_data->first_name . ' ' . $deal->owner_data->last_name : '';
                })
                ->addColumn('deal_value', function ($deal) {
                    $symbol = $deal->currency->symbol ?? '';
                    return $deal->value ? $symbol . number_format($deal->value, 2) : '-';
                })
                ->addColumn('actions', function ($deal) {
                    $edit = '<a class="dropdown-item" href="' . route('deal.edit', $deal->id) . '"><i class="mdi mdi-pencil-outline me-1"></i>Edit</a>';
                    $delete = '<form method="POST" action="' . route('deal.delete', $deal->id) . '" onsubmit="return confirmDelete();">'
                        . csrf_field() . method_field('DELETE') . '
                        <button class="dropdown-item" type="submit"><i class="mdi mdi-trash-can-outline me-1"></i>Delete</button>
                      </form>';
                    return '<div class="dropdown">
                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                    <i class="mdi mdi-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">' . $edit . $delete . '</div>
                            </div>';
                })
                ->orderColumn('title', function ($query, $order) {
                    $query->orderBy('organisation_name', $order);
                })
                ->orderColumn('deal_value', function ($query, $order) {
                    $query->orderBy('deals.value', $order);
                })
                ->orderColumn('latest_activity', function ($query, $order) {
                    $query->orderBy('next_activity_time', $order);
                })
                ->rawColumns(['title', 'latest_activity', 'stage', 'actions'])
                ->make(true);
        }
        
        // Non-AJAX view return (bottom part)
        if (auth()->user()->role == 1) {
            $totalDeals = DealsModel::count();
            $totalDealValue = DealsModel::sum('value');
            $currencySymbol = DealsModel::with('currency')->first()?->currency?->symbol ?? '£';
        } else {
            $totalDeals = DealsModel::where('owner', $logedInUserId)->count();
            $totalDealValue = DealsModel::where('owner', $logedInUserId)->sum('value');
            $currencySymbol = DealsModel::with('currency')->where('owner', $logedInUserId)->first()?->currency?->symbol ?? '£';
        }
        return view('sales.deals.index', [
            'stages' => DealStagesList::get(),
            'totalDeals' => $totalDeals,
            'totalDealValue' => $totalDealValue,
            'currencySymbol' => $currencySymbol,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $childs = [];
        $owners = Auth::user()->all_user();
        return view('sales.deals.add',['childs' => $childs, 'owners' =>$owners,'organizations'=>OrganisationModel::all(),'stages' => DealStagesList::get(),'users' => User::whereIn('role', [1,2])->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd(5);
         $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            // 'last_name' => 'required|max:255',
            'email' => 'required|email:rfc,dns|max:255',
            'company_name' => 'required',
            'website' => ['nullable', new ValidWebsite()],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $org = OrganisationModel::where('name', 'like', $request->input('company_name', ''))->first();
            if(empty($org)){
                $org = new OrganisationModel;
                $org->name = $request->input('company_name', '');
                $org->save();
            }
            
            $deal = new DealsModel;
            $deal->first_name = $request->input('first_name', '');
            $deal->last_name = $request->input('last_name', '');
            $deal->email = $request->input('email', '');
            $deal->organisation = $org->id;
            $deal->website = $request->input('website', '');
            // $deal->visible_to = $request->input('visible_to', '');
            $deal->phone = $request->input('phone_number', '');
            $deal->title = $request->input('title', '');
            $deal->value = $request->input('deal_value', '');
            $deal->currency = $request->input('currency', '');
            $deal->close_date = date('Y-m-d', strtotime($request->input('close_date', '')));
            $deal->owner = $request->input('owner', $request->user()->id);
            $deal->converted = 0;
            $deal->created_at = now();
            $deal->updated_at = now();
            $deal->save();

            $dealstage = new DealStages;
            $dealstage->deal_id = $deal->id;
            $dealstage->stage = $request->input('stage', '');
            $dealstage->created_at = now();
            $dealstage->updated_at = now();
            $dealstage->save();

            if($request->addmore){
            
            foreach ($request->addmore as $key => $value) {
                    $data = [
                        'name'=>$value['name'],
                        'price'=>$value['price'],
                        'quantity'=>$value['quantity'],
                        'tax'=>$value['tax'],
                        'tax_amount'=>$value['tax']/100,
                        'total_without_tax'=>$value['price']*$value['quantity'],
                        'total_with_tax'=>$value['price']+($value['price']*$value['quantity']*$value['tax']),
                        'deal_id'=>$deal->id,
                    ];

                    $product = ProductModel::create($data);

                    $data1 = [
                        'product_id'=>$product->id,
                        'start_date'=>date('Y-m-d 00:00:00', strtotime($value['start_date'])),
                        'end_date'=>date('Y-m-d 11:59:59', strtotime($value['end_date'])),
                        'created_at'=>now(),
                        'updated_at'=>now(),
                    ];

                    DealSubscription::create($data1);
            }
            
            }

             return redirect(route('deal.list'))->withSuccess('Deals has been created successfully');
        }
    }

    /**
     * Display the specified resource.
    */
    public function show(string $id)
    {
        $deal = DealsModel::where('id', $id)->first();
        
        if (!$deal) {
            return redirect(route('deal.list'))->withError('Deal not found');
        }
    
        // Fetch Lead Activities from 'leads_activity' table
        $leadActivities = LeadsActivityModel::where('lead_id', $deal->lead_id)
            ->orderBy('time_from', 'asc')
            ->get();
        // Fetch Deal Activities from 'deals_activity' table
        $activity = DealsActivityModel::where('deal_id', $id)
            ->where('done', 0)
            ->orderBy('time_from', 'asc')
            ->get();
    
        // Fetch Done Activities (Completed)
        $doneActivity = DealsActivityModel::where('deal_id', $id)
            ->where('done', 1)
            ->orderBy('time_from', 'desc')
            ->get();
    
        return view('sales.deals.detail', [
            'persons' => User::whereIn('role', [1, 2])->get(),
            'deal' => $deal,
            'leadActivities' => $leadActivities, // Pass Lead Activities
            'activity' => $activity, // Pass Deal Activities
            'done_activity' => $doneActivity,
            'stages' => DealStagesList::all(),
            'update' => '',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $deal = DealsModel::with('products.subscription', 'stage')->where('id', $id)->first();
        $owners = User::whereIn('role', [1,2])->get();  
        $users = User::whereIn('role', [1,2])->get(); 
        $stages = DealStagesList::all(); // Ensure this is fetching the list of available stages.
    
        return view('sales.deals.edit', compact('owners', 'users', 'deal', 'stages'));
    }

    public function updateCloseDate(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'close_date' => 'required|date',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        $deal = DealsModel::where('id', $id)->first();
        if (!$deal) {
            return redirect()->back()->withError('Deal not found');
        }
    
        $deal->close_date = date('Y-m-d', strtotime($request->input('close_date')));
        $deal->updated_at = now();
        $deal->save();
    
        return redirect()->route('deal.show', ['deal' => $id])->withSuccess('Expected close date has been updated successfully');
    }
    
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // return $request;
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            // 'last_name' => 'required|max:255',
            'email' => 'required|email:rfc,dns|max:255',
            'company_name'=>'required',
            'website' => 'nullable|url', 
            'deal_value'=>'required',
            // 'close_date'=>'required',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }else{
            $org = OrganisationModel::where('name', 'like', $request->input('company_name', ''))->first();
            if(empty($org)){
                $org = new OrganisationModel;
                $org->name = $request->input('company_name', '');
                $org->save();
            }
            $deal = DealsModel::where('id', $id)->first();
            $deal->first_name = $request->input('first_name', '');
            $deal->last_name = $request->input('last_name', '');
            $deal->email = $request->input('email', '');
            $deal->organisation = $org->id;
            $deal->website = $request->input('website', '');
            // $deal->visible_to = $request->input('visible_to', '');
            $deal->phone = $request->input('phone_number', '');
            $deal->title = $request->input('title', '');
            $deal->value = $request->input('deal_value', '');
            $deal->currency = $request->input('currency', '');
            $deal->close_date = date('Y-m-d', strtotime($request->input('close_date', '')));
            // $deal->owner = $request->input('owner', $request->user()->id);
            $deal->converted = 0;
            $deal->created_at = now();
            $deal->updated_at = now();
            $deal->save();
            // $dealstage = DealStages::where('deal_id', $id)->first();
            // $dealstage->stage = $request->input('stage', '');
            // $dealstage->created_at = now();
            // $dealstage->updated_at = now();
            // $dealstage->save();
            DealStages::where('deal_id', $id)->update([
                'stage' => $request->input('stage', ''),
                'updated_at' => now()
            ]);
            if($request->addmore){

                ProductModel::where('deal_id',$deal->id)->delete();

                foreach ($request->addmore as $key => $value) {
                    $data = [
                        'name'=>$value['name'],
                        'price'=>$value['price'],
                        'quantity'=>$value['quantity'],
                        'tax'=>$value['tax'],
                        'tax_amount'=>$value['price']*$value['tax']+$value['quantity'],
                        'total_without_tax'=>$value['price']*$value['quantity'],
                        'total_with_tax'=>$value['price']+($value['price']*$value['quantity']*$value['tax']),
                        'deal_id'=>$deal->id,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];

                    $product = ProductModel::create($data);

                    DealSubscription::where('product_id', $product->id)->delete();
                    DealSubscription::create([
                        'product_id' => $product->id,
                        'start_date' => date('Y-m-d 00:00:00', strtotime($value['start_date'])),
                        'end_date' => date('Y-m-d 11:59:59', strtotime($value['end_date'])),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            // return redirect()->back()->withSuccess('Deals has been updated successfully');
            return redirect()->route('deal.show', ['deal' => $id])->withSuccess('Deal has been updated successfully');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $deal = DealsModel::where('id', $id)->first();
        if($deal){
            $products = ProductModel::where('deal_id', $id)->get();
            if($products){
                foreach($products as $product){
                    DealSubscription::where('product_id', $product->id)->delete();
                    $product->delete();
                }
            }
            DealStages::where('deal_id', $id)->delete();
            $deal->delete();
            return redirect()->back()->withSuccess('Deal has been deleted');
        }else{
            return redirect()->back()->withError('Deal not found');
        }
    }

    public function transfer_owner(Request $request)
    {
        $request->validate([
            'deal_id' => 'required',
            'user_id' => 'required',
        ]);
        // if($request->deal)
        // first check if both the ids are present or not in the table 
        $user = User::find($request->user_id);
        $deal = DealsModel::find($request->deal_id);
        if($user == null || $deal == null)
        {
            return  redirect()->back()->withErrors('Deal or User not found');
        }
        $deal->owner = $user->id;
        $deal->save();
        return redirect()->back()->withSuccess('Ownership has been transfered.');
    }

    public function createdocusigntoken(ApiClient $apiClient){
        $apiClient = new ApiClient();
        $apiClient->getOAuth()->setOAuthBasePath(env('DS_AUTH_SERVER'));
        $accessToken = $this->getToken($apiClient);
        $userInfo = $apiClient->getUserInfo($accessToken);
        $accountInfo = $userInfo[0]->getAccounts();
        return response()->json(['accessToken'=>$accessToken,'accountid'=>$accountInfo[0]->getAccountId()]); 
    }

     private function getToken(ApiClient $apiClient) : string{
        try {
            $privateKey = file_get_contents(storage_path(env('DS_KEY_PATH')),true);
            $response = $apiClient->requestJWTUserToken(
                $ikey = env('DS_CLIENT_ID'),
                $userId = env('DS_IMPERSONATED_USER_ID'),
                $key = $privateKey,
                $scope = env('DS_JWT_SCOPE')
            );
            $token = $response[0];
            $accessToken = $token->getAccessToken();
        } catch (\Throwable $th) {
            throw $th;
        }  
        return $accessToken;        
    }
    
    public function sendenvelope(Request $request){
        $activity = new DealsActivityModel;
        $activity->deal_id = 10;
        $activity->type = 1;
        $activity->title = "Docusign";
        $activity->description = "Docusign file sent";
        $activity->envelop_id = $request->data['envelopeId'];
        $activity->time_from= Carbon::now('Europe/London');
        $activity->created_at= Carbon::now('Europe/London');
        $activity->updated_at= Carbon::now('Europe/London');
        $activity->save();
        // \Log::info($request);
    }
     public function getProduct($id){
        $deal = DealsModel::find($id);
        $view = view('partials.product')->with('deal',$deal);
        return $view->render(); 
    }
    public function addProduct(Request $request){
        // return $request->data;
        $product = [];
        ProductModel::where('deal_id',$request->deal_id)->delete();
        $amount = 0;
        foreach($request->data as $key=>$value){
            $data = [
                'name' => $value['name'],
                'price' => $value['price'],
                'quantity' => $value['quantity'],
                'tax' => $value['tax'],
                'tax_amount' => $value['tax']/100,
                'total_without_tax' => $value['price'] * $value['quantity'], // Total price without tax
                'total_with_tax' => ($value['price'] * $value['quantity']) + (($value['price'] * $value['quantity']) * $value['tax'] / 100), // Total price with tax
                'deal_id' => $request->deal_id,
            ];

            $amount += ($value['price'] * $value['quantity']) + (($value['price'] * $value['quantity']) * $value['tax'] / 100);
            $product = ProductModel::create($data);
            $data1 = [
                'product_id'=>$product->id,
                'start_date'=>date('Y-m-d 00:00:00', strtotime($value['start_date'])),
                'end_date'=>date('Y-m-d 11:59:59', strtotime($value['end_date'])),
                'created_at'=>now(),
                'updated_at'=>now(),
            ];
            DealSubscription::create($data1);
        }
        DealsModel::where(['id'=>$request->deal_id])->update(['value'=>$amount]);
        // Flash a success message
        session()->flash('success', 'Product details updated successfully!');
        return response()->json(['code'=>true]);
    }
    
    public function getNotification(){
        return view('notification');
    }
    
    // Update stage only
    public function updateStage(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'deal_id' => 'required|exists:deals,id',
            'stage_id' => 'required|exists:deals_stages_list,id', // Ensure the validation points to 'deals_stages_list'
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    
        // Fetch the deal and the stage
        $deal = DealsModel::find($request->deal_id);
        $stage = DealStagesList::find($request->stage_id); // Corrected to fetch from 'deals_stages_list'
    
        if ($deal && $stage) {
            // Update the stage_id in the deals_stages table
            $dealStage = $deal->stage; // Fetch the existing stage for the deal
    
            // Update the 'stage' field with the new stage_id
            $dealStage->stage = $request->stage_id;
            $dealStage->save();
    
            // Return to the deal page with a success message
            return redirect()->route('deal.show', ['deal' => $deal->id])
                ->withSuccess('Stage updated successfully!');
        }
    
        // Return an error if the deal or stage is not found
        return redirect()->back()->withErrors('Deal or Stage not found.');
    }
    
    private function getBadgeClass($stageId)
    {
        // Map stage ID to the corresponding badge class
        switch ($stageId) {
            case 1:
                return 'bg-label-danger';
            case 2:
                return 'bg-label-warning';
            case 3:
                return 'bg-label-info';
            case 4:
                return 'bg-label-primary';
            case 5:
                return 'bg-label-success';
            default:
                return 'bg-label-default';
        }
    }

}
