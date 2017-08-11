/**
 * Created by Administrator on 2017-07-21 0021.
 */
$(crud.create('formWapSite', 'wapsites'));
$(function () {
    var $pre = $('.preview');
    var $uploadFile = $('#uploadFile');
    // 初始化
    $uploadFile.fileinput({
        language: 'zh',
        theme: 'explorer',
        uploadUrl: "../wapsites/uploadImages",
        uploadAsync: false,
        maxFileCount:5,
        minImageWidth: 50, //图片的最小宽度
        minImageHeight: 50,//图片的最小高度
        maxImageWidth: 1000,//图片的最大宽度
        maxImageHeight: 1000,//图片的最大高度
        allowedFileExtensions: ['jpg', 'gif', 'png'],//接收的文件后缀
        fileActionSettings: {
            showRemove: true,
            showUpload: false,
            showDrag: false
        },
        uploadExtraData: {
            '_token': $('#csrf_token').attr('content')
        }
    });
    // 上传成功
    $uploadFile.on("filebatchuploadsuccess", function (event, data, previewId, index) {
        // 填充数据
        var response = data.response.data;
        $.each(response, function (index, obj) {
            $pre.append('<img src="../../' + obj.path + '" id="' + obj.id + '">');
            $pre.append('<input type="hidden" name="media_ids[]" value="' + obj.id + '">');
        });
    });

    // modal关闭，内容清空
    $('#modalPic').on('hide.bs.modal', function () {
        $uploadFile.fileinput('clear');
    })
});

