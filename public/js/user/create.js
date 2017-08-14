$(crud.create('formUser'));

$(function () {
    // 初始化
    $('#uploadFile').fileinput({
        "language": 'zh',
        'theme': 'explorer',
        'maxFileCount': 1,
        'uploadUrl': '#',
        'showUpload': false,
        'allowedFileExtensions': ['jpg', 'gif', 'png'],//接收的文件后缀
        'fileActionSettings': {
            showRemove: true,
            showUpload: false,
            showDrag: false
        }
    });
    $('#upload').click(function () {
        var data = new FormData($(".form-horizontal")[0]);
        var imgInputElement = document.getElementById('uploadFile');
        var len = imgInputElement.files.length;

        for (var i = 0; i < len; i++) {
            data.append('img[]', imgInputElement.files[i]);//获取到选中的图片
        }
        data.append('_token', $('#csrf_token').attr('content'));//添加token
        // 图片预览
        var $pre = $('.preview');
        if (len !== 0) {
            $.ajax({
                type: 'post',
                processData: false,
                contentType: false,
                dataType: 'json',
                url: "../users/uploadavatar",
                data: data,
                success: function ($result) {
                    var imgArr = $result.data;
                    $.each(imgArr, function (index, obj) {//渲染选中的图片到表单
                        console.log(index + obj.path);
                        $pre.append('<img src="../../' + obj.path + '">');
                        $pre.append('<input type="hidden" name="avatar_url" value="../../' + obj.path + '">');
                    });
                    // 清空,关闭弹框
                    $('#uploadFile').fileinput('clear');
                    $('#modalPic').modal('hide');
                }
            })
        }
    })
});