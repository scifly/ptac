page.create('formFlow', 'flows');
$(function () {
    var $pre = $('.preview');
    var $uploadFile = $('#uploadFile');
    // 初始化
    $uploadFile.fileinput({
        language: 'zh',
        theme: 'explorer',
        uploadUrl: "../procedure_logs/upload_medias",
        uploadAsync: false,
        maxFileCount: 5,
        minImageWidth: 50, //图片的最小宽度
        minImageHeight: 50,//图片的最小高度
        maxImageWidth: 1000,//图片的最大宽度
        maxImageHeight: 1000,//图片的最大高度
        allowedFileExtensions: ['jpg', 'gif', 'png', 'pdf', 'txt', 'xlsx', 'docx', 'zip'],//接收的文件后缀
        fileActionSettings: {
            showRemove: true,
            showUpload: false,
            showDrag: false
        },
        uploadExtraData: {
            _token: page.token()
        }
    });
    // 上传成功
    $uploadFile.on("filebatchuploadsuccess", function (event, data) {
        // 填充数据1
        var response = data.response.data;
        $.each(response, function (index, obj) {
            if ($.inArray(obj.type, ['jpg', 'gif', 'png']) === -1) {
                // 非img
                $pre.append('<div class="img-item"><img src="../img/'+obj.type+'_128px.png" id="' + obj.id + '" alt="'+obj.type+'文件">' +
                    '<div class="del-mask"><span class="file-name">' + obj.filename + '</span><i class="delete glyphicon glyphicon-trash"></i></div></div>');
            } else {
                //img
                $pre.append('<div class="img-item"><img src="../../' + obj.path + '" id="' + obj.id + '"><div class="del-mask"><i class="delete glyphicon glyphicon-trash"></i></div></div>');
            }
            // $pre.append('<div class="img-item"><img src="../../' + obj.path + '" id="' + obj.id + '"><div class="del-mask"><i class="delete glyphicon glyphicon-trash"></i></div></div>');
            $pre.append('<input type="hidden" name="media_ids[]" value="' + obj.id + '">');
        });
        // 成功后关闭弹窗
        setTimeout(function () {
            $('#modalPic').modal('hide');
        }, 800)
    });

    // modal关闭，内容清空
    $('#modalPic').on('hide.bs.modal', function () {
        $uploadFile.fileinput('clear');
    });
    // 点击删除按钮
    $('body').on('click', '.delete', function () {
        obj = $(this).parent();
        $.ajax({
            type: 'GET',
            url: '../procedure_logs/delete_medias/'+ obj.siblings().attr('id'),
            success: function (result) {
                if (result.statusCode === 200) {
                    obj.parent().remove();
                }
                page.inform(
                    '操作结果', result.message,
                    result.statusCode === 200 ? crud.success : crud.failure
                );
                return false;
            },
            error: function (e) {
                var obj = JSON.parse(e.responseText);
                crud.inform('出现异常', obj['message'], crud.failure);
            }
        });
    })
});