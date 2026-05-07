@extends('app')

@section('title', 'Edit New Deal')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item">
        <a href="{{ route('sales_dashboard') }}">Sales Dashboard</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('deal.list') }}">List</a>
    </li>
    <li class="breadcrumb-item">Edit New Deal</li>
</ol>
@endsection

@section('content')
@if($errors->any())
    {{ implode('', $errors->all('<div>:message</div>')) }}
@endif
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                @if(session()->has('success'))
                <div class="alert alert-success alert-dismissible bg-success text-white border-0 fade show">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <strong>Success -</strong>
                    {{ session()->get('success') }}
                </div>
                @endif
                <form method="POST" action="{{ route('deal.update',$deal->id) }}" id="dealform">
                    @csrf
                    @method('PUT')
                    <div class="form-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name',$deal->first_name) }}" placeholder="First Name" required>
                                    <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>
                                    @error('first_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name',$deal->last_name)}}" placeholder="Last Name">
                                    <label class="form-label" for="last_name">Last Name</label>
                                    @error('last_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email',$deal->email) }}" placeholder="Email" required>
                                    <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                                    @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number',$deal->phone) }}" placeholder="Phone Number" required>
                                    <label class="form-label" for="phone_number">Phone Number <span class="text-danger">*</span></label>
                                    @error('phone_number')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        {{-- <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="owner">Visible To</label>
                                    <select class="form-control select2 @error('owner') is-invalid @enderror" name="visible_to" id="owner" required>
                                        <option value="">-- select --</option>
                                        @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @if($user->id == old('visible_to',$deal->visible_to)) {{ 'selected="selected"' }} @endif>{{ $user->first_name.' '.$user->last_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('owner')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group form-floating-outline mb-3">
                                    <label class="form-label" for="title">Title</label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="designation" name="title" value="{{ old('title',$deal->title) }}" placeholder="Title">
                                    
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div> --}}
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name',$deal->organisation_detail->name) }}" placeholder="Company Name" required>
                                    <label class="form-label" for="company_name">Company Name <span class="text-danger">*</span></label>
                                    @error('company_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website',$deal->website) }}" placeholder="Website">
                                    <label class="form-label" for="website">Website</label>
                                    @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title',$deal->title) }}" placeholder="Title" required>
                                     <label class="form-label" for="title">Client Name <span class="text-danger">*</span></label>
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="datepicker form-control @error('close_date') is-invalid @enderror" id="close_date" name="close_date" value="{{ old('close_date',$deal->close_date) }}" placeholder="dd/mm/yyyy" required>
                                    <label class="form-label" for="close_date">Expected Close Date <span class="text-danger">*</span></label>
                                    @error('close_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            {{-- <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="website">Website</label>
                                    <input type="text" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('website',$deal->website) }}" placeholder="Website" required>
                                    @error('website')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}
                           {{--  <div class="col-md-6"> --}}
                              {{--   <div class="form-group mb-3">
                                    <label class="form-label" for="labels">Labels</label>
                                    <select class="form-control select2 @error('labels') is-invalid @enderror" name="labels[]" id="labels" multiple>
                                        <option value="">-- select --</option>
                                        @foreach ($labels as $label)
                                        <option value="{{ $label->id }}">{{ $label->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('labels')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div> --}}
                            {{-- </div> --}}
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="deal_value">Value <span class="text-danger">*</span></label>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text">{{ $global_currency->symbol }}</span>
                                        <input type="text" class="form-control @error('deal_value') is-invalid @enderror" aria-label="Amount (to the nearest pound)" name="deal_value" id="deal_value" value="{{ old('deal_value',$deal->value) }}" placeholder="0.00" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                    </div>
                                    <input type="hidden" name="currency" value="{{ old('currency') ? old('currency') : 1 }}">
                                    <small class="text-muted d-block mt-1">Add products to calculate the price.</small>
                                    @error('deal_value')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label class="form-label" for="owner">Stages <span class="text-danger">*</span></label>
                                    <select class="form-control select2 @error('stage') is-invalid @enderror" name="stage" id="stage" required>
                                        <option value="">-- select --</option>
                                        @foreach ($stages as $stage)
                                            <option value="{{ $stage->id }}" 
                                                @if(isset($deal->stage) && $deal->stage->stage == $stage->id) selected @endif>
                                                {{ $stage->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('stage')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                          <!--<div class="row">-->
                          <!--  <div class="col-md-6">-->
                          <!--      <div class="form-floating form-floating-outline mb-3">-->
                          <!--          <select class="form-control select2 @error('owner') is-invalid @enderror" name="owner" id="owner" required>-->
                          <!--              <option value="">-- select --</option>-->
                          <!--              @foreach ($users as $user)-->
                          <!--              <option value="{{ $user->id }}" @if($user->id == old('owner',$deal->owner)) {{ 'selected="selected"' }} @endif>{{ $user->first_name.' '.$user->last_name }}</option>-->
                          <!--              @endforeach-->
                          <!--          </select>-->
                          <!--          <label class="form-label" for="owner">Owner</label>-->
                          <!--          @error('owner')-->
                          <!--          <div class="invalid-feedback">{{ $message }}</div>-->
                          <!--          @enderror-->
                          <!--      </div>-->
                          <!--  </div>-->
                          <!--  <div class="col-md-6">-->
                          <!--     <div class="form-floating form-floating-outline mb-3">-->
                          <!--          <select class="form-control select2 @error('visible_to') is-invalid @enderror" name="visible_to" id="visible_to">-->
                          <!--              <option value="">All Users</option>-->
                          <!--              @foreach ($users as $user)-->
                          <!--              <option value="{{ $user->id }}" @if($user->id == old('visible_to',$deal->visible_to)) {{ 'selected="selected"' }} @endif>{{ $user->first_name.' '.$user->last_name }}</option>-->
                          <!--              @endforeach-->
                          <!--          </select>-->
                          <!--          <label class="form-label" for="visible_to">Visible To</label>-->
                          <!--          @error('visible_to')-->
                          <!--          <div class="invalid-feedback">{{ $message }}</div>-->
                          <!--          @enderror-->
                          <!--      </div>-->
                          <!--  </div>-->
                             {{-- <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-3">
                                    <label class="form-label" for="owner">Stages</label>
                                    <select class="form-control select2 @error('organization') is-invalid @enderror" name="stage" id="organization" required>
                                        <option value="">-- select --</option>
                                        @foreach ($stages as $stage)
                                        <option value="{{ $user->id }}" @if($stage->id == old('stage',$deal->stage->id)) {{ 'selected="selected"' }} @endif>{{ $stage->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('organization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div> --}}
                        </div>
                        <div class="row add_product">
                           @foreach($deal->products as $key=>$product)
                            <div class="row calculate">
                            <div class="col-md-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="text" class="form-control @error('item') is-invalid @enderror" id="item" name="addmore[{{$key}}][name]" value="{{$product->name}}" placeholder="Item" required data-name="name">
                                    <label class="form-label" for="item">item</label>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" class="form-control @error('price') is-invalid @enderror price" id="close_date" name="addmore[{{$key}}][price]" value="{{$product->price}}" placeholder="Price" required data-name="price">
                                    <label class="form-label" for="close_date">Price</label>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror quantity" id="close_date" name="addmore[{{$key}}][quantity]" value="{{$product->quantity}}" placeholder="Quantity" required data-name="quantity">
                                    <label class="form-label" for="close_date">Quantity</label>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" class="form-control @error('tax') is-invalid @enderror tax" id="close_date" name="addmore[{{$key}}][tax]" value="{{$product->tax}}" placeholder="Tax" data-name="tax">
                                    <label class="form-label" for="close_date">Tax</label>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror amount" id="close_date" name="addmore[{{$key}}][amount]" value="{{$product->price*$product->quantity+(($product->tax*$product->price*$product->quantity)/100)}}" placeholder="Amount" required data-name="amount"  step="0.01" 
                                    min="0" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                    <label class="form-label" for="close_date">Amount</label>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="date" class="datepicker form-control @error('start_date') is-invalid @enderror" id="close_date" name="addmore[{{$key}}][start_date]" value="{{ optional($product->subscription)->start_date ? date('Y-m-d', strtotime($product->subscription->start_date)) : '' }}" placeholder="Start Date" required data-name="start_date" onclick="this.showPicker();">
                                    <label class="form-label" for="start_date">Start Date</label>
                                </div>
                            </div>
                             <div class="col-md-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="date" class="datepicker form-control @error('end_date') is-invalid @enderror" id="close_date" name="addmore[{{$key}}][end_date]" value="{{ optional($product->subscription)->end_date ? date('Y-m-d', strtotime($product->subscription->end_date)) : '' }}" placeholder="End Date" data-name="end_date" onclick="this.showPicker();">
                                    <label class="form-label" for="close_date">End Date</label>
                                </div>
                            </div>
                            <div class="col-md-1 d-flex justify-content-center align-items-center">
                                <div class="delete_product pe-auto" style="cursor:pointer;"><i class="mdi mdi-trash-can-outline"></i></div>
                            </div>
                        </div>
                           @endforeach
                        </div>
                        <div class="form-actions">
                            <a href="javascript:void(0)" id="add_product" class="btn btn-outline-primary btn-sm waves-effect">
                                <span class="tf-icons mdi mdi-plus me-1"></span>
                                Product
                            </a>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <label>Total Quantity</label>
                                <p id="total_quantity">0</p>
                            </div>
                            <div class="col-md-2">
                                <label>Number of Products</label>
                                <p id="total_products">0</p>
                            </div>
                            <div class="col-md-2">
                                <label>Sub Total</label>
                                <p id="sub_total_amount">0.00</p>
                            </div>
                             <div class="col-md-2">
                                <label>Tax</label>
                                <p id="total_tax">0.00</p>
                            </div>
                            <div class="col-md-2">
                                <label>Total Amount</label>
                                <p id="total_amount">0.00</p>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="text-end">
                                <button type="submit" class="btn btn-primary" name="submit">UPDATE</button>
                            </div>
                        </div>
                </form>
            </div>
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
@endsection

@section('script')
<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js" integrity="sha512-LsnSViqQyaXpD4mBBdRYeP6sRwJiJveh2ZIbW41EBrNmKxgr/LFZIiWT6yr+nycvhvauz8c2nYMhrP80YhG7Cw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
        });
        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            todayHighlight: true,
            autoclose: true,
            todayBtn: true,
            startDate: new Date()   
        });
    });
</script>
<script type="text/javascript">
    var index = parseInt({{ count($deal->products) }});
      // Check if products are already added in edit mode
      if (index > 0) {
         $('#deal_value').prop('readonly', true); // Set readonly if products exist
    }
      
    $(document).ready(function () {
    // Function to toggle the visibility of delete buttons
    function toggleDeleteButtons() {
        const totalProducts = $('.calculate').length;
        if (totalProducts <= 1) {
            $('.delete_product').hide(); // Hide delete button when only one product
        } else {
            $('.delete_product').show(); // Show delete button when more than one product
        }
    }

    // Initial call to toggle delete buttons on page load
    toggleDeleteButtons();

    // Add product button click event
    $('#add_product').click(function () {
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
                                    <input type="number" class="form-control @error('quantity') is-invalid @enderror quantity" id="quantity" name="addmore[${index}][quantity]" value="1" placeholder="Quantity" required data-name="quantity">
                                    <label class="form-label" for="quantity">Quantity</label>
                                    @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" step="0.01" class="form-control @error('tax') is-invalid @enderror tax" id="tax" name="addmore[${index}][tax]" value="0" placeholder="Tax" step="0.01" 
                                    min="0" data-name="tax">
                                    <label class="form-label" for="tax">Tax</label>
                                    @error('tax')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="number" class="form-control @error('amount') is-invalid @enderror amount" id="amount" name="addmore[${index}][amount]" value="0" placeholder="Amount"  step="0.01" 
                                    min="0" required data-name="amount" readonly style="background-color: #f8f9fa; cursor: not-allowed;">
                                    <label class="form-label" for="amount">Amount</label>
                                    @error('amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-floating form-floating-outline mb-3">
                                    <input type="date" class="datepicker form-control @error('start_date') is-invalid @enderror" id="start_date" name="addmore[${index}][start_date]" value="{{ old('start_date') }}" placeholder="Start Date" required data-name="start_date" onclick="this.showPicker();">
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
                                <div class="delete_product pe-auto" style="cursor:pointer;"><i class="mdi mdi-trash-can-outline"></i></div>
                            </div>
                        </div>`;
        $('.add_product').append(add_product);
        index++;

        toggleDeleteButtons(); // Check delete button visibility
        });
    
        // Delete product button click event
        $(document).on('click', '.delete_product', function () {
            $(this).parents('.calculate').remove();
            toggleDeleteButtons(); // Check delete button visibility
            calculate_totals(); // Update totals
        });
    });
</script>
<script type="text/javascript">
    $("form").submit(function(e){
        var error = 0;
        $('.calculate').each(function(i,v){
            $(this).find('input').each(function(){
                // $(this).attr('name','addmore['+i+']['+$(this).attr('data-name')+']');
                if($(this).prop('required') === true && ($(this).val()==0 || $(this).val()=='')){
                    $(this).addClass('is-invalid');
                    error++;
                }else{
                    $(this).removeClass('is-invalid');
                }
            });
        })
        if(error>0){
            return false;
        }
    });
</script>
<script type="text/javascript">
 
    $(document).on('keyup','.price',function(){
        calculate_amount(this);
    });
    $(document).on('change','.price',function(){
        calculate_amount(this);
    });
    $(document).on('keyup','.quantity',function(){
        calculate_amount(this);
    });
    $(document).on('change','.quantity',function(){
        calculate_amount(this);
    })
    $(document).on('keyup','.tax',function(){
        calculate_amount(this);
    });
    $(document).on('change','.tax',function(){
        calculate_amount(this);
    });
    
    $(document).ready(function() {
    // Call the function to calculate the totals when the page loads
    calculate_totals();

    // Update the totals whenever the user changes values in the form
    $(document).on('keyup change', '.price, .quantity, .tax', function() {
        calculate_amount(this);
    });

    $(document).on('change', '.price, .quantity, .tax', function() {
        calculate_amount(this);
    });

    // When adding a new product, trigger the total calculation
    $(document).on('click', '.add_product', function() {
        // Add new product to the list 
        // $('.product-list').append('<div class="calculate">...</div>');
        calculate_totals();  // Recalculate totals when a new product is added
    });

    window.calculate_amount = function(param) {
        let price = parseFloat($(param).parents('.calculate').find('.price').val()) || 0;
        let quantity = parseFloat($(param).parents('.calculate').find('.quantity').val()) || 0;
        let tax = parseFloat($(param).parents('.calculate').find('.tax').val()) || 0;
    
        let amount = (price * quantity) + ((price * quantity * tax) / 100);
    
        let amountField = $(param).parents('.calculate').find('.amount');
        if (amountField.length > 0) {
            amountField.val(amount.toFixed(2));
        }
    
        calculate_totals();
    };

    function calculate_totals() {
        var totalQuantity = 0;
        var totalProducts = 0;
        var totalAmount = 0;
        var totalTax = 0;
        var subTotal = 0;

        // Loop through each product row and calculate totals
        $('.calculate').each(function() {
            let price = $(this).find('.price').val();
            let quantity = $(this).find('.quantity').val();
            let tax = $(this).find('.tax').val();
            let amount = (price * quantity) + ((price * quantity * tax) / 100);
            let subamount = (price * quantity);

            totalQuantity += parseInt(quantity);
            totalProducts += 1;
            totalAmount += amount;
            totalTax += (price * quantity * tax) / 100;
            subTotal += subamount;
        });

        // Update the calculated values on the UI
        $('#deal_value').val(totalAmount.toFixed(2));
        $('#total_quantity').text(totalQuantity);
        $('#total_products').text(totalProducts);
        $('#total_amount').text(totalAmount.toFixed(2));
        $('#sub_total_amount').text(subTotal.toFixed(2));
        $('#total_tax').text(totalTax.toFixed(2));
    }

    // When the user deletes a product, recalculate totals
    $(document).on('click', '.delete_product', function() {
        $(this).parents('.calculate').remove();
         calculate_row_index();
        calculate_totals();  // Recalculate totals after a product is deleted
        });
    });

    function calculate_row_index(){

        $('.calculate').each(function(i,v){
            $(this).find('input').each(function(){
                $(this).attr('name','addmore['+i+']['+$(this).attr('data-name')+']');
            });
        })
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
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',  // Optional: Adds Bootstrap 5 theme to Select2
        });
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
