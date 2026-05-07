@extends('app')

@section('title', $deal->organisation_detail->name)

@push('style')
    <link rel="stylesheet" type="text/css" id="mce-u0" href="https://benedict2010.com/vendor/tinymce/skins/ui/tinymce-5/skin.min.css">
@endpush

@section('breadcrumb')

<ol class="breadcrumb">

   <li class="breadcrumb-item">

      <a href="{{ route('sales_dashboard') }}">Sales Dashboard</a>

   </li>

   <li class="breadcrumb-item">

      <a href="{{ route('deal.list') }}">List</a>

   </li>

   <li class="breadcrumb-item">{{ $deal->title }}</li>

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

   <!-- left hand side div -->

   <div class="col-md-4">

      <div class="card mb-5">

         <div class="card-body">

            <!--<h5 class="mb-5">{{ $deal->title }}</h5>-->

            <h5 class="mb-5">{{ $deal->first_name }} {{ $deal->last_name }}</h5>

            <div class="d-flex justify-content-between align-items-center">

               <div class="col-12 text-end" id="add_product">

                  <!-- <div class="add-product-btn" style="cursor: pointer; color: #9055fd;">Add Product+</div> -->

                  <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#productModal">

                     <i class="tf-icons mdi mdi-plus"></i>

                     ADD PRODUCT

                  </button>

               </div>

            </div>

            <div class="divider text-start">

               <div class="divider-text">

                  <h6 class="m-0">DETAILS</h6>

               </div>

            </div>

            <div class="d-block">

               <div class="row mb-3">

                  <div class="col-1">

                     <i class="tf-icons mdi mdi-tag-outline"></i>

                  </div>

                  <div class="col-10">

                     @if($deal->stage->stage_data->id == 1)

                     <span class="badge rounded-pill bg-label-danger">{{ $deal->stage->stage_data->name }}</span>

                     @elseif($deal->stage->stage_data->id == 2)

                     <span class="badge rounded-pill bg-label-warning">{{ $deal->stage->stage_data->name }}</span>

                     @elseif($deal->stage->stage_data->id == 3)

                     <span class="badge rounded-pill bg-label-info">{{ $deal->stage->stage_data->name }}</span>

                     @elseif($deal->stage->stage_data->id == 4)

                     <span class="badge rounded-pill bg-label-primary">{{ $deal->stage->stage_data->name }}</span>

                     @elseif($deal->stage->stage_data->id == 5)

                     <span class="badge rounded-pill bg-label-success">{{ $deal->stage->stage_data->name }}</span>

                     @elseif($deal->stage->stage_data->id == 6)

                     <span class="badge rounded-pill bg-label-warning">{{ $deal->stage->stage_data->name }}</span>

                     @endif

                  </div>

                  <div class="col-1 text-end">

                     <i class="tf-icons mdi mdi-pencil-outline" style="cursor: pointer; color: #9055fd;" data-bs-toggle="modal" data-bs-target="#editStageModal"></i>

                  </div>

               </div>

               <div class="row mb-3">

                  <div class="col-1">

                     <i class="tf-icons mdi mdi-cash-multiple"></i>

                  </div>

                  <div class="col-7 d-flex">

                     <div class="ms-2">@if($deal->value){{ $global_currency->symbol.$deal->value }} @endif</div>

                  </div>

                  <div class="col-4 text-end">

                     <a href="{{ route('deal.edit', $deal->id) }}">

                        <i class="tf-icons mdi mdi-pencil-outline" style="cursor: pointer; color: #9055fd;"></i>

                     </a>

                  </div>

               </div>

               @if($deal->close_date)

               <div class="row mb-3">

                  <div class="col-1">

                     <i class="tf-icons mdi mdi-flag-checkered"></i>

                  </div>

                  <div class="col-10">

                     {{ date('jS M, Y', strtotime($deal->close_date)) }}

                  </div>

                  <div class="col-1 text-end">

                     <i class="tf-icons mdi mdi-pencil-outline" style="cursor: pointer; color: #9055fd;" data-bs-toggle="modal" data-bs-target="#editCloseDateModal"></i>

                  </div>

               </div>

               @endif

               <div class="row mb-5">

                  <div class="col-1">

                     <i class="tf-icons mdi mdi-account-circle"></i>

                  </div>

                  <div class="col-10">

                     {{ $deal->owner_data->first_name.' '.$deal->owner_data->last_name }}

                  </div>

                  <div class="col-1 text-end">

                     <i class="tf-icons mdi mdi-pencil-outline" style="cursor: pointer; color: #9055fd;" data-bs-toggle="modal" data-bs-target="#transferOwnershipModal"></i>

                  </div>

               </div>

            </div>

            <div class="divider text-start">

               <div class="divider-text">

                  <h6 class="m-0">PERSON</h6>

               </div>

            </div>

            <div class="d-block">

               <!-- Full Name Row -->

               <div class="row mb-3 position-relative name-row">

                  <div class="col-1">

                     <i class="tf-icons mdi mdi-account-outline"></i>

                  </div>

                  <div class="col-10">

                     <!-- Static Name Display -->

                     <span class="name-display">{{ $deal->full_name }}</span>

                     <input type="text" class="form-control name-input" value="{{ $deal->full_name }}" style="display:none;">

                  </div>

               </div>

               <!-- Email Row -->

               <div class="row mb-3 position-relative email-row">

                  <div class="col-1">

                     <i class="tf-icons mdi mdi-email-outline"></i>

                  </div>

                  <div class="col-10">

                     <!-- Static Email Display -->

                     <span class="email-display"><a href="mailto:{{ $deal->email }}">{{ $deal->email }}</a></span>

                     <input type="email" class="form-control email-input" value="{{ $deal->email }}" style="display:none;">

                  </div>

               </div>

               <!-- Phone Row -->

               <div class="row mb-5 position-relative phone-row">

                  <div class="col-1">

                     <i class="tf-icons mdi mdi-phone"></i>

                  </div>

                  <div class="col-10">

                     <!-- Static Phone Display -->

                     <span class="phone-display"><a href="tel:{{ $deal->phone }}">{{ $deal->phone }}</a></span>

                     <input type="tel" class="form-control phone-input" value="{{ $deal->phone }}" style="display:none;">

                  </div>

               </div>

            </div>

         </div>

      </div>

   </div>

   <!-- right hand side div -->

   <div class="col-md-8">

      <div class="card mb-5">

         <div class="card-header">

            @if($update)

            <h6 class="card-title">UPDATE ACTIVITY</h6>

            @else

            <h6 class="card-title">ADD ACTIVITY</h6>

            @endif

            <ul class="nav nav-tabs" role="tablist">

               <li class="nav-item" role="presentation">

                  <button type="button" class="nav-link  waves-effect {{ old('form_type') == 'activity' || !old('form_type') ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-home" id="activity_btn" aria-controls="navs-top-home" aria-selected="{{ old('form_type') == 'activity' ? 'true' : 'false' }}">Activity</button>

               </li>

               <li class="nav-item" role="presentation">

                  <button type="button" class="nav-link waves-effect {{ old('form_type') == 'note' ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-profile" aria-controls="navs-top-profile" id="note_btn" aria-selected="{{ old('form_type') == 'note' ? 'true' : 'false' }}" tabindex="-1">Note</button>

               </li>

               <li class="nav-item" role="presentation">

                  <button type="button" class="nav-link waves-effect {{ old('form_type') == 'file' ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#navs-top-messages" aria-controls="navs-top-messages" id="file_btn" aria-selected="{{ old('form_type') == 'file' ? 'true' : 'false' }}" tabindex="-1">File</button>

               </li>

               <li class="nav-item" role="presentation">

                  <button type="button" class="nav-link waves-effect {{ old('form_type') == 'docs' ? 'active' : '' }}" role="tab" data-bs-toggle="tab" data-bs-target="#docs-top-messages" aria-controls="docs-top-messages" id="docs_btn" aria-selected="{{ old('form_type') == 'doc' ? 'true' : 'false' }}" tabindex="-1">Document</button>

               </li>

               <span class="tab-slider" style="left: 0px; width: 90.2344px; bottom: 0px;"></span>

            </ul>

         </div>

         @if($update)

         <!--for updating the activity  -->

         <div class="card-body">

            @include('sales.deals.partials.activity.edit')

         </div>

         @else

         <!-- for fresh activity -->

         <div class="card-body">

            @include('sales.deals.partials.activity.add')

            
         </div>
         @endif

      </div>

        @if($done_activity->where('type', 7)->count() > 0)

            <div class="card mt-3">

                <div class="card-header">

                    <h6 class="card-title">FILES</h6>

                </div>

                <div class="card-body">

                     <ul class="timeline timeline-outline card-timeline mb-0">

                    @foreach($done_activity->where('type', 7) as $done)

                        @include('sales.deals.partials.activity.done.file')

                    @endforeach

                    </ul>

                </div>

            </div>

        @endif

       <p></p>

      <div class="card">

         <div class="card-header">

            <h6 class="card-title">DEALS ACTIVITY</h6>

         </div>

         <div class="card-body">

         <!-- section for pending activity  -->

         @if($activity->count() > 0)

         <ul class="timeline timeline-outline card-timeline mb-0">

            @foreach($activity->where('done', 0) as $item)  <!-- Show only upcoming activities -->

               @switch($item->type)

                  @case(1)

                     @include('sales.deals.partials.activity.upcoming.call')

                     @break

                  @case(2)

                     @include('sales.deals.partials.activity.upcoming.meeting')

                     @break

                  @case(3)

                     @include('sales.deals.partials.activity.upcoming.task')

                     @break

                  @case(4)

                     @include('sales.deals.partials.activity.upcoming.deadline')

                     @break

                  @case(5)

                     @include('sales.deals.partials.activity.upcoming.email')

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

                        @include('sales.deals.partials.activity.done.call')

                        @break

                     @case(2)

                        @include('sales.deals.partials.activity.done.meeting')

                        @break

                     @case(3)

                        @include('sales.deals.partials.activity.done.task')

                        @break

                     @case(4)

                        @include('sales.deals.partials.activity.done.deadline')

                        @break

                     @case(5)

                        @include('sales.deals.partials.activity.done.email')

                        @break

                     @case(6)

                        @include('sales.deals.partials.activity.done.note')

                        @break

                     @case(7)

                        @include('sales.deals.partials.activity.done.file')

                        @break

                  @endswitch

               @endforeach

            @endif

            <li class="timeline-item timeline-item-transparent border-transparent">

               <span class="timeline-point timeline-point-success"></span>

               <div class="timeline-event pb-1 mb-n4">

                  <div class="timeline-header">

                     <h6 class="mb-0 text-success">

                        <i class="tf-icons mdi mdi-account-circle-outline"></i>

                        <span>Deal Created</span>

                     </h6>

                  </div>

                  <p class="mb-0">{{ date('F j, Y', strtotime($deal->created_at)). ' at '.date('h:i a', strtotime($deal->created_at)) }} - {{ $deal->owner_data->first_name.' '.$deal->owner_data->last_name }}</p>

               </div>

            </li>

         </ul>

         </div>
      </div>

   </div></br>

   

