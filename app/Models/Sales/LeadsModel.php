<?php

namespace App\Models\Sales;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Sales\CurrencyModel;
use App\Models\Sales\OrganisationModel;
use App\Models\User;
use App\Models\Sales\LeadLabels;
use App\Models\Sales\LeadLabelList;
use App\Models\Sales\LeadsActivityModel;

class LeadsModel extends Model
{
    use HasFactory;

    protected $table = 'leads';
    // protected $with = ['currency', 'organisation_detail', 'owner_data', 'labels'];
    protected $append = [
        'full_name',
        'next_activity'
    ];
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
        return $this->hasOne(CurrencyModel::class, 'id', 'currency');
    }

    public function organisation_detail()
    {
        return $this->hasOne(OrganisationModel::class, 'id', 'organisation');
    }

    public function owner_data()
    {
        return $this->belongsTo(User::class, 'owner', 'id');
    }

    public function getLabelsAttribute()
    {
        return LeadLabels::where('lead_id', $this->id)->with('label')->get();
    }

    public function labels()
    {
        // return $this->hasManyThrough(LeadLabelList::class, LeadLabels::class, 'lead_label', 'id', 'id', 'lead_id');
        return $this->hasMany(LeadLabels::class, 'lead_id');
    }

    public function leads_activity()
    {
        return $this->hasMany(LeadsActivityModel::class, 'id', 'lead_id');
    }

    public function getNextActivityAttribute()
    {
        return LeadsActivityModel::where('lead_id', $this->id)->where('done', 0)->orderBy('time_from', 'asc')->first();
    }
}
