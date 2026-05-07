@extends('app')



@section('title', $lead->organisation_detail->name)

@push('style')
    <link rel="stylesheet" type="text/css" id="mce-u0" href="https://benedict2010.com/vendor/tinymce/skins/ui/tinymce-5/skin.min.css">
@endpush

@section('breadcrumb')

<ol class="breadcrumb">

    <li class="breadcrumb-item">

        <a href="{{ route('sales_dashboard') }}">Sales Dashboard</a>

    </li>

    <li class="breadcrumb-item">

        <a href="{{ route('lead.list') }}">List</a>

    </li>

    <li class="breadcrumb-item">{{ $lead->title }}</li>

</ol>

@endsection



@section('content')

@if(session()->has('success'))

<div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show">

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

    <strong><i class="tf-icons mdi mdi-check-circle-outline"></i> Success -</strong>

    {{ session()->get('success') }}

</div>

@endif

@if(session()->has('error'))

<div class="alert alert-danger alert-dismissible bg-danger text-white border-0 fade show">

    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

    <strong><i class="tf-icons mdi mdi-alert-outline"></i> Error -</strong>

    {{ session()->get('error') }}

</div>

@endif

<div class="row">

    <!-- LEFT HAND SIDE DIV  -->

    <div class="col-md-4">

        @include('sales.leads.partials.sidebar')

    </div>



    <!-- RIGHT HAND SIDE DIV  -->

    <div class="col-md-8">

        <div class="card mb-5">

            <div class="card-header">

                @if($update)

                <h6 class="card-title mb-5">

                    <span>UPDATE ACTIVITY</span>

                    <div class="float-end">

                        <a href="{{ route('lead.show', $lead->id) }}" class="btn btn-primary btn-sm">ADD ACTIVITY</a>

                    </div>

                </h6>

                @else

                <h6 class="card-title mb-5">ADD ACTIVITY</h6>

                @endif

                <ul class="nav nav-tabs" role="tablist">

                    <li class="nav-item" role="presentation">

                        <button type="button" class="nav-link waves-effect {{ old('form_type') == 'activity' || ($update && $update->type != 6) ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-home" id="activity_btn" aria-controls="navs-top-home" aria-selected="{{ old('form_type') == 'activity' || ($update && $update->type != 6) ? 'true' : 'false' }}">Activity</button>

                    </li>

                    <li class="nav-item" role="presentation">

                        <button type="button" class="nav-link waves-effect {{ old('form_type') == 'note' || ($update && $update->type == 6) ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" id="note_btn" aria-selected="{{ old('form_type') == 'note' || ($update && $update->type == 6) ? 'true' : 'false' }}" tabindex="-1">Note</button>

                    </li>

                </ul>

            </div>

            @if($update)

                @include('sales.leads.partials.activity.edit')

            @else

                @include('sales.leads.partials.activity.add')

            @endif

        </div>

            <div class="card">

                <div class="card-header">

                    <h6 class="card-title">ACTIVITY</h6>

                </div>

                <div class="card-body">

                    <!-- section for pending activity  -->

                    @if($activity->count() > 0)

                    <ul class="timeline timeline-outline card-timeline mb-0">

                        @foreach($activity as $item)

                            @switch($item->type)

                                @case(1)

                                    @include('sales.leads.partials.activity.upcoming.call')

                                    @break

                                @case(2)

                                    @include('sales.leads.partials.activity.upcoming.meeting')

                                    @break

                                @case(3)

                                    @include('sales.leads.partials.activity.upcoming.task')

                                    @break

                                @case(4)

                                    @include('sales.leads.partials.activity.upcoming.deadline')

                                    @break

                                @case(5)

                                    @include('sales.leads.partials.activity.upcoming.email')

                                    @break

                            @endswitch

                        @endforeach

                    </ul>

                    @else

                    <div class="text-center my-5">You have no upcoming activities</div>

                    @endif



                    <!-- section for activity done -->

                    <div class="divider text-center my-5">

                        <div class="divider-text">

                            <h6 class="m-0">DONE</h6>

                        </div>

                    </div>

                    <ul class="timeline card-timeline mb-0">

                        @if($done_activity->count() > 0)

                            @foreach($done_activity as $done)

                                @switch($done->type)

                                    @case(1)

                                        @include('sales.leads.partials.activity.done.call')

                                        @break

                                    @case(2)

                                        @include('sales.leads.partials.activity.done.meeting')

                                        @break

                                    @case(3)

                                        @include('sales.leads.partials.activity.done.task')

                                        @break

                                    @case(4)

                                        @include('sales.leads.partials.activity.done.deadline')

                                        @break

                                    @case(5)

                                        @include('sales.leads.partials.activity.done.email')

                                        @break

                                    @case(6)

                                        @include('sales.leads.partials.activity.done.note')

                                        @break

                                    @case(7)

                                        @include('sales.leads.partials.activity.done.file')

                                        @break

                                @endswitch

                            @endforeach

                        @endif

                        <li class="timeline-item timeline-item-transparent border-transparent">

                            <span class="timeline-point timeline-point-success"></span>

                            <div class="timeline-event pb-1 mb-n4">

                                <div class="timeline-header mb-2">

                                    <h6 class="mb-0 text-success">

                                        <i class="tf-icons mdi mdi-account-circle-outline"></i>

                                        <span>Lead Created</span>

                                    </h6>

                                </div>

                                <p class="mb-0">{{ $lead->created_at->format('F j, Y'). ' at '.$lead->created_at->format('h:i A') }} by {{ $lead->owner_data?->first_name.' '.$lead->owner_data?->last_name }}</p>

                            </div>

                        </li>

                    </ul>

                </div>

            </div>

        </div>

    </div>