<!-- Product Modal  -->

<div class="modal modal-top fade" tabindex="-1" role="dialog" id="productModal">

   <div class="modal-dialog modal-xl" role="document">

      <div class="modal-content">

         <div class="modal-header">

            <h5 class="modal-title">Products</h5>

            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

         </div>

         <div class="modal-body">

            <div class="product_div"></div>

         </div>

         <div class="modal-footer">

            <button type="button" class="btn btn-primary create-product">Save</button>

            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

         </div>

      </div>

   </div>

</div>

<!-- Transfer Ownership Modal -->

<div class="modal modal-top fade" id="transferOwnershipModal" tabindex="-1" aria-labelledby="transferOwnershipModalLabel" aria-hidden="true">

   <div class="modal-dialog">

      <div class="modal-content">

         <div class="modal-header">

            <h5 class="modal-title" id="transferOwnershipModalLabel">Transfer Ownership</h5>

            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

         </div>

         <form action="{{ route('deals.transfer_owner') }}" method="post">

            @csrf

            <input type="hidden" name="deal_id" value="{{ $deal->id }}">

            <div class="modal-body">

               <div class="form-group">

                  <label for="user_id">Select User</label>

                  <select name="user_id" class="form-select">

                     <option value="">-- select --</option>

                     @foreach ($persons as $user)

                     <option value="{{ $user->id }}">{{ $user->first_name.' '.$user->last_name }}</option>

                     @endforeach

                  </select>

               </div>

            </div>

            <div class="modal-footer">

               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

               <button type="submit" class="btn btn-primary">Submit</button>

            </div>

         </form>

      </div>

   </div>

