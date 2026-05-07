<div class="form-body">
	<div class="row">
		<div class="col">
			<input type="hidden" name="modal-type" value="labels">
			<div class="form-group mb-3">
				<label for="labels">Labels</label>
				<select class="form-control select2 @error('labels') is-invalid @enderror" name="labels[]" id="labels" multiple>
	                <option value="">-- select --</option>
	                @foreach ($labels as $label)
	                    <option value="{{ $label->id }}" {!! in_array($label->id, $previous_label) ? 'selected="selected"' : '' !!}>{{ $label->name }}</option>
	                @endforeach
	            </select>
	            @error('labels')
	            <div class="form-text invalid-feedback">{{ $message }}</div>
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