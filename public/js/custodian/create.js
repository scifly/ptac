$(crud.create('formCustodian','custodians'))

$(".expiry-date").datetimepicker({
    dateFormat: 'yy-mm-dd'
});
/*
$(function()
{
    $(document).on('click', '.btn-add', function(e)
    {
        e.preventDefault();

        var controlForm = $('.form-horizontal'),
            currentEntry = $(this).parents('.form-group:first'),
            newEntry = $(currentEntry.clone()).appendTo(controlForm);

        newEntry.find('input').val('');
        controlForm.find('.form-group:not(:last) .btn-add')
            .removeClass('btn-add').addClass('btn-remove')
            .removeClass('btn-success').addClass('btn-danger')
            .html('<span class="glyphicon glyphicon-minus"></span>');
    }).on('click', '.btn-remove', function(e)
    {
        $(this).parents('.entry:first').remove();

        e.preventDefault();
        return false;
    });
});
*/

$(document).on("click", ".btn-add", function (e) {
    e.preventDefault();
    var controlInput = $(".addInput"),
        currentEntry = $(this).parents('.addInput:first'),
        newEntry = $(currentEntry.clone()).append(controlInput);

    newEntry.find('input').val('');
    controlInput.find('.form-group:not(:last) .btn-add')
        .removeClass('btn-add').addClass('btn-remove')
        .removeClass('btn-success').addClass('btn-danger')
        .html('<span class="glyphicon glyphicon-minus"></span>');
});
