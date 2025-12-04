@csrf
<div class="col-12 col-lg-6">
    <div class="form-floating">
        <input id="name" type="text" placeholder="Nombre del Producto"
            class="form-control @error('name') is-invalid @enderror"
            name="name" value="{{ old('name', $product->name ?? '') }}">
        <label for="name">Nombre del Producto</label>
        @error('name')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="col-12 col-lg-6">
    <div class="form-floating">
        <select id="branch_id" name="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
            <option value="">Selecciona una sucursal</option>
            @foreach($branches as $branch)
                <option value="{{ $branch->id }}"
                    {{ (old('branch_id', $product->branch_id ?? '') == $branch->id) ? 'selected' : '' }}>
                    {{ $branch->name }}
                </option>
            @endforeach
        </select>
        <label for="branch_id">Sucursal</label>
        @error('branch_id')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="col-12 col-lg-6">
    <div class="form-floating">
        <input id="stock" type="number" placeholder="Stock"
            class="form-control @error('stock') is-invalid @enderror"
            name="stock" value="{{ old('stock', $product->stock ?? '') }}" min="0">
        <label for="stock">Stock</label>
        @error('stock')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="col-12 col-lg-6">
    <div class="form-floating">
        <input id="price" type="number"
       step="0.01"
       placeholder="Precio (COP)"
       class="form-control @error('price') is-invalid @enderror"
       name="price"
       value="{{ old('price', $product->price ?? '') }}"
       min="0">
        <label for="price">Precio (Pesos Colombianos)</label>
        @error('price')
            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
        @enderror
    </div>
</div>

<div class="col-12 text-center mt-3">
    <button type="submit" class="btn btn-primary w-auto">
        {{ isset($product) ? 'Actualizar Producto' : 'Guardar Producto' }}
    </button>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary w-auto">Cancelar</a>
</div>