</div>

<!-- Modal for Editing Close Date -->

<div class="modal modal-top fade" id="editCloseDateModal" tabindex="-1" aria-labelledby="editCloseDateModalLabel" aria-hidden="true">

   <div class="modal-dialog">

      <div class="modal-content">

         <div class="modal-header">

            <h5 class="modal-title" id="editCloseDateModalLabel">Change Close Date</h5>

            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

         </div>

         <div class="modal-body">

            <form action="{{ route('deal.updateCloseDate', ['deal' => $deal->id]) }}" method="POST">

               @csrf

               @method('PUT')

               <div class="mb-3">

                  <label for="close_date" class="form-label">Expected Close Date</label>

                  <input type="date" class="form-control" id="close_date" name="close_date" value="{{ date('Y-m-d', strtotime($deal->close_date)) }}" required>

               </div>

               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

               <button type="submit" class="btn btn-primary">Update</button>

            </form>

         </div>

      </div>

   </div>

</div>

<!--Edit Stage Modal-->

<div class="modal modal-top fade" id="editStageModal" tabindex="-1" aria-labelledby="editStageModalLabel" aria-hidden="true">

   <div class="modal-dialog">

      <div class="modal-content">

         <div class="modal-header">

            <h5 class="modal-title" id="editStageModalLabel">Edit Stage</h5>

            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

         </div>

         <form action="{{ route('deal.update-stage') }}" method="POST">

            @csrf

            <input type="hidden" name="deal_id" value="{{ $deal->id }}">

            <div class="modal-body">

               <div class="form-group mb-3">

                  <label class="form-label" for="stage">Select Stage</label>

                  <select class="form-control select2" name="stage_id" id="stage" required>

                     <option value="">-- select --</option>

                     @foreach ($stages as $stage)

                     <option value="{{ $stage->id }}" 

                        @if($deal->stage->stage_data->id == $stage->id) selected @endif>

                        {{ $stage->name }}

                     </option>

                     @endforeach

                  </select>

               </div>

            </div>

            <div class="modal-footer">

               <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

               <button type="submit" class="btn btn-primary">Save changes</button>

            </div>

         </form>

      </div>

   </div>

