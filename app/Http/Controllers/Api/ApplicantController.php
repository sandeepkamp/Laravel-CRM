<?php

namespace App\Http\Controllers\Api;

use App\Models\Applicant;
use Illuminate\Http\Request;
use App\Constants\ResponseCode;
use App\Constants\ResponseMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicantResource;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\ApplicantCollection;

class ApplicantController extends Controller
{
    public function applicantIndex()
    {
        try {
            $limit = $request->limit ?? 10;
            $applicants = Applicant::paginate($limit);

            return successHandler(
                new ApplicantCollection($applicants),
                ResponseCode::OK_CODE,
                ResponseMessage::APPLICANT_FETCHED_SUCCESSFULLY
            );
        } catch (\Exception $e) {
            return serverErrorHandler($e);
        }
    }

    public function applicantStore(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'job_posting_id' => 'required|integer|exists:applicants,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string',
                'resume_path' => 'required|file|mimes:pdf,doc,docs|max:10240', // max 10MB
                'address'    => ['nullable', 'string', 'max:1000'],
            ]);
            
            if ($validator->fails()) {
                return validationErrorHandler($validator->errors());
            }
            
            $fileData = [];
            if($request->hasFile('resume_path')) {
                $fileData = uploadFile($request->file('resume_path'), \UploadPaths::APPLICANT_RESUME);
            }
            
            $applicant = new Applicant();
            $applicant->job_posting_id = $request->job_posting_id;
            $applicant->name = ucwords($request->name);
            $applicant->email = $request->email;
            $applicant->phone = $request->phone;
            $applicant->resume_path = !empty($fileData) ? $fileData['path'] : null;
            $applicant->address = $request->address ? ucfirst($request->address) : null;
            $applicant->save();

            return successHandler(
                new ApplicantResource($applicant),
                ResponseCode::CREATED_CODE,
                ResponseMessage::APPLICANT_CREATED_SUCCESSFULLY
            );

        } catch (\Exception $e) {
            return serverErrorHandler($e);
        }
    }
}