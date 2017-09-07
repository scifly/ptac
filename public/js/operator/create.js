$(crud.create('formOperator', 'operators'));
var isDefault =
'<label for="Mobile[isdefault][]">' +
    '<input name="Mobile[isdefault][]" type="radio" id="Mobile[isdefault][]" class="minimal">' +
'</label>';
var enabled =
'<label for="Mobile[enabled][]">' +
'   <input name="Mobile[enabled][]" type="checkbox" id="Mobile[enabled][]" class="minimal">' +
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
