@csrf
<div class="form-grid">
    <div class="field">
        <label>Name</label>
        <input name="name" value="{{ old('name', $department->name) }}" required>
    </div>
    <div class="field field-span-2">
        <label>Description</label>
        <textarea name="description">{{ old('description', $department->description) }}</textarea>
    </div>
</div>
<button class="primary-button" type="submit">Save department</button>
