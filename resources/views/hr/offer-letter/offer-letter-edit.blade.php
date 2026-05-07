@extends('app')

@section('title', 'Edit Offer Letter')

@push('style')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('hr.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('hr.recruitment') }}">Recruitment</a>
    </li>
    <li class="breadcrumb-item">Update Offer Letter</li>
</ol>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <form method="POST" action="{{ route('hr.offer.letter.update', [$offerLetter->id]) }}" class="prevent-multiple-submits" autocomplete="off" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="card-title">{{ __('Offer Letter Form') }}</h5>
                        <div class=" text-end pt-0 my-n5">
                            <a href="{{ route('hr.recruitment') }}" class="btn btn-outline-primary btn-sm waves-effect">
                                <span class="mdi mdi-step-backward"></span> {{ __('Back') }}
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-body">
                        <div class="row">

                            <!-- applicant select2 -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select @error('applicant_id') is-invalid @enderror" name="applicant_id" id="applicant_id" required disabled>
                                        @foreach($applicants as $applicant)
                                        <option value="{{ $applicant->id }}" @selected(old('applicant_id', $offerLetter?->applicant_id) == $applicant->id)>
                                            {{ $applicant->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="applicant_id">{{ __('Applicant') }} <span class="text-danger">*</span></label>
                                    @error('applicant_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- offer_date -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="date" class="form-control @error('offer_date') is-invalid @enderror" name="offer_date" id="offer_date" value="{{ \Carbon\Carbon::parse($offerLetter->offer_date)->format('Y-m-d') }}" placeholder="{{ __('Offer Date') }}" onclick="this.showPicker();" required>
                                    <label for="offerDate">{{ __('Offer Date') }} <span class="text-danger">*</span></label>
                                    @error('offer_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- joining_date -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="date" class="form-control @error('joining_date') is-invalid @enderror" name="joining_date" id="joining_date" value="{{ \Carbon\Carbon::parse($offerLetter->joining_date)->format('Y-m-d') }}" placeholder="{{ __('Joining Date') }}" onclick="this.showPicker();" required>
                                    <label for="joiningDate">{{ __('Joining Date') }} <span class="text-danger">*</span></label>
                                    @error('joining_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- offer_file_path -->
                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="file" class="form-control @error('offer_file_path') is-invalid @enderror" name="offer_file_path" id="offer_file_path" placeholder="{{ __('Offer File Path') }}" accept=".pdf, .doc, .docs">
                                    <label for="offerFilePath">{{ __('Offer File Path') }} <span class="text-danger">*</span></label>
                                    @error('offer_file_path')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <select class="form-select select2 @error('status') is-invalid @enderror" name="status" id="status" required>
                                        <option value="">{{ __('Select Status') }}</option>
                                        @foreach(Enum::OFFER_LETTER_STATUS as $key => $value)
                                        <option value="{{ $key }}" @selected(old('status', $offerLetter?->status) == $key)>
                                            {{ $value['label'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <label for="status">{{ __('Status') }} <span class="text-danger">*</span></label>
                                    @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                                    
                        </div>

                    </div>
                </div>
                <div class="card-footer text-end">
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection


@push('script')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('vendor/tinymce/tinymce.min.js') }}" referrerpolicy="origin"></script>
<script src="{{ asset('assets/js/tinymce-helper.js') }}"></script>
<script>
    $('document').ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            placeholder: "Select department",
            allowClear: false
        });
    }); // document closed
</script>
@endpush
