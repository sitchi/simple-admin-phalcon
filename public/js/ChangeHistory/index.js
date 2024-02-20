function detail(id) {
    $.post('/changeHistory', {id: id}, function (data) {
        $('#modal-detail').html(data);
    })
}