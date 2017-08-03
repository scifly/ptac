var crud = {
    success: 'img/confirm.png',
    failure: 'img/failure.jpg',
    siteroot: function() {
        var path = window.location.pathname;
        var paths = path.split('/');
        return '/' + paths[1] + '/' + paths[2] + '/';
    },
    inform: function(title, text, image) {
        $.gritter.add({title: title, text: text, image: crud.siteroot() + image});
    },
    ajaxRequest: function(requestType, ajaxUrl, data, obj) {
        $.ajax({
            type: requestType,
            dataType: 'json',
            url: ajaxUrl,
            data: data,
            success: function(result) {
                if (result.statusCode === 200) {
                    switch(requestType) {
                        case 'POST': obj.reset(); break;
                        case 'DELETE': obj.remove(); break;
                        default: break;
                    }
                }
                crud.inform(
                    '操作结果', result.message,
                    result.statusCode === 200 ? crud.success : crud.failure
                );
                return false;
            },
            error: function(e) {
                var obj = JSON.parse(e.responseText);
                crud.inform('出现异常', obj['message'], crud.failure);
            }
        });
    },
    init: function(homeUrl, formId, ajaxUrl, requestType) {
        // Select2
        if($('select').select2()){
            $('select').select2();

        }

        // Switchery
        var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        elems.forEach(function (html) {
            // noinspection JSUnusedLocalSymbols
            var switchery = new Switchery(html, {size: 'small'});
        });

        // Cancel button
        $('#cancel').on('click', function() { window.location = homeUrl; });

        var $form = $('#' + formId);
        // Parsley
        $form.parsley().on("form:validated", function () {
            if ($('.parsley-error').length === 0) {
                crud.ajaxRequest(requestType, ajaxUrl, $form.serialize(), $form[0]);
            }
        }).on('form:submit', function() {
            return false;
        });
    },
    index: function () {
        $('#data-table').dataTable({
            processing: true,
            serverSide: true,
            ajax: 'index',
            order: [[0, 'desc']],
            stateSave: true,
            language: {url: '../files/ch.json'}
        });

        var id, $row;

        $(document).on('click', '.fa-trash', function () {
            id = $(this).attr('id');
            $row = $(this).parents().eq(1);
            $('#modal-dialog').modal({backdrop: true});
        });
        // $(document).on('click', '.fa-show', function () {
        //     id = $(this).attr('id');
        //     $row = $(this).parents().eq(1);
        //
        //     $('#modal-show-company').modal({backdrop: true});
        // });


        $('#confirm-delete').on('click', function () {
            crud.ajaxRequest(
                'DELETE', 'delete/' + id,
                { _token: $('#csrf_token').attr('content') }, $row
            );
        });
    },
    create: function(formId) { this.init('index', formId, 'store', 'POST'); },
    edit: function (formId) {
        var path = window.location.pathname;
        var paths = path.split('/');
        var id = paths[paths.length - 1];
        this.init('../index', formId, '../update/' + id, 'PUT');
    }
};
