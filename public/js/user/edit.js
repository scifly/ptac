page.edit('formUser', 'users');

$('#avatar_upload').change(function () {
    $.ajax({
        url: "../upload_ava/" + id,
        data: {
            id: $('input[name=avatar_url]').attr("id"),
            avatar: $('#avatar_upload')[0].files[0],
            _token: page.token()
        },
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function (data) {
            $('#avatar_thumb_img').attr('src', '/ptac/storage/app/avauploads/' + data.fileName);
            $('input[name=avatar_url]').val(data.fileName);
            page.inform(result.title, data.message, page.success);
        },
        error: function (e) {
            page.errorHandler(e)
        }
    })
});