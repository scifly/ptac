//# sourceURL=wap.js
(function ($) {
    $.wap = function (options) {
        var wap = {
            options: $.extend({
                preview: '.preview',
                uploadFiles: '#uploadFiles',
            }, options),
            initEditor: function () {
                UE.delEditor('container');
                UE.getEditor('container', { initialFrameHeight: 300 });
            },
            initUploader: function (table, action) {
                var $preview = $(wap.options.preview),
                    $uploadFiles = $(wap.options.uploadFiles),
                    id = (action === 'edit' ? '/' + $('#id').val() : '');
                $uploadFiles.fileinput({
                    language: 'zh',
                    theme: 'explorer',
                    uploadUrl: page.siteRoot() + table + '/' + action + id,
                    uploadAsync: false,
                    maxFileCount: 5,
                    minImageWidth: 50,      // 最小宽度
                    minImageHeight: 50,     // 最小高度
                    maxImageWidth: 1000,    // 最大宽度
                    maxImageHeight: 1000,   // 最大高度
                    allowedFileExtensions: ['jpg', 'gif', 'png'],// 允许的文件类型
                    fileActionSettings: {
                        showRemove: true,
                        showUpload: false,
                        showDrag: false
                    },
                    uploadExtraData: {
                        _token: page.token()
                    }
                }).on("filebatchuploadsuccess", function (event, data/*, previewId, index*/) {
                    // 填充数据
                    var files = data.response;
                    $.each(files, function (index, file) {
                        $preview.append(
                            '<div class="img-item">' +
                            '<img src=' + file.path + '"../../.." id="' + file.id + '" alt="">' +
                            '<div class="del-mask">' +
                            '<i class="delete glyphicon glyphicon-trash text-red"></i>' +
                            '</div>' +
                            '</div>'
                        );
                        $preview.append('<input type="hidden" name="media_ids[]" value="' + file.id + '">');
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
                    $preview.append(
                        '<input type="hidden" name="del_ids[]" value="' +
                        $(this).parent().siblings().attr('id') + '">'
                    );
                });
            },
            init: function (action, table) {
                var scripts = [
                    plugins.fileinput.js,
                    plugins.ueditor_config.js,
                    plugins.ueditor_all.js
                ];

                page.loadCss('css/wap.css');
                if (action === 'create') {
                    page.create('formArticle', table);
                } else if (action === 'edit') {
                    page.edit(table === 'articles' ? 'formArticle' : 'formWap', table);
                }
                page.loadCss(plugins.fileinput.css);
                $.getMultiScripts(scripts).done(
                    function () {
                        $.getMultiScripts([plugins.fileinput.language]).done(
                            function () {
                                if (table !== 'waps') {
                                    wap.initEditor();
                                }
                                wap.initUploader(table, action);
                            }
                        )
                    }
                );
            }
        };

        return { init: wap.init }
    }
})(jQuery);