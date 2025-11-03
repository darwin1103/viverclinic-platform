function showDeleteConfirmation(staffId, baseUrl) {
    const modal = new bootstrap.Modal('#removeConfirmationModal');
    $('#delete').attr('action',baseUrl+'/'+staffId);
    $('#deleteElementBtn').attr('action',baseUrl+'/'+staffId);
    modal.show();
}