</div>

<div class="modal modal-top fade" id="largeModal" tabindex="-1" style="display: none;" aria-hidden="true">

    <div class="modal-dialog modal-xl" role="document">

        <form method="post" action="{{ route('lead.convert', $lead->id) }}" class="modal-content" id="leadConvert">

            @csrf

            <div class="modal-header">

                <h5 class="modal-title" id="exampleModalLabel3">Convert to Deal</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <div class="d-flex justify-content-center">

                    <div class="spinner-grow" role="status">

                        <span class="visually-hidden">Loading...</span>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

<div class="modal modal-top fade" id="modalToggle" tabindex="-1">

    <div class="modal-dialog">

        <form method="post" action="{{ route('lead.submit-update-info', $lead->id) }}" class="modal-content" id="leadUpdate">

            @csrf

            <input type="hidden" name="lead-id" value="{{ $lead->id }}">

            <div class="modal-header">

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <div class="modal-body">

                <div class="d-flex justify-content-center">

                    <div class="spinner-grow" role="status">

                        <span class="visually-hidden">Loading...</span>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>

<div class="modal fade" id="transferOwnerModal" tabindex="-1" aria-labelledby="transferOwnerModalLabel" aria-hidden="true">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title" id="transferOwnerModalLabel">Transfer Lead Ownership</h5>

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>

            <form action="{{ route('lead.transfer.owner') }}" method="POST">

                @csrf

                <div class="modal-body">

                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">

                    <div class="mb-3">

                        <label for="user_id" class="form-label">Select New Owner</label>

                        <select name="user_id" class="form-select" required>

                            <option value="">Select User</option>

                            @foreach($users as $user)

                                <option value="{{ $user->id }}">{{ $user?->first_name }} {{ $user?->last_name }}</option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="submit" class="btn btn-success">Transfer</button>

                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                </div>

            </form>

        </div>

    </div>

</div>



@endsection







@section('style')

<!-- Styles -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<!-- Or for RTL support -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.standalone.min.css" integrity="sha512-D5/oUZrMTZE/y4ldsD6UOeuPR4lwjLnfNMWkjC0pffPTCVlqzcHTNvkn3dhL7C0gYifHQJAIrRTASbMvLmpEug==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/css/bootstrap-datetimepicker-standalone.min.css" integrity="sha512-L0/PNISezIYAoqFXBGP9EJ4qLH8XF356+Lo92vzloQqk7HUpZ4FN1x1dUOnsUAUjHTSxXxeaD0HXfrANhtJOEA==" crossorigin="anonymous" referrerpolicy="no-referrer" /> -->