</div>

<!--Modal End-->

@endsection

@section('style')

<!-- Styles -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />

<!-- Or for RTL support -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.standalone.min.css" integrity="sha512-D5/oUZrMTZE/y4ldsD6UOeuPR4lwjLnfNMWkjC0pffPTCVlqzcHTNvkn3dhL7C0gYifHQJAIrRTASbMvLmpEug==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<link rel="stylesheet" href="{{ asset('assets/css/fileUpload.css') }}">

@endsection

@section('script')

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/pdfjs-dist@2.2.228/build/pdf.min.js"></script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>

<script src="{{ asset('assets/js/fileUpload.js') }}"></script>
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

         tinymce.init({
               selector: 'textarea#file_note',
               license_key: 'gpl',
               branding: false,
               promotion: false,
               theme: 'silver',
               skin: 'tinymce-5'
         });

      $("#fileUpload").fileUpload();

      $('.select2').select2({

         theme: 'bootstrap-5',

      });

      $('[name="type"]').change(function() {

         if ($(this).val() == 6) {

            $('#title').parent().addClass('d-none');

            $('#from_date').closest('.row').addClass('d-none');

            $('#from_date').closest('.row').find('input').prop('required', false);

            $('#mark_done').parent().addClass('d-none');

            $('#file').parent().addClass('d-none');

            $('#note').prop('required', true);

         } else if ($(this).val() == 7) {

            $('#title').parent().addClass('d-none');

            $('#from_date').closest('.row').addClass('d-none');

            $('#from_date').closest('.row').find('input').prop('required', false);

            $('#mark_done').parent().addClass('d-none');

            $('#file').parent().removeClass('d-none');

            $('#file').prop('required', true);

// $('#note').prop('required', true);

         } else {

            $('#title').parent().removeClass('d-none');

            $('#from_date').closest('.row').removeClass('d-none');

            $('#from_date').closest('.row').find('input').prop('required', true);

            $('#mark_done').parent().removeClass('d-none');

            $('#file').parent().addClass('d-none');

            $('#note').prop('required', false);

            $('#file').prop('required', false);

         }

      });

      if ($('[name="type"]:checked').val() == 6) {

         $('#title').parent().addClass('d-none');

         $('#from_date').closest('.row').addClass('d-none');

         $('#mark_done').parent().addClass('d-none');

         $('#from_date').closest('.row').find('input').prop('required', false);

         $('#file').parent().addClass('d-none');

         $('#note').prop('required', true);

         $('#file').prop('required', false);

      } else if ($('[name="type"]:checked').val() == 7) {

         $('#title').parent().addClass('d-none');

         $('#from_date').closest('.row').addClass('d-none');

         $('#mark_done').parent().addClass('d-none');

         $('#from_date').closest('.row').find('input').prop('required', false);

         $('#file').parent().removeClass('d-none');

         $('#file').prop('required', true);

// $('#note').prop('required', true);

      } else {

         $('#title').parent().removeClass('d-none');

         $('#from_date').closest('.row').removeClass('d-none');

         $('#from_date').closest('.row').find('input').prop('required', true);

         $('#mark_done').parent().removeClass('d-none');

         $('#file').parent().addClass('d-none');

         $('#note').prop('required', false);

         $('#file').prop('required', false);

      }

      var type = "{{ $update->type ?? '' }}";

      if (type == 6) {

         console.log(type);

// first remove the active class from the head and then hide the body

         $("#activity_btn").removeClass('active');

         $("#activity_btn").attr('aria-selected', 'false');

         $("#activity_btn").attr('tabindex', '-1');

         $("#file_btn").attr('tabindex', '-1');

         $("#navs-top-home").removeClass('active show');

// and now set

         $("#note_btn").addClass('active');

         $("#note_btn").attr('aria-selected', 'true');

         $("#note_btn").removeAttr('tabindex');

         $("#navs-top-profile").addClass('active show');

// also change the slider style

         $(".tab-slider").css({

            "left": "100.957 px",

            "width": "76.1719 px",

            "bottom": "0 px"

         });

      }

