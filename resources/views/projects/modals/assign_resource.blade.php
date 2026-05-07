<div class="row">
	<div class="col-md-12">
		<div class="form-floating form-floating-outline mb-3">
            <select class="form-select @error('assign') is-invalid @enderror select2" name="assign[]" id="assign" required>
                @if($users->count() > 0)
                    @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                    @endforeach
                @endif
            </select>
            <label class="form-label" for="assign">{{ __('Assign') }} <span class="text-danger">*</span></label>
            @error('assign')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
	</div>
</div>
<div class="row">
	<div class="col-md-12 text-end">
		<button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
	</div>
</div>