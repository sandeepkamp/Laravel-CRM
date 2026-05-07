<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Models\Sales\DealsActivityModel;
use DocuSign\eSign\Client\ApiClient;
use DocuSign\eSign\Api\EnvelopesApi;
use App\Http\Controllers\ContractController;
use App\Models\Sales\DealsActivityFilesModel;


class DocuSignController extends Controller

{

    public function createEnvelope(Request $request)
    {
        $apiClient = new ApiClient();
        $apiClient->getOAuth()->setOAuthBasePath(env('DS_AUTH_SERVER'));
        $keyPath = storage_path(env('DS_KEY_PATH'));
        if (!file_exists($keyPath) || is_dir($keyPath)) {
            throw new \Exception("Docusign private key not found or invalid at: $keyPath");
        }
        $privateKey = file_get_contents($keyPath);
        $response = $apiClient->requestJWTUserToken(
            $ikey = env('DS_CLIENT_ID'),
            $userId = env('DS_IMPERSONATED_USER_ID'),
            $key = $privateKey,
            $scope = env('DS_JWT_SCOPE')
        );
        $token = $response[0];
        $accessToken = $token->getAccessToken();
        $accountId = env('DS_ACCOUNT_ID');
        $response = Http::withHeaders([
            'Authorization' => "Bearer $accessToken",
            'Content-Type' => 'application/json'
        ])->post("https://demo.docusign.net/restapi/v2.1/accounts/{$accountId}/envelopes", $request->all());
        // dd($response);
        if ($response->successful()) {

            return response()->json($response->json());
        } else {
            return response()->json(['error' => 'Envelope creation failed'], 500);
        }
    }

    public function viewSender(Request $request)
    { 
        $accountId = env('DS_ACCOUNT_ID');
        $apiClient = new ApiClient();
        $apiClient->getOAuth()->setOAuthBasePath(env('DS_AUTH_SERVER'));
        // Get access token via static method
        $contractController = new ContractController();
        $accessToken = $contractController->getToken($apiClient);
        // Get user and account info
        $userInfo = $apiClient->getUserInfo($accessToken);
        $accountInfo = $userInfo[0]->getAccounts();
        // Set API host base URI for envelope creation
        $apiClient->getConfig()->setHost($accountInfo[0]->getBaseUri() . env('DS_ESIGN_URI_SUFFIX'));
        // Build and send the envelope
        $envelopeApi = new EnvelopesApi($apiClient);
        $envelopeDefinition = $contractController->buildEnvelope($request,'created');
        $result = $envelopeApi->createEnvelope($accountInfo[0]->getAccountId(), $envelopeDefinition);
        $envelopeId = $result->getEnvelopeId(); // 
        $fileName = $request->input('file_name'); // should now work

        // Save to deals_activity table
        $dealId = $request->input('deal_id'); 
        if ($dealId) {
            $activity = new DealsActivityModel();
            $activity->deal_id = $dealId;
            $activity->type = 1; 
            $activity->title = "Docusign";
            // $activity->description = "Docusign envelope sent to user";
            $activity->description = $fileName ? "Docusign envelope sent for file: $fileName" : "Docusign envelope sent to user";
            $activity->envelop_id = $envelopeId;
            $activity->time_from = Carbon::now('Europe/London');
            $activity->created_at = Carbon::now('Europe/London');
            $activity->updated_at = Carbon::now('Europe/London');
            $activity->save();
        }
        // $returnUrl = $request->input('returnUrl') ?? route('contract.create'); 
        $returnUrl = route('docusign.callback');
        // Generate Sender View URL
        $response = Http::withHeaders([
            'Authorization' => "Bearer $accessToken",
            'Content-Type' => 'application/json'
        ])->post("https://demo.docusign.net/restapi/v2.1/accounts/{$accountId}/envelopes/{$envelopeId}/views/sender", [
            'returnUrl' => $returnUrl
        ]);
        // Return the view URL to use in iframe or redirect
        return response()->json(['url' => $response->json()['url'] ?? null]);
    }
    // For checking status of recipients
    public function getRecipientStatus($envelopeId)
    {
        $apiClient = new ApiClient();
        $apiClient->getOAuth()->setOAuthBasePath(env('DS_AUTH_SERVER'));
        $contractController = new ContractController();
        $accessToken = $contractController->getToken($apiClient);
        $userInfo = $apiClient->getUserInfo($accessToken);
        $accountInfo = $userInfo[0]->getAccounts();
        $apiClient->getConfig()->setHost($accountInfo[0]->getBaseUri() . env('DS_ESIGN_URI_SUFFIX'));
        $envelopesApi = new EnvelopesApi($apiClient);
        $recipients = $envelopesApi->listRecipients($accountInfo[0]->getAccountId(), $envelopeId);
        $total = 0;
        $completed = 0;
        foreach ($recipients->getSigners() as $signer) {
            $total++;
            if ($signer->getStatus() === 'completed' || $signer->getStatus() === 'delivered') {
                $completed++;
            }
        }
        foreach ($recipients->getCarbonCopies() as $cc) {
            $total++;
            if ($cc->getStatus() === 'completed' || $cc->getStatus() === 'delivered') {
                $completed++;
            }
        }
        // Update deal activity if all are completed
        if ($total > 0 && $completed === $total) {
            \App\Models\Sales\DealsActivityModel::where('envelop_id', $envelopeId)->update([
                'done' => 1,
                'updated_at' => \Carbon\Carbon::now('Europe/London')
            ]);
        }
        return response()->json([
            'total' => $total,
            'completed' => $completed,
            'details' => [
                'signers' => collect($recipients->getSigners())->map(function ($s) {
                    return [
                        'name' => $s->getName(),
                        'email' => $s->getEmail(),
                        'status' => $s->getStatus()
                    ];
                }),
                'carbonCopies' => collect($recipients->getCarbonCopies())->map(function ($c) {
                    return [
                        'name' => $c->getName(),
                        'email' => $c->getEmail(),
                        'status' => $c->getStatus()
                    ];
                })
            ]
        ]);
    }
}

