$('#data-table').dataTable({
    processing: true,
    serverSide: true,
    ajax: '/school/index',
    order: [[0, 'desc']],
    stateSave: true,
    language: { url: '/files/ch.json' }
});

$(document).on('click', '.fa-trash', function() {
    var $dialog = $('#modal-dialog');
    var id = $(this).attr('id');
    var $row = $(this).parents().eq(1);

    $dialog.modal({ backdrop: true });
    $dialog.find('#confirm-delete').on('click', function() {
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: '/' + controller + '/delete/' + id,
            success: function(result) {
                if (result.statusCode === 200) {
                    $row.remove();
                }
                $.gritter.add({
                    title: "删除结果",
                    text: result.message,
                    image: result.statusCode === 200 ? '/img/confirm.png' : '/img/failure.jpg'
                });
                return false;
            }
        });
    });
});