document.addEventListener('DOMContentLoaded', function () {
    // Referencias al DOM
    const headerBranchSelect = document.getElementById('branch-selector');
    const formBranchInput = document.getElementById('form_branch_id');

    // Contenedores Productos
    const productsContainer = document.getElementById('products-container');
    const productSearchInput = document.getElementById('product-search');

    // Contenedores Pacientes
    const patientSearchInput = document.getElementById('patient-search');
    const patientSelect = document.getElementById('patient-select');

    // Carrito y Formulario
    const cartTableBody = document.getElementById('cart-table-body');
    const cartTotalDisplay = document.getElementById('cart-total');
    const cartJsonInput = document.getElementById('cart_items_json');
    const submitBtn = document.getElementById('btn-submit');
    const saleForm = document.getElementById('sale-form');

    // Estado del Carrito
    let cart = {}; // { id: { qty: 1, name: '...', price: 100, max: 10 } }
    let debounceTimer;

    // --- 1. Inicialización y Sincronización ---

    const init = () => {
        if (headerBranchSelect) {
            formBranchInput.value = headerBranchSelect.value;
            loadProducts();
            loadPatients(); // Cargar inicial (probablemente vacío hasta que busque)

            headerBranchSelect.addEventListener('change', function () {
                formBranchInput.value = this.value;
                clearCart(); // Limpiar carrito al cambiar sucursal para evitar inconsistencias
                loadProducts();
                loadPatients();
            });
        }
    };

    // --- 2. Carga de Datos (AJAX) ---

    const loadProducts = () => {
        productsContainer.innerHTML = '<div class="col-12 text-center py-4"><div class="spinner-border text-primary"></div></div>';

        const url = new URL(urls.products);
        if (formBranchInput.value) url.searchParams.append('branch_id', formBranchInput.value);
        if (productSearchInput.value) url.searchParams.append('search', productSearchInput.value);

        fetch(url)
            .then(res => res.text())
            .then(html => {
                productsContainer.innerHTML = html;
                attachProductListeners();
            })
            .catch(err => console.error('Error loading products:', err));
    };

    const loadPatients = () => {
        // Solo buscamos si hay una sucursal seleccionada
        if (!formBranchInput.value) {
            patientSelect.innerHTML = '<option value="">Seleccione una sucursal primero</option>';
            return;
        }

        const url = new URL(urls.patients);
        url.searchParams.append('branch_id', formBranchInput.value);
        if (patientSearchInput.value) url.searchParams.append('search', patientSearchInput.value);

        fetch(url)
            .then(res => res.json())
            .then(data => {
                patientSelect.innerHTML = '';
                if (data.length === 0) {
                    const opt = document.createElement('option');
                    opt.text = "No se encontraron pacientes";
                    opt.disabled = true;
                    patientSelect.add(opt);
                } else {
                    // Placeholder si no ha buscado
                    if(!patientSearchInput.value){
                         const placeholder = document.createElement('option');
                         placeholder.text = "Seleccione un paciente...";
                         placeholder.value = "";
                         placeholder.selected = true;
                         placeholder.disabled = true;
                         patientSelect.add(placeholder);
                    }

                    data.forEach(user => {
console.log(user)
                        const opt = document.createElement('option');
                        opt.value = user.id;
                        opt.text = `${user.name} (${user.email})`;
                        patientSelect.add(opt);
                    });
                }
            })
            .catch(err => console.error('Error loading patients:', err));
    };

    // --- 3. Listeners de Búsqueda (Debounce) ---

    const handleDebounce = (callback) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(callback, 500);
    };

    if (productSearchInput) {
        productSearchInput.addEventListener('input', () => handleDebounce(loadProducts));
    }

    if (patientSearchInput) {
        // Al escribir en el buscador de pacientes, recargamos el select
        patientSearchInput.addEventListener('input', () => handleDebounce(loadPatients));
    }

    // --- 4. Lógica del Carrito ---

    const attachProductListeners = () => {
        document.querySelectorAll('.btn-add-cart').forEach(btn => {
            btn.addEventListener('click', function() {
                const card = this.closest('.product-card');
                const id = card.dataset.id;
                const name = card.dataset.name;
                const price = parseFloat(card.dataset.price);
                const stock = parseInt(card.dataset.stock);

                addToCart(id, name, price, stock);
            });
        });
    };

    const addToCart = (id, name, price, maxStock) => {
        if (cart[id]) {
            if (cart[id].qty < maxStock) {
                cart[id].qty++;
            } else {
                alert('No hay más stock disponible de este producto.');
            }
        } else {
            cart[id] = { qty: 1, name, price, max: maxStock };
        }
        renderCart();
    };

    const removeFromCart = (id) => {
        delete cart[id];
        renderCart();
    };

    const updateQty = (id, newQty) => {
        if (cart[id]) {
            let qty = parseInt(newQty);
            if (qty <= 0) {
                removeFromCart(id);
                return;
            }
            if (qty > cart[id].max) {
                qty = cart[id].max;
                alert(`Solo hay ${qty} unidades disponibles.`);
            }
            cart[id].qty = qty;
            renderCart();
        }
    };

    const clearCart = () => {
        cart = {};
        renderCart();
    };

    const renderCart = () => {
        cartTableBody.innerHTML = '';
        let total = 0;
        let itemCount = 0;
        const itemsArray = [];

        Object.keys(cart).forEach(id => {
            const item = cart[id];
            const subtotal = item.qty * item.price;
            total += subtotal;
            itemCount++;

            itemsArray.push({ id: id, qty: item.qty });

            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="small text-truncate" style="max-width: 150px;" title="${item.name}">${item.name}</td>
                <td>
                    <input type="number" class="form-control form-control-sm qty-input"
                           data-id="${id}" value="${item.qty}" min="1" max="${item.max}">
                </td>
                <td class="text-end small">$ ${new Intl.NumberFormat('es-CO').format(subtotal)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-xs btn-danger btn-remove" data-id="${id}"><i class="bi bi-x"></i></button>
                </td>
            `;
            cartTableBody.appendChild(row);
        });

        if (itemCount === 0) {
            cartTableBody.innerHTML = '<tr><td colspan="4" class="text-center text-muted small py-3">Carrito vacío</td></tr>';
            submitBtn.disabled = true;
        } else {
            submitBtn.disabled = false;
        }

        cartTotalDisplay.textContent = '$ ' + new Intl.NumberFormat('es-CO').format(total);
        cartJsonInput.value = JSON.stringify(itemsArray);

        // Reasignar eventos dentro de la tabla
        document.querySelectorAll('.btn-remove').forEach(btn => {
            btn.addEventListener('click', () => removeFromCart(btn.dataset.id));
        });

        document.querySelectorAll('.qty-input').forEach(input => {
            input.addEventListener('change', function() {
                updateQty(this.dataset.id, this.value);
            });
        });
    };

    // Validación final antes de enviar
    if (saleForm) {
        saleForm.addEventListener('submit', function(e) {
            if (!patientSelect.value) {
                e.preventDefault();
                alert('Por favor selecciona un paciente.');
                return;
            }
            if (Object.keys(cart).length === 0) {
                e.preventDefault();
                alert('El carrito está vacío.');
                return;
            }
            if(!confirm('¿Confirmar venta y generar cobro?')){
                 e.preventDefault();
            }
        });
    }

    // Arrancar
    init();
});
