/**
 * Created by Administrator on 2017-07-21 0021.
 */
$(crud.create('formWapSite', 'wapsites'));
$(function () {
    // 初始化
    $('#uploadFile').fileinput({
        "language": 'zh',
        'theme': 'explorer',
        'maxFileCount': 5,
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
            data.append('img[]', imgInputElement.files[i]);
        }
        data.append('_token', $('#csrf_token').attr('content'));
        // 图片预览
        var $pre = $('.preview');
        if (len !== 0) {
            $.ajax({
                type: 'post',
                processData: false,
                contentType: false,
                dataType: 'json',
                url: "../wapsites/uploadwapsite",
                data: data,
                success: function ($result) {
                    var imgArr = $result.data;
                    $.each(imgArr, function (index, obj) {
                        console.log(index + obj.id + obj.path);
                        $pre.append('<img src="../../' + obj.path + '" id="' + obj.id + '">');
                        $pre.append('<input type="hidden" name="media_ids[]" value="' + obj.id + '">');
                    });
                    // 清空，弹框关闭
                    $('#uploadFile').fileinput('clear');
                    $('#modalPic').modal('hide');
                }
            })
        }
    })
});
