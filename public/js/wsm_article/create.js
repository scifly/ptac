var token = $('#csrf_token').attr('content');

page.create('formWsmArticle', 'wsm_articles');
page.loadCss(plugins.fileinput.css);
$.getMultiScripts([plugins.fileinput.js]);
$.getMultiScripts([plugins.ueditor_config.js, plugins.ueditor_all.js]).done(
    function () {
        // var editor = UE.getEditor('container').render('container');//初始化富文本编辑器
        // $(function () {
        UE.delEditor('container');
        UE.getEditor('container', {
            initialFrameHeight: 300
        });
        var $preview = $('.preview'),
            $uploadFiles = $('#uploadFiles');
        // 初始化
        $uploadFiles.fileinput({
            language: 'zh',
            theme: 'explorer',
            uploadUrl: page.siteRoot() + "/wsm_articles/create",
            uploadAsync: false,
            maxFileCount: 5,
            minImageWidth: 50,      // 最小宽度
            minImageHeight: 50,     // 最小高度
            maxImageWidth: 1000,    // 最大宽度
            maxImageHeight: 1000,   // 最大高度
            allowedFileExtensions: ['jpg', 'gif', 'png'],//接收的文件后缀
            fileActionSettings: {
                showRemove: true,
                showUpload: false,
                showDrag: false
            },
            uploadExtraData: {
                '_token': token
            }
        });
        // 上传成功
        $uploadFiles.on("filebatchuploadsuccess", function (event, data/*, previewId, index*/) {
            // 填充数据
            var response = data.response.data;
            $.each(response, function (index, obj) {
                $preview.append(
                    '<div class="img-item">' +
                        '<img src="../../' + obj.path + '" id="' + obj.id + '">' +
                        '<div class="del-mask">' +
                            '<i class="delete glyphicon glyphicon-trash"></i>' +
                        '</div>' +
                    '</div>'
                );
                $preview.append('<input type="hidden" name="media_ids[]" value="' + obj.id + '">');
            });
            // 成功后关闭弹窗
            setTimeout(function () {
                $('#modalPic').modal('hide');
            }, 800)
        });

        // modal关闭，内容清空
        $('#modalPic').on('hide.bs.modal', function () {
            $uploadFiles.fileinput('clear');
        });
        // 点击删除按钮
        $('body').on('click', '.delete', function () {
            $(this).parent().parent().remove();
            $preview.append('<input type="hidden" name="del_ids[]" value="' + $(this).parent().siblings().attr('id') + '">');
        });
    // });
});
