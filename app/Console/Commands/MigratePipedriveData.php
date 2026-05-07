<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Sales\LeadsModel;
use App\Models\Sales\LeadLabels;
use App\Models\Sales\LeadsActivityModel;
use App\Models\Sales\DealsModel;
use App\Models\Sales\LeadLabelList;
use App\Models\Sales\OrganisationModel;
use App\Models\User;
use App\Models\Sales\CurrencyModel;
use App\Models\Sales\DealsActivityModel;
use App\Models\Sales\DealStages;
use App\Models\Sales\DealStagesList;
use Illuminate\Support\Facades\Hash;

class MigratePipedriveData extends Command
{
    protected $signature = 'migrate:pipedrive';
    protected $description = 'Migrate leads, organizations, lead labels, and activities from Pipedrive to MySQL';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting migration from Pipedrive to MySQL...');
        $users = $this->fetchPipedriveData('users');
        $this->info('users');
        // foreach ($users as $user) {
        //     // $this->migrateUsers($user);
        // }
        // Fetch and migrate leads
        $leads = $this->fetchPipedriveData('leads');
        $this->info('leads');
        // foreach ($leads as $lead) {
        //     // $this->migrateLead($lead);
        // }
        // Fetch and migrate lead label
        $this->info('leads');
        foreach ($leads as $lead) {
            // $this->migrateLeadLabel($lead);
        }

        // Fetch and migrate lead activities
        $leadActivities = $this->fetchPipedriveData('activities'); dd(($leadActivities));
        $this->info('activities');
        foreach ($leadActivities as $activity) {
            $this->migrateLeadActivity($activity);
        }  

        // foreach ($leads as $lead) {
        //     $dealId = $lead['id'];
        //     $products = $this->fetchDealProducts($dealId);
        //     $this->info("Fetched products for deal ID {$dealId}");
            
        //     foreach ($products as $product) {
        //         $this->migrateDealProduct($product, $dealId);
        //     }
        // }
        
