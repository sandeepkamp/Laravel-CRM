<?php



namespace App\Http\Controllers;



use Carbon\Carbon;

use Illuminate\Http\Request;

use Illuminate\Http\UploadedFile;

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

use App\Models\Sales\DealsActivityModel;



class ContractController extends SalesController

{

    /**

     * @return view

     */

    public function create(string $id){

        return view('sales.deals.contract.create', ['deal_id' => $id]);
    }

    /**

     * @param Request $request

     * 

     * @return object

     */

    public function send(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'formFile' => 'required|file|mimes:doc,docx,pdf|max:204800',
            'name.*' => 'required|min:3|max:50',
            'email.*' => 'required|email|max:255'
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $apiClient = new ApiClient();
        $apiClient->getOAuth()->setOAuthBasePath(env('DS_AUTH_SERVER'));
        try {
            $accessToken = $this->getToken($apiClient);
        } catch (\Throwable $th) {
            return back()->withError($th->getMessage())->withInput();
        }
        $userInfo = $apiClient->getUserInfo($accessToken);
        $accountInfo = $userInfo[0]->getAccounts()[0];
        $accountId = $accountInfo->getAccountId();
        $apiClient->getConfig()->setHost($accountInfo->getBaseUri() . env('DS_ESIGN_URI_SUFFIX'));
        // Create document
        $file = $request->file('formFile');
        $document = new Document([
            'document_base64' => base64_encode(file_get_contents($file->getPathname())),
            'name' => $file->getClientOriginalName(),
            'file_extension' => $file->getClientOriginalExtension(),
            'document_id' => '1'
        ]);
        //Create envelope definition (no recipients)
        $envelopeDefinition = new EnvelopeDefinition([
            'email_subject' => "Please review and send",
            'documents' => [$document],
            'status' => 'created' // Important: draft mode
        ]);
        try {
            $envelopeApi = new EnvelopesApi($apiClient);
            $envelopeSummary = $envelopeApi->createEnvelope($accountId, $envelopeDefinition);
            $envelopeId = $envelopeSummary->getEnvelopeId();
            // Log activity
            $activity = new DealsActivityModel;
            $activity->deal_id = $id;
            $activity->type = 1;
            $activity->title = "Docusign";
            $activity->description = "Docusign file prepared for sender view";
            $activity->envelop_id = $envelopeId;
            $activity->time_from = Carbon::now('Europe/London');
            $activity->created_at = Carbon::now('Europe/London');
            $activity->updated_at = Carbon::now('Europe/London');
            $activity->save();
            // Create Sender View URL
            $returnUrlRequest = new ReturnUrlRequest([
                'return_url' => route('deals.show', $id) // change to your own "back to CRM" page
            ]);
            $viewUrl = $envelopeApi->createSenderView($accountId, $envelopeId, $returnUrlRequest);
            // Redirect to DocuSign sender view
            return redirect()->away($viewUrl->getUrl());
        } catch (\Throwable $th) {
            return back()->withError($th->getMessage())->withInput();
        }
    }

    /**

     * @param Request $request

     * 

     * @return EnvelopeDefinition

     */

    public function buildEnvelope(Request $request): EnvelopeDefinition
    {

        $fileContent = $request->file('formFile')->get();

        $fileName = $request->file('formFile')->getClientOriginalName();

        $fileExtension = $request->file('formFile')->getClientOriginalExtension();
        // Create document
        $document = new Document([

            'document_id' => "1",

            'document_base64' => base64_encode($fileContent),

            'file_extension' => $fileExtension,

            'name' => $fileName

        ]);

        $signersArray = [];

        $carbonCopiesArray = [];

        // Loop through recipients and define roles correctly

        foreach ($request->input('name') as $key => $name) {

            $recipientEmail = $request->input('email')[$key];

            $recipientRole = $request->input('role')[$key];

            // Ensure unique recipient ID and routing order

            $recipientId = strval($key + 1);  // Increment recipient ID for each recipient

            // $routingOrder = strval($key + 1); // Ensure unique routing order
            $routingOrder = "1";

            $sign_here_tab = new SignHere([

                'anchor_string' => "**signature**",

                'anchor_units' => "pixels",

                'anchor_x_offset' => "100",

                'anchor_y_offset' => "0"

            ]);

            $tabs = new Tabs(['sign_here_tabs' => [$sign_here_tab]]);
            
            if ($recipientRole === 'sign') {

                $signer = new Signer([

                    'email' => $recipientEmail,

                    'name' => $name,

                    'recipient_id' => $recipientId,

                    'routing_order' => $routingOrder,

                    'tabs' => $tabs,

                ]);

                $signersArray[] = $signer;

            } elseif ($recipientRole === 'copy') {

                $carbonCopy = new \DocuSign\eSign\Model\CarbonCopy([

                    'email' => $recipientEmail,

                    'name' => $name,

                    'recipient_id' => $recipientId,

                    'routing_order' => $routingOrder,

                ]);

                $carbonCopiesArray[] = $carbonCopy;

            }

        }

        $recipients = new Recipients([

            'signers' => $signersArray,

            'carbon_copies' => $carbonCopiesArray

        ]);

        // Create the envelope definition

        $envelopeDefinition = new EnvelopeDefinition([

            'documents' => [$document],

            'recipients' => $recipients,

            'email_subject' => "Please sign your Digitex agreement",

            'status' => "created"

        ]);

        return $envelopeDefinition;
    }

    /**

     * @param ApiClient $apiClient

     * 

     * @return string

     */

    public function getToken(ApiClient $apiClient) : string{

        try {

          $keyPath = storage_path(env('DS_KEY_PATH'));

            // dd(env('DS_KEY_PATH'), storage_path(env('DS_KEY_PATH')));



            if (!file_exists($keyPath) || is_dir($keyPath)) {

                throw new \Exception("Docusign private key not found or invalid at: $keyPath");

            }

            

            $privateKey = file_get_contents($keyPath);

            // dd($privateKey);

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

}

