$(crud.create('formCustodian','custodians'))

$(".expiry-date").datetimepicker({
    dateFormat: 'yy-mm-dd'
});

var n = 0;
$(document).off('click','.btn-add');
$(document).on('click', '.btn-add', function (e) {
    e.preventDefault();
    var $tbody = $('tbody');
    n++;
    // add html
    $tbody.append(
        '<tr><td><input type="text" class="form-control" placeholder="（请输入手机号码）" name="mobile[mobile][k' + n + ']" value=""></td>' +
        '<td style="text-align: center"><input type="radio" class="minimal" name="mobile[isdefault]" value="k' + n + '"></td>' +
        '<td style="text-align: center"><input type="checkbox" class="minimal" name="mobile[enabled][k' + n + ']"></td>' +
        '<td style="text-align: center"><button class="btn btn-box-tool btn-add" type="button"><i class="fa fa-plus text-blue"></i></button></td></tr>'
    );
    // icheck init
    $tbody.find('input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });
    $tbody.find('input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });
    $tbody.find('tr:not(:last) .btn-add')
        .removeClass('btn-add').addClass('btn-remove')
        .html('<i class="fa fa-minus text-blue"></i>');
}).on('click', '.btn-remove', function (e) {
    $(this).parents('tr:first').remove();
    e.preventDefault();
    return false;
});


$(function () {
    $(document).off('click','.btn-add2');
    $(document).on('click', '.btn-add2', function (e) {
//            样式
        e.preventDefault();
        var controlForm = $('.addInput');
        var html = '<div class="entry input-group col-sm-6 col-sm-offset-3">' +
            '<input type="text" class="form-control" name="relationship[]">' +
            '<span class="input-group-btn">' +
            '<button class="btn btn-add2 btn-success" type="button">' +
            '<span class="glyphicon glyphicon-plus"></span>' +
            '</button>' +
            '</span>' +
            '</div>';
        controlForm.append(html);
        controlForm.find('.entry:not(:last) .btn-add2')
            .removeClass('btn-add2').addClass('btn-remove')
            .removeClass('btn-success').addClass('btn-danger')
            .html('<span class="glyphicon glyphicon-minus"></span>');
    }).on('click', '.btn-remove', function (e) {
        $(this).parents('.entry:first').remove();
        e.preventDefault();
        return false;
    });
});