        // Fetch and migrate lead labels
        // $leadLabels = $this->fetchPipedriveData('leadLabels');
        // $this->info('leadLabels');
        // foreach ($leadLabels as $label) {
        //     // $this->migrateLeadLabelList($label);
        // }
        // Fetch and migrate lead activities
        // $leadActivities = $this->fetchPipedriveData('activities');
        // $this->info('activities');
        // foreach ($leadActivities as $activity) {
        //     $this->migrateLeadActivity($activity);
        // }
        // // Fetch and migrate organizations
        // $organisations = $this->fetchPipedriveData('organizations');
        // $this->info('organizations');
        // foreach ($organisations as $organisation) {
        //     // $this->migrateOrganisation($organisation);
        // }
        // $notes = $this->fetchPipedriveData('notes');
        // foreach ($notes as $note) {
        //     // Only save if note belongs to a lead
        //     if (!empty($note['lead_id'])) {
        //         $this->migrateLeadActivityFromNote($note);
        //     }
        // }
        $this->info('Migration completed!');
    }

    // private function fetchDealProducts($dealId)
    // {
    //     $url = env('PIPEDRIVE_API_URL') . "deals/{$dealId}/products";
    //     $apiKey = env('PIPEDRIVE_API_KEY');

    //     $response = Http::get($url, [
    //         'api_token' => $apiKey,
    //     ]);

    //     return $response->json()['data'] ?? [];
    // }

    // private function fetchDealActivities($dealId)
    // {
    //     $url = env('PIPEDRIVE_API_URL') . 'activities';
    //     $apiKey = env('PIPEDRIVE_API_KEY');
        
    //     // Make API request to fetch activities associated with the deal
    //     $response = Http::get($url, [
    //         'api_token' => $apiKey,
    //         'deal_id' => $dealId,  // Specify the deal ID to fetch its activities
    //     ]);
        
    //     return $response->json()['data'] ?? [];
    // }
    private function fetchPipedriveData($type)
    {
        $allData = [];
        $url = env('PIPEDRIVE_API_URL') . $type;
        $apiKey = env('PIPEDRIVE_API_KEY');
        $start = 0;
        $limit = 100; // Pipedrive default limit

        do {
            $response = Http::get($url, [
                'api_token' => $apiKey,
                'start' => $start,
                'limit' => $limit
            ]);

            $data = $response->json()['data'] ?? [];
            $moreItems = $response->json()['additional_data']['pagination']['more_items_in_collection'] ?? false;
            $start = $response->json()['additional_data']['pagination']['next_start'] ?? null;

            $allData = array_merge($allData, $data);

        } while ($moreItems && $start !== null);

        return $allData;
    }

    // private function migrateOrganisation($organisation)
    // {
    //     OrganisationModel::updateOrCreate(
    //         ['id' => $organisation['id']],
    //         [
    //             'name' => $organisation['name'] ?? null,
    //             'address' => $organisation['address'] ?? null,
    //             'phone' => $this->extractPrimaryContact($organisation['phone'] ?? []),
    //             'email' => $this->extractPrimaryContact($organisation['email'] ?? []),
    //             'website' => $organisation['website'] ?? null,
    //             'owner_id' => $organisation['owner_id'] ?? null,
    //         ]
    //     );
    // }
    // private function migrateLead($lead)
    // {
    //     $person = $this->fetchPersonDetails($lead['person_id']);
    //     // $ownerId = $this->getOrCreateOwner($lead['owner_id']);
    //     $currencyId = $this->getOrCreateCurrency(isset($lead['currency']) ? $lead['currency'] : 1);
    //     $organisationId = $this->getOrCreateOrganisation($lead['organization_id'] ?? null);
    //     LeadsModel::updateOrCreate(
    //         [
    //             'lead_id' => $lead['id'],
    //             'first_name' => $person['first_name'] ?? null,
    //             'last_name' => $person['last_name'] ?? null,
    //             'email' => $this->extractPrimaryContact($person['email'] ?? []),
    //             'phone' => $this->extractPrimaryContact($person['phone'] ?? []),
    //             'title' => $lead['title'] ?? null,
    //             'organisation' => $organisationId,
    //             'close_date' => $lead['close_date'] ?? null,
    //             'owner' => $lead['owner_id'],
    //             'converted' => $lead['converted'] ?? 0,
    //             'currency' => 1,
    //         ]
    //     );
    // }
    // private function migrateLeadLabelList($label)
    // {
    //     LeadLabelList::updateOrCreate(
    //         [
    //             'lead_label_id' => $label['id'] ?? null,
    //             'name' => $label['name'] ?? null,
    //             'status' => $label['status'] ?? 1,
    //             'bg_color' => $label['color'] ?? null,
    //             'font_color' => $label['font_color'] ?? null,
    //         ]
    //     );
    // }
    // private function migrateLeadLabel($lead)
    // {
    //     if (!empty($lead['label_ids']) && is_array($lead['label_ids'])) {
    //         foreach ($lead['label_ids'] as $labelId) {
    //             LeadLabels::updateOrCreate(
    //                 [
    //                     'lead_uuid' => $lead['id'], // foreign key of leads table
    //                     'lead_label_uuid' => $labelId, // foreign key of lead_label_lists table
    //                 ]
    //             );
    //         }
    //     }
    // }
    // private function migrateLeadActivityFromNote($note)
    // {
    //     LeadsActivityModel::updateOrCreate(
    //         ['id' => $note['id']],
    //         [
    //             'lead_uuid' => $note['lead_id'],
    //             'type' => 6, // note
    //             'title' => 'Note',
    //             'description' => strip_tags($note['content'] ?? ''), 
    //             'time_from' => $note['add_time'] ?? now(),
    //             'time_to' => null,
    //             'done' => 1,
    //             'status' => 1,
    //         ]
    //     );
    // }

    // private function migrateLeadActivity($activity)
    // {
    //     $typeMap = [
    //         'call' => 1,
    //         'meeting' => 2,
    //         'task' => 3,
    //         'deadline' => 4,
    //         'email' => 5,
    //         'note' => 6,
    //     ];

    //     $mappedType = $typeMap[$activity['type']] ?? null;

    //     LeadsActivityModel::updateOrCreate(
    //         ['id' => $activity['id']],
    //         [
    //             'lead_uuid' => $activity['lead_id'],
    //             'type' => $mappedType,
    //             'title' => $activity['subject'] ?? null,
    //             'description' => $activity['public_description'] ?? null,
    //             'time_from' => $activity['due_date'] ?? null,
    //             'time_to' => $activity['time_to'] ?? null,
    //             'done' => $activity['done'] ?? 0,
    //             'status' => $activity['status'] ?? 0,
    //         ]
    //     );
    // }

    private function fetchPersonDetails($personId)
    {
        if (!$personId) {return [];}
        $url = env('PIPEDRIVE_API_URL') . 'persons/' . $personId;
        $apiKey = env('PIPEDRIVE_API_KEY');
        $response = Http::get($url, [
            'api_token' => $apiKey,
        ]);
        return $response->json()['data'] ?? [];
    }
    private function getOrCreateOwner($ownerId)
    {
        if (!$ownerId || !User::where('id', $ownerId)->exists()) {
            return User::first()->id ?? 1;
        }
        return $ownerId;
    }
    // private function getOrCreateCurrency($currencyId)
    // {
    //     if (!$currencyId || !CurrencyModel::where('id', $currencyId)->exists()) {
    //         return 1;
    //     }
    //     return $currencyId;
    // }
    // private function getOrCreateOrganisation($organisationId)
    // {
    //     if (!$organisationId) {
    //         return null;
    //     }
    //     $organisation = OrganisationModel::where('id', $organisationId)->first();
    //     if (!$organisation) {
    //         // Fetch organisation details from Pipedrive
    //         $orgDetails = $this->fetchPipedriveData('organizations/' . $organisationId);
    //         if (!$orgDetails) {
    //             return null;
    //         }
    //         // Create new organisation if it doesn't exist
    //         $organisation = OrganisationModel::create([
    //             'id' => $organisationId,
    //             'name' => $orgDetails['name'] ?? null,
    //             'address' => $orgDetails['address'] ?? null,
    //             'phone' => $this->extractPrimaryContact($orgDetails['phone'] ?? []),
    //             'email' => $this->extractPrimaryContact($orgDetails['email'] ?? []),
    //             'website' => $orgDetails['website'] ?? null,
    //             'owner_id' => $orgDetails['owner_id'] ?? null,
    //         ]);
    //     }
    //     return $organisation->id;
    // }
    // private function checkLeadExists($lead_id, $leads)
    // { 
    //     $exists = false;
    //     foreach ($leads as $lead) {
    //         if ($lead_id == $lead['person_id']) {
    //             $exists = true;
    //             break;
    //         }
    //     }
    //     return $exists;
    // }
    private function migrateDealProduct($product, $dealId)
    {
        \DB::table('products')->updateOrInsert(
            [
                'id' => $product['id'],
            ],
            [
                'name' => $product['name'] ?? null,
                'price' => $product['item_price'] ?? 0,
                'quantity' => $product['quantity'] ?? 1,
                'tax' => $product['tax'] ?? 0,
                'tax_amount' => $product['sum_tax'] ?? 0,
                'total_without_tax' => $product['sum_no_tax'] ?? 0,
                'total_with_tax' => $product['sum'] ?? 0,
                'deal_id' => $dealId,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function migrateLeadActivity($activity)
    {
        $typeMap = [ 'call' => 1, 'meeting' => 2, 'task' => 3, 'deadline' => 4, 'email' => 5, 'note' => 6, ];
        $mappedType = $typeMap[$activity['type']] ?? null;

        LeadsActivityModel::updateOrCreate(
            ['id' => $activity['id']],
            [
                'lead_uuid' => $activity['lead_id'],
                'type' => $mappedType,
                'title' => $activity['subject'] ?? null,
                'description' => $activity['public_description'] ?? null,
                'time_from' => $activity['due_date'] ?? null,
                'time_to' => $activity['time_to'] ?? null,
                'done' => $activity['done'] ?? 0,
                'status' => $activity['status'] ?? 0,
            ]
        );
    }

} 