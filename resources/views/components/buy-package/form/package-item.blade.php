@props(['item', 'type'])

<div class="d-flex justify-content-between align-items-center border p-3 mb-2 rounded">
    <div>
        @if($type === 'package')
            <strong>{{ $item['name'] }}:</strong> ({{ $item['big_zones'] }} zona + {{ $item['mini_zones'] }} mini zonas)
        @else
            <strong>{{ $item['name'] }}</strong>
        @endif
        <div class="text-muted">${{ number_format($item['price'], 0, ',', '.') }}</div>
    </div>
    <div>
        <label for="quantity-{{ $type }}-{{ $item['id'] }}" class="form-label visually-hidden">Cantidad</label>
        <input type="number"
           class="form-control item-amount"
           style="width: 80px;"
           min="0"
           value="0"
           id="quantity-{{ $type }}-{{ $item['id'] }}"
           data-id="{{ $item['id'] }}"
           data-type="{{ $type }}"
        >
    </div>
</div>
