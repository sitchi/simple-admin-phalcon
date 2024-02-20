function deleteRole(id) {
    $.post('/roles/delete/' + id, function (data) {
        $('#modal-delete').html(data);
    })
}