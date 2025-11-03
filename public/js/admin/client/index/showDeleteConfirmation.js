function showDeleteConfirmation(clientId) {
    const modal = new bootstrap.Modal(document.getElementById('removeConfirmationModal'));
    const deleteForm = document.getElementById('deleteForm'); // Asume que tu modal tiene un form con este id
    if (deleteForm) {
        deleteForm.action = `{{ url("/client") }}/${clientId}`;
    }
    modal.show();
}
