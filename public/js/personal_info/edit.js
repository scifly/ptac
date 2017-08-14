$(crud.edit('formPersonalInfo'));

$(function () {
    $('#avatar_upload').change(function () {
        var formData = new FormData();
        formData.append('avatar', $('#avatar_upload')[0].files[0]);
        formData.append('_token', $('#csrf_token').attr('content'));
        var id = $('input[name=avatar_url]').attr("id");
        $.ajax({
            url: "../upload_ava/" + id,
            data: formData,
            type: 'POST',
            dataType: 'json',
            contentType: false,
            processData: false,
            cache: false,
            success: function (data) {
                if (data.statusCode === 200) {
                    $('#avatar_thumb_img').attr('src', '/ptac/storage/app/avauploads/' + data.fileName);
                    $('input[name=avatar_url]').val(data.fileName);
                    crud.inform('更新头像', data.message, crud.success);
                }else{
                    crud.inform('出现异常', data.message, crud.failure);
                }
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                var number = XMLHttpRequest.status;
                var info = "错误号" + number + "文件上传失败!";
                crud.inform('出现异常', info, crud.failure);
            }
        })
    })
});

