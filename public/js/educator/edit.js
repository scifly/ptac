$(crud.edit('formEducator', 'educators'));
var n = 0;
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