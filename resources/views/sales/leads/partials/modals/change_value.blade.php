<div class="form-body">
	<div class="row">
		<div class="col">
			<input type="hidden" name="modal-type" value="value">
			<div class="form-group mb-3">
				<label for="labels">Value (optional)</label>
				<div class="input-group mb-3">
                    <span class="input-group-text">{{ $global_currency->symbol }}</span>
                    <input type="number" class="form-control @error('lead_value') is-invalid @enderror" aria-label="Amount (to the nearest pound)" name="lead_value" id="lead_value" value="{{ old('value') ? old('value') : $lead->value }}" placeholder="0.00" min="0" step="0.01" onkeypress="return isNumberKey(event);">
                </div>
                <input type="hidden" name="currency" value="{{ old('currency') ? old('currency') : 1 }}">
                @error('lead_value')
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