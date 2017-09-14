$(crud.create('formStudent','students'));

var isDefault =
    '<label for="mobile[isdefault][]">' +
    '<input name="mobile[isdefault][]" type="radio" id="mobile[isdefault][]" class="minimal">' +
    '</label>';
var enabled =
    '<label for="mobile[enabled][]">' +
    '   <input name="mobile[enabled][]" type="checkbox" id="mobile[enabled][]" class="minimal">' +
    '</label>';
$(document).on('click', '.btn-add', function(e) {
    e.preventDefault();

    var $tbody = $('tbody');
    var $row = $(this).parents('tr:first');
    var $clone = $($row.clone()).appendTo($tbody);
    $tbody.find('tr:last td:nth-child(2)').html(isDefault)
        .find('input[type="radio"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });
    $tbody.find('tr:last td:nth-child(3)').html(enabled)
        .find('input[type="checkbox"]').iCheck({
        checkboxClass: 'icheckbox_minimal-blue',
        radioClass: 'iradio_minimal-blue'
    });
    $clone.find('input[type="text"]').val('');
    $tbody.find('tr:not(:last) .btn-add')
        .removeClass('btn-add').addClass('btn-remove')
        .html('<i class="fa fa-minus text-blue"></i>');
}).on('click', '.btn-remove', function(e) {
    $(this).parents('tr:first').remove();
    e.preventDefault();
    return false;
});


$(function () {
    $(document).on('click', '.btn-add2', function (e) {
//            样式
        e.preventDefault();
        var controlForm = $('.addInput');
        var html = '<div class="entry input-group col-sm-6 col-sm-offset-3">' +
            '<input type="text" class="form-control" name="relationship[]">' +
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