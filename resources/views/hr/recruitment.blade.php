@extends('app')

@section('title', 'Recruitment')

@push('style')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/vendor/libs/DataTables/datatables.min.css') }}">
@endpush

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('hr.dashboard') }}">Dashboard</a>
    </li>
    <li class="breadcrumb-item">Recruitment</li>
</ol>
@endsection

@section('content')
<div class="row">

    <!-- Job Posting -->
    @if(in_array(auth()->user()->role, [1, 3]))
    <x-data-table-card title="Job Posting" table-id="jobPostingTable" table-class="job-posting-table" :search="false" :button="true" route="{{ route('hr.job.posting.create') }}" button-name="Add New">
        <x-slot name="thead">
            <tr><th>Title</th><th>Description</th><th>Department</th><th>Location</th><th>Posted Date</th><th>Status</th><th>Action</th></tr>
        </x-slot>
    </x-data-table-card>
    @endif

    <!-- Applicant -->
    @if(in_array(auth()->user()->role, [1, 3]))
    <x-data-table-card title="Applicant" table-id="applicantTable" table-class="applicant-table" :button="true" route="{{ route('hr.applicant.create') }}" button-name="Add New">
        <x-slot name="thead">
            <tr><th>Post Title</th><th>Department</th><th>Name</th><th>Email</th><th>Phone</th><th>Address</th><th>Status</th><th>Action</th></tr>
        </x-slot>
    </x-data-table-card>
    @endif

    <!-- interview -->
    @if(in_array(auth()->user()->role, [1, 3]))
    <x-data-table-card title="Interview" table-id="interviewTable" table-class="interview-table" :search="false" :button="true" route="{{ route('hr.interview.create') }}" button-name="Add New">
        <x-slot name="thead">
            <tr><th>Applicant</th><th>Interviewer</th><th>Interview Scheduled Date</th><th>feedback</th><th>Status</th><th>Action</th></tr>
        </x-slot>
    </x-data-table-card>
    @endif

    <!-- offer letter generation -->
    @if(in_array(auth()->user()->role, [1, 3]))
    <x-data-table-card title="Offer Letter Generation" table-id="offerLetterTable" table-class="offer-letter-table" :search="false" :button="true" route="{{ route('hr.offer.letter.create') }}" button-name="Add New">
        <x-slot name="thead">
            <tr><th>Applicant Name</th><th>Offer Date</th><th>Joining Date</th><th>File</th><th>Status</th><th>Action</th></tr>
        </x-slot>
    </x-data-table-card>
    @endif

    <x-modals.description-modal />

    <!-- Document View Modal -->
    <div class="modal fade" id="offerLetterModal" tabindex="-1" aria-labelledby="offerLetterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="offerLetterModalLabel">Offer Letter View</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <iframe id="offerLetterViewer" src="" width="100%" height="600px" style="border:none;"></iframe>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('components.scripts.description-modal-script')

@push('script')
<script src="{{ asset('assets/vendor/libs/DataTables/datatables.min.js') }}"></script>
<script>
    window.STATUS_ENUMS = {
        applicant_status: @json(\Enum::APPLICANT_STATUS),
    };
    window.CSRF_TOKEN = '{{ csrf_token() }}';
</script>
<script src="{{ asset('assets/js/common-helpers.js') }}"></script>
<script>
    $('document').ready(function() {
        // ---job-posting---
        @if(in_array(auth()->user()->role, [1, 3]))
        initDataTable('.job-posting-table', '{{ route("hr.job.posting.index") }}', ['title', 'description.sf', 'department.sf', 'location.sf', 'posted_date.sf', 'status.sf', 'action.sfof'], {language: { searchPlaceholder: 'Post Title' }});
        @endif
        // ---applicant-table---
        @if(in_array(auth()->user()->role, [1, 3]))
        initDataTable('.applicant-table', '{{ route("hr.applicant.index") }}', ['post_title.sf', 'department.sf', 'name', 'email', 'phone.sf', 'address.sfof', 'status.sf', 'action.sfof'], {language: { searchPlaceholder: 'Applicant Name' }});
        @endif
        // ---interview-table---
        @if(in_array(auth()->user()->role, [1, 3]))
        initDataTable('.interview-table', '{{ route("hr.interview.index") }}', ['applicant', 'interviewer', 'scheduled_date.sf', 'feedback.sf', 'status.sf', 'action.sfof'], {language: { searchPlaceholder: 'Applicant Name' }});
        @endif
        // ---offer-letter---
        @if(in_array(auth()->user()->role, [1, 3]))
        initDataTable('.offer-letter-table', '{{ route("hr.offer.letter.index") }}', ['applicant', 'offer_date.sf', 'joining_date.sf', 'offer_file_path.sf', 'status.sf', 'action.sfof'], {language: { searchPlaceholder: 'Applicant Name' }});
        @endif

        $('body').on('click', '.view-offer-letter', function () {
            let offerLetterUrl = $(this).data('url');
            $('#offerLetterViewer').attr('src', offerLetterUrl);
            $('#offerLetterModal').modal('show');
        });
    }); // document closed
</script>
@endpush