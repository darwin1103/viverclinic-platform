@props(['zone', 'type'])

<div class="form-check">
    <input class="form-check-input checkbox-zone"
           name="selected_zones[{{ $type }}][]"
           type="checkbox"
           value="{{ $zone }}"
           id="zone-{{ Str::slug($zone) }}"
           data-type="{{ $type }}">
    <label class="form-check-label" for="zone-{{ Str::slug($zone) }}">
        {{ $zone }}
    </label>
</div>