if (type == 7) { // means file

   console.log(type);

// first remove the active class from the head and then hide the body

   $("#activity_btn").removeClass('active');

   $("#activity_btn").attr('aria-selected', 'false');

   $("#navs-top-home").removeClass('active show');



// and now set the body

   $("#file_btn").addClass('active');

   $("#file_btn").attr('aria-selected', 'true');

   $("#navs-top-messages").addClass('active show');

// also change the slider style

   $(".tab-slider").css({

      "color": "red",

      "left": "177.129 px",

      "width": "66.7773 px",

      "bottom": "0 px"

   });

}

});

</script>

<script type="text/javascript">

   $(document).ready(function(){

      $('#add_product').click(function () {

         $.ajax({

            url: '/get-deal-product/{{$deal->id}}',

            method: 'GET',

            success: function (res) {

               $('.product_div').html(res);

               // $("#modal_div_2").removeClass("modal fade");

               // $("#modal_div_2").addClass("modal show");

               // $("#modal_div_2").css("display", "block");

               // $("#modal_div_2").removeAttr("aria-hidden");

               // $("#modal_div_2").attr("aria-modal", "true");

// Call calculate_totals to update totals

               calculate_totals();

            }

         });

      });

   })

</script>

<script type="text/javascript">   

   $(document).on('click','.create-product',function(e) {

      console.log(789);

      var error = 0;

      $('.calculate').each(function(i, v) {

         $(this).find('input').each(function() {

            if ($(this).prop('required') === true && $(this).val() == 0 || $(this).val() == '') {

               $(this).addClass('is-invalid');

               error++;

            } else {

               $(this).removeClass('is-invalid');

            }

         });

      });



      if (error > 0) {

         return false;

      }

      var object = [];

      $('.calculate').each(function(i, v) {

         var data = [];

         var obj = [];

         $(this).find('input').each(function(index,value) {

            obj[$(value).attr('data-name')] = $(value).val();

            data.push(obj);

         });

         object.push(Object.assign({}, obj));

      });

      $.ajax({

         url:"/add-deal-product",

         method:'POST',

         data:{data:object,"_token": "{{ csrf_token() }}","deal_id":{{$deal->id}}},

         success:function(res){

            if(res.code){

               // $("#modal_div_2").addClass("modal fade");

               // $("#modal_div_2").removeClass("modal show");

               // $("#modal_div_2").css("display","none");

// Reload the page after success

               window.location.reload();

            }

         }

      });

   });

   // $(document).on('click','.close-modal',function(){

   //    $("#modal_div_2").addClass("modal fade");

   //    $("#modal_div_2").removeClass("modal show");

   //    $("#modal_div_2").css("display","none");

   // });

