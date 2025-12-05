document.addEventListener('DOMContentLoaded', function () {
    const filterForm = document.getElementById('filter-form');
    const dateFrom = document.getElementById('date_from');
    const dateTo = document.getElementById('date_to');
    const tableContainer = document.getElementById('orders-table-container');

    let debounceTimer;

    const fetchOrders = () => {
        const url = new URL(filterForm.action);
        const params = new URLSearchParams();

        if (dateFrom.value) params.append('date_from', dateFrom.value);
        if (dateTo.value) params.append('date_to', dateTo.value);

        url.search = params.toString();

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.text())
        .then(html => {
            tableContainer.innerHTML = html;
        })
        .catch(error => console.error('Error fetching orders:', error));
    };

    const handleInput = () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            fetchOrders();
        }, 500);
    };

    if (dateFrom) dateFrom.addEventListener('change', handleInput); // 'change' es mejor para datepickers
    if (dateTo) dateTo.addEventListener('change', handleInput);
});
