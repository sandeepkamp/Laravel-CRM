<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ImportLeadActivitiesFromPipedrive extends Command
{
    protected $signature = 'import:pipedrive-lead-activities';
    protected $description = 'Import lead activities and notes from Pipedrive API';

    public function handle()
    {
        $apiKey = env('PIPEDRIVE_API_KEY');
        $baseUrl = env('PIPEDRIVE_API_URL');
        $startLeads = 0;
        $hasMoreLeads = true;

        while ($hasMoreLeads) {
            $response = Http::get("{$baseUrl}leads", [
                'api_token' => $apiKey,
                'start' => $startLeads,
                'limit' => 100,
            ]);

            if ($response->failed()) {
                $this->error('Failed to fetch leads');
                return;
            }

            $leads = $response->json()['data'] ?? [];

            foreach ($leads as $lead) {
                $leadId = $lead['id'];
                $dealId = $lead['deal_id'] ?? null;
                $personId = $lead['person_id'] ?? null;

                // Fetch Activities
                $activitiesResponse = Http::get("{$baseUrl}leads/{$leadId}/activities", [
                    'api_token' => $apiKey
                ]);

                if ($activitiesResponse->successful()) {
                    $activities = $activitiesResponse->json()['data'] ?? [];

                    foreach ($activities as $activity) {
                        DB::table('leads_activity')->insert([
                            'lead_id' => $leadId,
                            'lead_uuid' => Str::uuid(),
                            'type' => $this->mapType($activity['type']),
                            'title' => $activity['subject'] ?? '',
                            'description' => $activity['note'] ?? '',
                            'time_from' => $activity['due_date'] ? Carbon::parse($activity['due_date']) : null,
                            'time_to' => $activity['due_date'] ? Carbon::parse($activity['due_date'])->addHour() : null,
                            'done' => $activity['done'] ? 1 : 0,
                            'status' => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }

                // Fetch Notes by deal_id or person_id
                if ($dealId || $personId) {
                    $notesParams = array_filter([
                        'api_token' => $apiKey,
                        'deal_id' => $dealId,
                        'person_id' => $personId,
                    ]);

                    $notesResponse = Http::get("{$baseUrl}notes", $notesParams);

                    if ($notesResponse->successful()) {
                        $notes = $notesResponse->json()['data'] ?? [];

                        foreach ($notes as $note) {
                            DB::table('leads_activity')->insert([
                               'lead_id' => is_numeric($leadId) ? (int) $leadId : null,
                                'lead_uuid' => $leadId,
                                'type' => 6, // "Note" type
                                'title' => 'Note',
                                'description' => $note['content'] ?? '',
                                'time_from' => $note['add_time'] ? Carbon::parse($note['add_time']) : null,
                                'time_to' => null,
                                'done' => 1,
                                'status' => 0,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    }
                }
            }

            $pagination = $response['additional_data']['pagination'] ?? [];
            $hasMoreLeads = $pagination['more_items_in_collection'] ?? false;
            $startLeads = $pagination['next_start'] ?? null;
        }

        $this->info('All lead activities and notes imported successfully.');
    }

    private function mapType($type)
    {
        return match (strtolower($type)) {
            'call' => 1,
            'meeting' => 2,
            'task' => 3,
            'deadline' => 4,
            'email' => 5,
            'note' => 6,
            default => 3,
        };
    }
}