</script>

<script type="text/javascript">

   $(document).on('keyup', '.price', function() {

      calculate_amount(this);

   });

   $(document).on('change', '.price', function() {

      calculate_amount(this);

   });

   $(document).on('keyup', '.quantity', function() {

      calculate_amount(this);

   });

   $(document).on('change', '.quantity', function() {

      calculate_amount(this);

   });

   $(document).on('keyup', '.tax', function() {

      calculate_amount(this);

   });

   $(document).on('change', '.tax', function() {

      calculate_amount(this);

   });



   $(document).on('keyup change', '.price, .quantity, .tax', function () {

      calculate_totals();

   });



   function calculate_amount(param) {

      let price = $(param).parents('.calculate').find('.price').val();

      let quantity = $(param).parents('.calculate').find('.quantity').val();

      let tax = $(param).parents('.calculate').find('.tax').val();

      var amount = (price * quantity) + ((price * quantity * tax) / 100);

      $(param).parents('.calculate').find('.amount').val(amount);

      calculate_total();

   }



   function calculate_total() {

      var total = 0;

      $('.calculate').each(function(index, value) {

         let price = $(value).find('.price').val();

         let quantity = $(value).find('.quantity').val();

         let tax = $(value).find('.tax').val();

         var amount = (price * quantity) + ((price * quantity * tax) / 100);

         total += amount;

      });

      $('#deal_value').attr('value', total.toFixed(2));

      $('#deal_value').val(total.toFixed(2));

   }



   $(document).on('click', '.delete_product', function () {

      $(this).parents('.calculate').remove();

      calculate_row_index();

      calculate_totals();

   });



   function calculate_row_index() {

      $('.calculate').each(function(i, v) {

         $(this).find('input').each(function() {

            $(this).attr('name', 'addmore[' + i + '][' + $(this).attr('data-name') + ']');

         });

      });

   }



   function calculate_totals() {

      var totalQuantity = 0;

      var totalProducts = 0;

      var totalAmount = 0;

      var totalTax = 0;

      var subTotal = 0;



// Loop through each product row and calculate totals

      $('.calculate').each(function () {

         let price = parseFloat($(this).find('.price').val()) || 0;

         let quantity = parseInt($(this).find('.quantity').val()) || 0;

         let tax = parseFloat($(this).find('.tax').val()) || 0;

         let amount = (price * quantity) + ((price * quantity * tax) / 100);

         let subamount = (price * quantity);

         totalQuantity += quantity;

         totalProducts += 1;

         totalAmount += amount;

         totalTax += (price * quantity * tax) / 100;

         subTotal += subamount;

      });



// Update the calculated values on the UI

      $('#total_quantity').text(totalQuantity);

      $('#total_products').text(totalProducts);

      $('#sub_total_amount').text(subTotal.toFixed(2));

      $('#total_tax').text(totalTax.toFixed(2));

      $('#total_amount').text(totalAmount.toFixed(2));

   }

