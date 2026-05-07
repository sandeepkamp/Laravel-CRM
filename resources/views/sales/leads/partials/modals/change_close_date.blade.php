<div class="form-body">
	<div class="row">
		<div class="col">
			<input type="hidden" name="modal-type" value="close_date">
			<div class="form-floating form-floating-outline mb-3">
                <input type="text" class="datepicker form-control @error('close_date') is-invalid @enderror" id="close_date" name="close_date" value="{{ old('close_date') ? old('close_date') : $lead->close_date }}" placeholder="yyyy-mm-dd">
                <label class="form-label" for="close_date">Expected Close Date (optional)</label>
                @error('close_date')
                <div class="invalid-feedback form-text">{{ $message }}</div>
                @enderror
            </div>
		</div>
	</div>
</div>
<div class="form-actions">
    <div class="text-end">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
        <button type="submit" class="btn btn-primary" name="submit">UPDATE</button>
    </div>
</div>