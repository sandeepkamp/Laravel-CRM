<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales\CurrencyModel;
use Illuminate\Support\Facades\View;
use App\Models\Sales\LeadsModel;
use App\Models\Sales\DealsModel;
use Illuminate\Support\Carbon;
use DB;
use Auth;

class SalesController extends Controller
{
    protected $currency_id;
    protected $currency;

    public function __construct()
    {
        $this->currency_id = env("CURRENCY", 1); // default GBP
        $this->currency = CurrencyModel::where('id', $this->currency_id)->first();
        view()->share('global_currency', $this->currency);
    }
    
    public function sales_dash(){
        $leads = LeadsModel::all()->groupBy(function($val) {
            return Carbon::parse($val->created_at)->format('M-Y');
        });
        
        $condition_sql_lead = "";
        $condition_sql_deal = "";

        if(Auth::user()->role == 1){
            $condition_sql_lead .= " WHERE leads.converted=0";
            $condition_sql_deal .= "";
        }else if(Auth::user()->role == 2){
            $user_id = Auth::user()->id;
            $condition_sql_lead .= " WHERE leads.owner = $user_id AND MONTH(leads.created_at) = MONTH(CURRENT_DATE()) AND YEAR(leads.created_at) = YEAR(CURRENT_DATE()) AND leads.converted=0";
            $condition_sql_deal .= " WHERE deals.owner = $user_id";
        }

      $sales_person = DB::select("SELECT DISTINCT users.id, users.first_name, users.last_name FROM deals INNER JOIN users ON deals.owner = users.id;");
      $sales_count = DB::select("SELECT count(leads.id) as sales_count FROM leads $condition_sql_lead");
      $deals_count = DB::select("SELECT count(deals.id) as deals_count FROM deals $condition_sql_deal");
      $deals_count_prospects = DB::select("SELECT count(deals.id) as deals_count FROM deals INNER JOIN deals_stages ON deals_stages.deal_id =  deals.id $condition_sql_deal AND deals_stages.stage=1");
      $users_count = DB::select("SELECT count(users.id) as users_count FROM users");
      $product_revenue = DB::select("SELECT count(users.id) as users_count FROM users");
          $product_revenue = DB::select("SELECT SUM(products.total_with_tax) as total_with_tax  from products INNER JOIN deals ON deals.id = products.deal_id $condition_sql_deal");

        return view('sales.dashboard',['sales_person'=>$sales_person,'sales_count'=>$sales_count,'deals_count'=>$deals_count,'users_count'=>$users_count,'product_revenue'=>$product_revenue,'deals_count_prospects'=>$deals_count_prospects]);
    }
}