</script>

<script type="text/javascript">

   var index = 0;

   $(document).on('click', '#add_product', function() {

      var add_product = `<div class="row calculate">

            <div class="col-md-2">

                <div class="form-floating form-floating-outline mb-3">

                    <input type="text" class="form-control @error('item') is-invalid @enderror" id="item" name="addmore[${index}][name]" value="{{ old('item') }}" placeholder="Item" required data-name="name">

                    <label class="form-label" for="item">item</label>

                    @error('item')

                    <div class="invalid-feedback">{{ $message }}</div>

                    @enderror

                </div>

            </div>

            <div class="col-md-1">

                <div class="form-floating form-floating-outline mb-3">

                    <input type="number" class="form-control @error('price') is-invalid @enderror price" id="price" name="addmore[${index}][price]" value="0" placeholder="Price" required data-name="price">

                    <label class="form-label" for="price">Price</label>

                    @error('price')

                    <div class="invalid-feedback">{{ $message }}</div>

                    @enderror

                </div>

            </div>

            <div class="col-md-1">

                <div class="form-floating form-floating-outline mb-3">

                    <input type="number" class="form-control @error('quantity') is-invalid @enderror quantity" id="quantity" name="addmore[${index}][quantity]" value="0" placeholder="Quantity" required data-name="quantity">

                    <label class="form-label" for="quantity">Quantity</label>

                    @error('quantity')

                    <div class="invalid-feedback">{{ $message }}</div>

                    @enderror

                </div>

            </div>

            <div class="col-md-1">

                <div class="form-floating form-floating-outline mb-3">

                    <input type="number" class="form-control @error('tax') is-invalid @enderror tax" id="tax" name="addmore[${index}][tax]" value="0" placeholder="Tax" data-name="tax">

                    <label class="form-label" for="tax">Tax</label>

                    @error('tax')

                    <div class="invalid-feedback">{{ $message }}</div>

                    @enderror

                </div>

            </div>

            <div class="col-md-2">

                <div class="form-floating form-floating-outline mb-3">

                    <input type="number" class="form-control @error('amount') is-invalid @enderror amount" id="amount" name="addmore[${index}][amount]" value="0" placeholder="Amount" required data-name="amount" style="background-color: #f8f9fa; cursor: not-allowed;">

                    <label class="form-label" for="amount">Amount</label>

                    @error('amount')

                    <div class="invalid-feedback">{{ $message }}</div>

                    @enderror

                </div>

            </div>

            <div class="col-md-2">

                <div class="form-floating form-floating-outline mb-3">

                    <input type="date" class="datepicker form-control @error('start_date') is-invalid @enderror" id="start_date" name="addmore[${index}][start_date]" value="{{ old('start_date') }}" placeholder="Start Date" data-name="start_date" onclick="this.showPicker();">

                    <label class="form-label" for="start_date">Start Date</label>

                    @error('start_date')

                    <div class="invalid-feedback">{{ $message }}</div>

                    @enderror

                </div>

            </div>

            <div class="col-md-2">

                <div class="form-floating form-floating-outline mb-3">

                    <input type="date" class="datepicker form-control @error('end_date') is-invalid @enderror" id="end_date" name="addmore[${index}][end_date]" value="{{ old('end_date') }}" placeholder="End Date" data-name="end_date" onclick="this.showPicker();">

                    <label class="form-label" for="end_date">End Date</label>

                    @error('end_date')

                    <div class="invalid-feedback">{{ $message }}</div>

                    @enderror

                </div>

            </div>

            <div class="col-md-1 d-flex justify-content-center align-items-center">

                <div class="delete_product mb-3" style="cursor:pointer;" data-index="${index}"><i class="mdi mdi-trash-can-outline"></i></div>

            </div>

      </div>`;



      $('.add_product').append(add_product);

      index++;



      if (index > 0) {

         $('#deal_value').val(0);

         $('#deal_value').prop('readonly', true);

      }

   });

   $(document).on('click', '.delete_product', function() {

      var index = $(this).data('index');

// Remove the product row

      $(this).closest('.row.calculate').remove();



// Update the total count and values

      updateTotal();

   }); 



   function updateTotal() {

      var totalQuantity = 0;

      var totalProducts = 0;

      var subTotal = 0;

      var totalTax = 0;

      var totalAmount = 0;



      $('.calculate').each(function() {

         var quantity = parseInt($(this).find('.quantity').val()) || 0;

         var price = parseFloat($(this).find('.price').val()) || 0;

         var tax = parseFloat($(this).find('.tax').val()) || 0;

         totalQuantity += quantity;

         totalProducts++;

         subTotal += (price * quantity);

         totalTax += ((tax * price * quantity) / 100);

         totalAmount += (price * quantity) + ((tax * price * quantity) / 100);

      });



      $('#total_quantity').text(totalQuantity);

      $('#total_products').text(totalProducts);

      $('#sub_total_amount').text(subTotal.toFixed(2));

      $('#total_tax').text(totalTax.toFixed(2));

      $('#total_amount').text(totalAmount.toFixed(2));



// Show or hide the delete option based on the product count

      if (totalProducts <= 1) {

         $('.delete_product').hide();

      } else {

         $('.delete_product').show();

      }

   }

