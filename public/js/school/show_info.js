$('.edit_input').click(function () {
    var input_obj = $(this).prevAll('.edit-school');
    input_obj.removeAttr("readonly");
    input_obj.focus();
    input_obj.unbind();
    save_input(input_obj);
});
function save_input(input_obj) {
    var id = $('#id').val();
    var $form = $('#school-form');
    input_obj.blur(function () {
        $.ajax({
            type: 'PUT',
            dataType: 'json',
            url: page.siteRoot() + 'schools/update/' + id,
            data: $form.serialize(),
            success: function(result) {
                page.inform(
                    '操作结果', result.message,
                    result.statusCode === 200 ? page.success : page.failure
                );
            },
            error: function (e) { page.errorHandler(e); }
    });
        input_obj.attr("readonly","readonly");
    })
}

