var crud = {
    index: function(controller) {
        $('#data-table').dataTable({
            processing: true,
            serverSide: true,
            ajax: 'index',
            order: [[0, 'desc']],
            stateSave: true,
            language: { url: '../files/ch.json' }
        });

        $(document).on('click', '.fa-trash', function() {
            var $dialog = $('#modal-dialog');
            var id = $(this).attr('id');
            var $row = $(this).parents().eq(1);

            $dialog.modal({ backdrop: true });
            $dialog.find('#confirm-delete').on('click', function() {
                $.ajax({
                    type: 'DELETE',
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
    },
    create: function(formId, controller) {
        var $save = $('#save'),
            $cancel = $('#cancel');
        var $form = $('#' + formId);

        $form.parsley();
        $('select').select2();

        $save.on('click', function() {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: '/' + controller + '/store',
                data: $form.serialize(),
                success: function(result) {
                    if (result.statusCode === 200) {
                        $form[0].reset();
                    }
                    $.gritter.add({
                        title: "新增结果",
                        text: result.message,
                        image: result.statusCode === 200 ? '/img/confirm.png' : '/img/failure.jpg'
                    });
                    return false;
                }
            });
        });

        $cancel.on('click', function() {
            window.location = '/' + controller + '/index';
        });
    },
    edit: function(formId, controller) {
        var $save = $('#save'),
            $cancel = $('#cancel');
        var $form = $('#' + formId);

        $form.parsley();
        $('select').select2();

        var path = window.location.pathname;
        var paths = path.split('/');
        var id = paths[paths.length - 1];
        $save.on('click', function() {
            $.ajax({
                type: 'PUT',
                dataType: 'json',
                url: '/' + controller + '/edit/' + id,
                data: $form.serialize(),
                success: function(result) {
                    if (result.statusCode === 200) {
                        $form[0].reset();
                    }
                    $.gritter.add({
                        title: "编辑结果",
                        text: result.message,
                        image: result.statusCode === 200 ? '/img/confirm.png' : '/img/failure.jpg'
                    });
                    return false;
                }
            });
        });

        $cancel.on('click', function() {
            window.location = '/' + controller + '/index';
        });
    }
};