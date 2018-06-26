page.create('formUser', 'users');

$(function () {
    $('#avatar_upload').change(function () {
        $.ajax({
            url: "../users/upload_ava/" + $('input[name=avatar_url]').attr("id"),
            data: {
                avatar: $('#avatar_upload')[0].files[0],
                _token: page.token()
            },
            type: 'POST',
            dataType: 'json',
            contentType: false,
            processData: false,
            cache: false,
            success: function (result) {
                $('#avatar_thumb_img').attr('src', '/ptac/storage/app/avauploads/' + data.fileName);
                $('input[name=avatar_url]').val(data.fileName);
                page.inform(result.title, result.message, page.success);
            },
            error: function (e) {
                page.errorHandler(e);
            }
        })
    })
});