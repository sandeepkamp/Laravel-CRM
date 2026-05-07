<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ImportPipedriveDealProducts extends Command
{
    protected $signature = 'pipedrive:import-products';
    protected $description = 'Import products for each deal from Pipedrive API into the local products table';

    public function handle()
    {
        $apiKey = env('PIPEDRIVE_API_KEY');
        $baseUrl = 'https://api.pipedrive.com/v1';

        $this->info("Fetching deals...");

        $deals = Http::get("$baseUrl/deals", [
            'api_token' => $apiKey,
            'limit' => 100
        ])->json()['data'] ?? [];

        if (empty($deals)) {
            $this->warn("No deals found.");
            return;
        }

        foreach ($deals as $deal) {
            $dealId = $deal['id'];
            $this->info("Fetching products for deal ID: $dealId");

            $products = Http::get("$baseUrl/deals/{$dealId}/products", [
                'api_token' => $apiKey
            ])->json()['data'] ?? [];

            foreach ($products as $product) {
                DB::table('products')->updateOrInsert(
                    [
                        'deal_id'    => $dealId,
                        'name'       => $product['name'] ?? '',
                    ],
                    [
                        'price'             => $product['item_price'] ?? 0,
                        'quantity'          => $product['quantity'] ?? 1,
                        'tax'               => $product['tax'] ?? 0,
                        'tax_amount'        => $product['tax_value'] ?? 0,
                        'total_without_tax' => $product['sum_no_tax'] ?? 0,
                        'total_with_tax'    => $product['sum'] ?? 0,
                        'created_at'        => now(),
                        'updated_at'        => now(),
                    ]
                );
            }
        }

        $this->info("Products imported into 'products' table.");
    }
}
