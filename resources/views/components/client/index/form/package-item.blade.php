@props(['item', 'type'])

<div class="d-flex justify-content-between align-items-center border p-3 mb-2 rounded item-row">

    <!-- 1. Checkbox agregado al inicio -->
    <div class="form-check me-3">
        <input class="form-check-input trigger-checkbox"
               type="checkbox"
               value=""
               id="check-{{ $type }}-{{ $item['id'] }}"
               data-target="quantity-{{ $type }}-{{ $item['id'] }}">
    </div>

    <!-- Información del Item -->
    <div class="flex-grow-1">
        @if($type === 'package')
            <strong>{{ $item['name'] }}:</strong> ({{ $item['big_zones'] }} zona + {{ $item['mini_zones'] }} mini zonas)
        @else
            <strong>{{ $item['name'] }}</strong>
        @endif
        <div class="text-muted">${{ number_format($item['price'], 0, ',', '.') }}</div>
    </div>

    <!-- Input Numérico -->
    <div>
        <label for="quantity-{{ $type }}-{{ $item['id'] }}" class="form-label visually-hidden">Cantidad</label>
        <input type="number"
           class="form-control item-amount"
           name="{{ $type === 'package' ? 'package' : 'additional' }}[{{ $item['id'] }}]"
           style="width: 80px;"
           min="0"
           value="0"
           id="quantity-{{ $type }}-{{ $item['id'] }}"
           data-id="{{ $item['id'] }}"
           data-type="{{ $type }}"
        >
    </div>
</div>
