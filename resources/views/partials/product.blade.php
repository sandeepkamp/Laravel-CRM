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
        <input type="number" class="form-control @error('price') is-invalid @enderror price" id="price" name="addmore[{{$key}}][price]" value="{{$product->price}}" placeholder="Price" required data-name="price">
        <label class="form-label" for="price" min="1" step="1">Price</label>
      </div>
    </div>
    <div class="col-md-1">
      <div class="form-floating form-floating-outline mb-3">
        <input type="number" class="form-control @error('quantity') is-invalid @enderror quantity" id="quantity" name="addmore[{{$key}}][quantity]" value="{{$product->quantity}}" placeholder="Quantity" required data-name="quantity">
        <label class="form-label" for="quantity" min="1" step="1">Quantity</label>
      </div>
    </div>
    <div class="col-md-1">
      <div class="form-floating form-floating-outline mb-3">
        <input type="number" class="form-control @error('tax') is-invalid @enderror tax" id="tax" name="addmore[{{$key}}][tax]" value="{{$product->tax}}" placeholder="Tax"  data-name="tax">
        <label class="form-label" for="tax" min="0" step="1">Tax</label>
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-floating form-floating-outline mb-3">
        <input type="number" class="form-control @error('amount') is-invalid @enderror amount" id="amount" name="addmore[{{$key}}][amount]" value="{{$product->price*$product->quantity+(($product->tax*$product->price*$product->quantity)/100)}}" placeholder="Amount" required readonly data-name="amount" style="background-color: #f8f9fa; cursor: not-allowed;">
        <label class="form-label" for="amount">Amount</label>
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-floating form-floating-outline mb-3">
        <input type="date" class="datepicker form-control @error('start_date') is-invalid @enderror" id="start_date" name="addmore[{{$key}}][start_date]" value="{{ optional($product->subscription)->start_date ? date('Y-m-d', strtotime($product->subscription->start_date)) : '' }}" placeholder="Start Date" required data-name="start_date" onclick="this.showPicker();">
        <label class="form-label" for="start_date">Start Date</label>
      </div>
    </div>
    <div class="col-md-2">
      <div class="form-floating form-floating-outline mb-3">
        <input type="date" class="datepicker form-control @error('end_date') is-invalid @enderror" id="end_date" name="addmore[{{$key}}][end_date]" value="{{ optional($product->subscription)->end_date ? date('Y-m-d', strtotime($product->subscription->end_date)) : '' }}" placeholder="End Date" data-name="end_date" onclick="this.showPicker();">
        <label class="form-label" for="end_date">End Date</label>
      </div>
    </div>
    <!--<div class="col-md-1 d-flex justify-content-center align-items-center">-->
    <!--  <div class="delete_product pe-auto" style="cursor:pointer;"><i class="mdi mdi-trash-can-outline"></i></div>-->
    <!--</div>-->
    <div class="col-md-1 d-flex justify-content-center align-items-center">
        @if(count($deal->products) > 1)
        <div class="delete_product pe-auto" style="cursor:pointer;"><i class="mdi mdi-trash-can-outline"></i></div>
        @endif
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