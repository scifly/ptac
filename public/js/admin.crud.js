var crud = {
    index: function () {
        $('#data-table').dataTable({
            processing: true,
            serverSide: true,
            ajax: 'index',
            order: [[0, 'desc']],
            stateSave: true,
<<<<<<< HEAD
            language: {url: '../files/ch.json'}
=======
            language: {url: '../files.json'}
>>>>>>> refs/remotes/origin/master
        });

        var $dialog = $('#modal-dialog');
        var $del = $('#confirm-delete');
        var id, $row;

        $(document).on('click', '.fa-trash', function () {
            id = $(this).attr('id');

            $row = $(this).parents().eq(1);
            $dialog.modal({backdrop: true});
        });

        $del.on('click', function () {
            $.ajax({
                type: 'DELETE',
                dataType: 'json',
                url: 'delete/' + id,
                data: {_token: $('#csrf_token').attr('content')},
                success: function (result) {
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
    },
    create: function(formId) {
        var $cancel = $('#cancel');
        var $form = $('#' + formId);

        $('select').select2();
        $form.parsley().on("form:validated", function () {
            var ok = $('.parsley-error').length === 0;
            if (ok) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: 'store',
                    data: $form.serialize(),
                    success: function(result) {
                        alert(result.statusCode());
                        if (result.statusCode === 200) {
                            $form[0].reset();
                        } else {

                        }
                        $.gritter.add({
                            title: "新增结果",
                            text: result.message,
                            image: result.statusCode === 200 ? '/img/confirm.png' : '/img/failure.jpg'
                        });
                        return false;
                    },
                    error: function(e) {
                        var obj = JSON.parse(e.responseText);
                        for (var key in obj) {
                            if (obj.hasOwnProperty(key)) {
                                $.gritter.add({
                                    title: "新增结果",
                                    text: obj[key],
                                    image: '/img/failure.jpg'
                                });
                            }
                        }
                    }
                });
            }
        }).on('form:submit', function() {
            return false;
        });
        // Switchery
        // var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch'));
        // elems.forEach(function (html) {
        //    var switchery = new Switchery(html, {size: 'small'});
        // });

        $cancel.on('click', function() {
            window.location = 'index';
        });
    },
<<<<<<< HEAD

=======
>>>>>>> refs/remotes/origin/master
    edit: function (formId) {
        var $cancel = $('#cancel');
        var $form = $('#' + formId);

        $('select').select2();
        var path = window.location.pathname;
        var paths = path.split('/');
        var id = paths[paths.length - 1];

        $form.parsley().on("form:validated", function () {
            var ok = $('.parsley-error').length === 0;
            if (ok) {
                $.ajax({
                    type: 'PUT',
                    dataType: 'json',
                    url: '../update/' + id,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
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
                    },
                    error: function(e) {
                        var obj = JSON.parse(e.responseText);
                        for (var key in obj) {
                            if (obj.hasOwnProperty(key)) {
                                $.gritter.add({
                                    title: "编辑结果",
                                    text: obj[key],
                                    image: '/img/failure.jpg'
                                });
                            }
                        }
                    }
                });
            }
        }).on('form:submit', function() {
            return false;
        });

        $cancel.on('click', function () {
            window.location = '../index';
        });
    }
};