// Initially call updateTotal to handle the existing product count

   updateTotal();

</script>

<script type="text/javascript">

   $(document).on("click",".modal-close",function(){

      $("#docusign_div_sign_frame").addClass("modal fade");

      $("#docusign_div_sign_frame").removeClass("modal show");

      $("#docusign_div_sign_frame").css("display","none");

   });

   $(document).on('input', '.price, .quantity, .tax', function() {

      let minValue = parseInt($(this).attr('min')) || 0;

      let currentValue = parseInt($(this).val()) || 0;



// Reset to minValue if currentValue is less than minValue

      if (currentValue < minValue) {

         $(this).val(minValue);

      }

   });

   $(document).on('change', '.tax', '.amount', function() {

      let value = parseFloat($(this).val());

      if (isNaN(value) || value < 0) {

$(this).val(0); // Reset invalid values

} else {

$(this).val(value.toFixed(2)); // Ensure two decimal places

}

});

// Prevent decrement button from going below min

   $(document).on('keydown', '.price, .quantity, .tax', function(event) {

      if (event.key === "ArrowDown" || event.which === 40) {

         let minValue = parseInt($(this).attr('min')) || 0;

         let currentValue = parseInt($(this).val()) || 0;

         if (currentValue <= minValue) {

event.preventDefault(); // Block further decrement

}

}

});

</script>

@endsection