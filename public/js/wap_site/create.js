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
        'minImageWidth': 50, //图片的最小宽度
        'minImageHeight': 50,//图片的最小高度
        'maxImageWidth': 1000,//图片的最大宽度
        'maxImageHeight': 1000,//图片的最大高度
        'allowedFileExtensions': ['jpg', 'gif', 'png'],//接收的文件后缀
        'fileActionSettings': {
            showRemove: true,
            showUpload: false,
            showDrag: false
        }
    });
    $('#uploadFile').on('filepreremove', function(event, id, index) {
        console.log('id = ' + id + ', index = ' + index);
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
                url: "../wapsites/uploadwapsite",

                data: data,
                success: function ($result) {
                    var imgArr = $result.data;
                    $.each(imgArr, function (index, obj) {//渲染选中的图片到表单
                        console.log(index + obj.id + obj.path);
                        $pre.append('<img src="../../' + obj.path + '" id="' + obj.id + '">');
                        $pre.append('<input type="hidden" name="media_ids[]" value="' + obj.id + '">');
                    });
                    // 清空,关闭弹框
                    $('#uploadFile').fileinput('clear');
                    $('#modalPic').modal('hide');
                }
            })
        }
    })
});
