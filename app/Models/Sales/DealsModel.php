<?php



namespace App\Models\Sales;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use App\Models\Sales\CurrencyModel;

use App\Models\Sales\OrganisationModel;

use App\Models\User;

use App\Models\Sales\DealsActivityModel;



class DealsModel extends Model

{

    use HasFactory;



    protected $table = 'deals';

    protected $with = ['currency', 'organisation_detail', 'owner_data', 'stage'];

    protected $appends = ['full_name'];

    protected $fillable = [

        'first_name',

        'last_name',

        'email',

        'phone',

        'title',

        'organisation',

        'close_date',

        'owner',

        'converted'

    ];



    public function getFullNameAttribute()

    {

        return $this->first_name.' '.$this->last_name;

    }

    public function currency()
    {
        return $this->belongsTo(CurrencyModel::class, 'currency', 'id');
    }


    // public function currency()

    // {

    //     return $this->hasOne(CurrencyModel::class, 'id', 'currency');

    // }



    public function organisation_detail()

    {

        return $this->hasOne(OrganisationModel::class, 'id', 'organisation');

    }



    public function owner_data()

    {

        return $this->belongsTo(User::class, 'owner', 'id');

    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner', 'id');
    }
    



    public function leads_activity()

    {

        return $this->hasMany(DealsActivityModel::class, 'id', 'lead_id');

    }



    public function products()

    {

        return $this->hasMany(ProductModel::class, 'deal_id', 'id');

    }



    public function stage()

    {

        return $this->belongsTo(DealStages::class, 'id', 'deal_id');

    }



    // Hosting details relationship

    public function hostingDetails()

    {

        return $this->hasMany(\App\Models\HostingDetails::class, 'client_id', 'id');

    }

    

    public function seoDetails()

    {

        return $this->hasMany(\App\Models\seoDetails::class, 'client_id', 'id');

    }





    public function activities()

    {

        return $this->hasMany(DealsActivityModel::class, 'deal_id', 'id')->where('done', 0)

                    ->orderBy('time_from', 'asc');

    }

}

