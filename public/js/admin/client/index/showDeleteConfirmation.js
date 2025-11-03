function showDeleteConfirmation(clientId, baseUrl) {
    const modal = new bootstrap.Modal('#removeConfirmationModal');
    $('#delete').attr('action',baseUrl+'/'+clientId);
    $('#deleteElementBtn').attr('action',baseUrl+'/'+clientId);
    modal.show();
}
