$(crud.create('formCustodian','custodians'))

$(".expiry-date").datetimepicker({
    dateFormat: 'yy-mm-dd'
});

$(function () {
    $(document).on('click', '.btn-add', function (e) {
//            样式
        e.preventDefault();
        var controlForm = $('.addInput');
        var html = '<div class="entry input-group col-sm-6 col-sm-offset-3">' +
            '<input type="text" class="form-control">' +
            '<span class="input-group-btn">' +
            '<button class="btn btn-add btn-success" type="button">' +
            '<span class="glyphicon glyphicon-plus"></span>' +
            '</button>' +
            '</span>' +
            '</div>';
        controlForm.append(html);
        controlForm.find('.entry:not(:last) .btn-add')
            .removeClass('btn-add').addClass('btn-remove')
            .removeClass('btn-success').addClass('btn-danger')
            .html('<span class="glyphicon glyphicon-minus"></span>');
    }).on('click', '.btn-remove', function (e) {
        $(this).parents('.entry:first').remove();
        e.preventDefault();
        return false;
    });
});