@endsection





@section('script')



<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.30.1/moment.min.js" integrity="sha512-hUhvpC5f8cgc04OZb55j0KNGh4eh7dLxd/dPSJ5VyzqDWxsayYbojWyl5Tkcgrmb/RVKCRJI1jNlRbVP4WWC4w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js" integrity="sha512-Y+0b10RbVUTf3Mi0EgJue0FoheNzentTMMIE2OreNbqnUPNbQj8zmjK3fs5D2WhQeGWIem2G2UkKjAL/bJ/UXQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <script src="https://benedict2010.com/vendor/tinymce/tinymce.min.js" referrerpolicy="origin"></script>

<script type="text/javascript">

        $(function() {
            tinymce.init({
                selector: 'textarea#activity_note',
                license_key: 'gpl',
                branding: false,
                promotion: false,
                theme: 'silver',
                skin: 'tinymce-5',
            });

            tinymce.init({
                selector: 'textarea#note',
                license_key: 'gpl',
                branding: false,
                promotion: false,
                theme: 'silver',
                skin: 'tinymce-5'
            });

        if ("{{ $errors->has('modal') }}") {

            $("#modal_btn").click();

        }

        $('.datepicker').datepicker({

            format: 'yyyy-mm-dd',

            todayHighlight: true,

            autoclose: true,

            todayBtn: true,

        });



        // new tempusDominus.TempusDominus(, {

        //     format: 'yyyy-mm-dd',

        //     buttons: {

        //         today: true,

        //         clear: false,

        //         close: false

        //     },

        //     restrictions: {

        //         minDate: new Date(),

        //     },

        // });



        // $('.datepicker').DateTimePicker({

        //     format: 'yyyy-mm-dd'

        // });



        // $('input[type="date"]').DateTimePicker({

        //     format: 'yyyy-mm-dd',

        //     buttons: {

        //         today: true,

        //         clear: false,

        //         close: false

        //     },

        //     restrictions: {

        //         minDate: new Date(),

        //     },

        // });

        // $('input[type="time"]').DateTimePicker({

        //     format: 'hh:mm a',

        //     buttons: {

        //         today: true,

        //         clear: false,

        //         close: false

        //     },

        // });



        $('[name="type"]').change(function() {

            if ($(this).val() == 6) {

                $('#title').parent().addClass('d-none');

                $('#from_date').closest('.row').addClass('d-none');

                $('#from_date').closest('.row').find('input').prop('required', false);

                $('#mark_done').parent().addClass('d-none');

                $('#note').prop('required', true);

            } else {

                $('#title').parent().removeClass('d-none');

                $('#from_date').closest('.row').removeClass('d-none');

                $('#from_date').closest('.row').find('input').prop('required', false);

                $('#mark_done').parent().removeClass('d-none');

                $('#note').prop('required', false);

            }

        });

        if ($('[name="type"]:checked').val() == 6) {

            $('#title').parent().addClass('d-none');

            $('#from_date').closest('.row').addClass('d-none');

            $('#mark_done').parent().addClass('d-none');

            $('#from_date').closest('.row').find('input').prop('required', false);

            $('#note').prop('required', true);

        } else {

            $('#title').parent().removeClass('d-none');

            $('#from_date').closest('.row').removeClass('d-none');

            $('#from_date').closest('.row').find('input').prop('required', false);

            $('#mark_done').parent().removeClass('d-none');

            $('#note').prop('required', false);

        }



        $('#modalToggle').modal({

            backdrop: 'static',

            keyboard: false

        })

        .on('show.bs.modal', function(e){

            var action = $(e.relatedTarget).data('action');

            $.ajax({

                type: "GET",

                url: "{{ route('lead.update-info', $lead->id) }}",

                data: {

                    action: action,

                    id: "{{ $lead->id }}"

                },

                success: function(res){

                    $(e.target).find('.modal-body').html(res);

                    $('.select2').select2({

                        theme: 'bootstrap-5',

                        dropdownParent: $(e.target).find('.modal-body')

                    });

                    $('#close_date').datepicker({

                        format: 'yyyy-mm-dd',

                        todayHighlight: true,

                        autoclose: true,

                        todayBtn: true,

                    });

                },

                error: function(request, status, error){

                    console.log("ajax call went wrong:" + request.responseText);

                }

            });

        })

        .on('hide.bs.modal', function(e){

            $(e.target).find('.modal-body').html('<div class="d-flex justify-content-center"><div class="spinner-grow" role="status"><span class="visually-hidden">Loading...</span></div></div>');

        });



        $('#leadUpdate').submit(function(e){

            e.preventDefault();

            $.ajax({

                type: "POST",

                url: "{{ route('lead.submit-update-info', $lead->id) }}",

                data: $('#leadUpdate').serialize(),

                success: function(res){

                    $('#modalToggle').modal('hide');

                    Swal.fire({

                        title: 'Lead updated',

                        icon: "success",

                        allowOutsideClick: false,

                        didClose: (toast) => {

                            location.reload();

                        }

                    });

                },

                error: function(request, status, error){

                    console.log("ajax call went wrong:" + request.responseText);

                }

            });

        });



        $('#largeModal').modal({
            backdrop: 'static',
            keyboard: false
        })
        .one('show.bs.modal', function(e){  // Use .one() instead of .on()
            $.ajax({
                type: "GET",
                url: "{{ route('lead.convert-lead', $lead->id) }}",
                data: { id: "{{ $lead->id }}" },
                success: function(res){
                    $(e.target).find('.modal-body').html(res);

                    $('.select2').select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $(e.target).find('.modal-body')
                    });

                    $('#close_date').datepicker({
                        format: 'yyyy-mm-dd',
                        todayHighlight: true,
                        autoclose: true,
                        todayBtn: true,
                    });
                },
                error: function(request, status, error){
                    console.log("ajax call went wrong: " + request.responseText);
                }
            });
        })
        .on('hide.bs.modal', function(e){
            $(e.target).find('.modal-body').html('<div class="d-flex justify-content-center"><div class="spinner-grow" role="status"><span class="visually-hidden">Loading...</span></div></div>');
        });




        $('#leadConvert').submit(function(e) {

            e.preventDefault();

            $.ajax({

                type: "POST",

                url: "{{ route('lead.submit-convert-lead', $lead->id) }}",

                data: $(this).serialize(),

                success: function(res) {

                    if (res.success) {

                        $('#largeModal').modal('hide');

                        Swal.fire({

                            title: res.message,

                            icon: "success",

                            allowOutsideClick: false,

                            didClose: () => {

                                window.location.href = "{{ route('deal.list') }}"; // Redirect to deals page

                            }

                        });

                    }

                },

                error: function(request, status, error) {

                    if (request.responseJSON && request.responseJSON.errors) {

                        // Display validation errors

                        const errors = request.responseJSON.errors;

                        for (const field in errors) {

                            const errorMessage = errors[field][0];

                            $(`#${field}`).addClass('is-invalid');

                            $(`#${field}`).next('.invalid-feedback').text(errorMessage);

                        }

                    } else {

                        console.log("An error occurred:", request.responseText);

                    }

                }

            });

        });



    });

    

    document.addEventListener("DOMContentLoaded", function() {

        // Get all tab buttons

        var activityTab = document.getElementById('activity_btn');

        var noteTab = document.getElementById('note_btn');

    

        // Add event listeners to switch tabs properly

        activityTab.addEventListener('click', function () {

            document.getElementById('navs-top-home').classList.add('show', 'active');

            document.getElementById('navs-top-profile').classList.remove('show', 'active');

        });

    

        noteTab.addEventListener('click', function () {

            document.getElementById('navs-top-profile').classList.add('show', 'active');

            document.getElementById('navs-top-home').classList.remove('show', 'active');

        });

    });



</script>

@endsection