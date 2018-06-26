page.edit('formPersonalInfo', 'personal_infos');

$(function () {
    $('#avatar_upload').change(function () {
        var formData = new FormData();
        formData.append('avatar', $('#avatar_upload')[0].files[0]);
        formData.append('_token', page.token());
        var id = $('input[name=avatar_url]').attr("id");
        $.ajax({
            url: "../personal_infos/upload_ava/" + id,
            data: formData,
            type: 'POST',
            dataType: 'json',
            contentType: false,
            processData: false,
            cache: false,
            success: function (data) {
                if (data.statusCode === 200) {
                    $('#avatar_thumb_img').attr('src', '/ptac/storage/app/avauploads/' + data.message);
                    $('input[name=avatar_url]').val(data.message);
                }
                page.inform(
                    '操作结果',  data.statusCode === 200 ? '更新头像成功' : '更新头像失败',
                    data.statusCode === 200 ? page.success : page.failure
                );
            },
            error: function (XMLHttpRequest, textStatus, errorThrown) {
                var number = XMLHttpRequest.status;
                var info = "错误号" + number + "文件上传失败!";
                page.inform('出现异常', info, page.failure);
            }
        })
    })
});

