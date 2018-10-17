//# sourceURL=upload.js
(function ($) {
    $.upload = function (options) {
        var upload = {
            options: $.extend({
                file: '#file-image',
                mediaId: '#media_id',
                preview: '.preview'
            }, options),
            upload: function (action, table, title) {
                $(upload.options.file).on('change', function () {
                    var file = $(this)[0].files[0],
                        id = action === 'edit' ? '/' + $('#id').val() : '',
                        data = new FormData();

                    data.append('file', file);
                    data.append('_token', page.token());
                    page.inform(title, '图片上传中...', page.info);
                    $('.overlay').show();
                    $.ajax({
                        type: 'POST',
                        url: page.siteRoot() + table + '/' + action + id,
                        data: data,
                        contentType: false,
                        processData: false,
                        success: function (result) {
                            var $preview = $(upload.options.preview),
                                imgAttrs = {
                                    'src': '../../' + result['path'],
                                    'title': '文件名：' + result['filename']
                                };

                            $(upload.options.mediaId).val(result['id']);
                            $preview.find('img').remove();
                            $preview.append($('<img' + ' />', imgAttrs).prop('outerHTML'));
                            $('.overlay').hide();
                            page.inform(title, '图片上传成功', page.success)
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            }
        };

        return { init: upload.upload }
    }
})(jQuery);