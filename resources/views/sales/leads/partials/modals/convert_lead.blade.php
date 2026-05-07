<div class="form-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-floating form-floating-outline mb-3">
                <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') ? old('first_name') : $lead->first_name }}" placeholder="First Name" required>
                <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>
                @error('first_name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating form-floating-outline mb-3">
                <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') ? old('last_name') : $lead->last_name }}" placeholder="Last Name" required>
                <label class="form-label" for="last_name">Last Name <span class="text-danger">*</span></label>
                @error('last_name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-floating form-floating-outline mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') ? old('email') : $lead->email }}" placeholder="Email" required>
                <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating form-floating-outline mb-3">
                <input type="text" class="form-control @error('phone_number') is-invalid @enderror" id="phone_number" name="phone_number" value="{{ old('phone_number') ? old('phone_number') : $lead->phone }}" placeholder="Phone Number">
                <label class="form-label" for="phone_number">Phone Number <span class="text-danger">*</span></label>
                @error('phone_number')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-floating form-floating-outline mb-3">
                <input type="text" class="form-control @error('company_name') is-invalid @enderror" id="company_name" name="company_name" value="{{ old('company_name') ? old('company_name') : $lead->organisation_detail->name }}" placeholder="Company Name">
                <label class="form-label" for="company_name">Company Name <span class="text-danger">*</span></label>
                @error('company_name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating form-floating-outline mb-3">
                <input type="text" class="form-control @error('website') is-invalid @enderror" id="website" name="website" value="{{ old('company_name') ? old('website') : $lead->website }}" placeholder="Website">
                <label class="form-label" for="company_name">Website</label>
                @error('website')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-floating form-floating-outline mb-3">
                <input type="text" class="form-control @error('designation') is-invalid @enderror" id="designation" name="designation" value="{{ old('designation') ? old('designation') : $lead->title }}" placeholder="Designation">
                <label class="form-label" for="designation"> Job Title <span class="text-danger">*</span></label>
                @error('designation')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-floating form-floating-outline mb-3">
                <input type="text" class="datepicker form-control @error('close_date') is-invalid @enderror" id="close_date" name="close_date" value="{{ old('close_date') ? old('close_date') : $lead->close_date }}" placeholder="yyyy/mm/dd" autocomplete="off">
                <label class="form-label" for="close_date">Expected Close Date <span class="text-danger">*</span></label>
                @error('close_date')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label" for="lead_value">Value <span class="text-danger">*</span></label>
                <div class="input-group mb-3">
                    <span class="input-group-text">{{ $global_currency->symbol }}</span>
                    <input type="text" class="form-control @error('lead_value') is-invalid @enderror" aria-label="Amount (to the nearest pound)" name="lead_value" id="lead_value" value="{{ old('value') ? old('value') : $lead->value }}" placeholder="0.00" min="0" required>
                </div>
                <input type="hidden" name="currency" value="{{ old('currency') ? old('currency') : 1 }}">
                @error('lead_value')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="form-label" for="owner">Stages <span class="text-danger">*</span></label>
                <select class="form-control select2 @error('stage') is-invalid @enderror" name="stage" id="organization" required>
                    <option value="">-- select --</option>
                    @foreach ($stages as $stage)
                    <option value="{{ $stage->id }}" @if($stage->id == old('stage')) {{ 'selected="selected"' }} @endif>{{ $stage->name }}</option>
                    @endforeach
                </select>
                @error('stage')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>
</div>
<div class="form-actions">
    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</div>