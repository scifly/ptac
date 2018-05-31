//# sourceURL=wsm.js
(function ($) {
    $.wsm = function (options) {
        var wsm = {
            options: $.extend({
                file: '#file-image',
                mediaId: '#media_id',
                preview: '.preview'
            }, options),
            token: function () { return $('#csrf_token').attr('content'); },
            upload: function (action) {
                $(wsm.options.file).on('change', function () {
                    var file = $(this)[0].files[0],
                        id = action === 'edit' ? '/' + $('#id').val() : '',
                        data = new FormData();

                    data.append('file', file);
                    data.append('_token', wsm.token());
                    page.inform('微网站栏目', '文件上传中...', page.info);
                    $('.overlay').show();
                    $.ajax({
                        type: 'POST',
                        url: page.siteRoot() + 'wap_site_modules/' + action + id,
                        data: data,
                        contentType: false,
                        processData: false,
                        success: function (result) {
                            var $preview = $(wsm.options.preview),
                                imgAttrs = {
                                    'src': '../../' + result['path'],
                                    'style': 'height: 200px;',
                                    'title': '文件名：' + result['filename']
                                };

                            $(wsm.options.mediaId).val(result['id']);
                            $preview.find('img').remove();
                            $preview.append($('<img' + ' />', imgAttrs).prop('outerHTML'));
                            $('.overlay').hide();
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            }
        };

        return { init: wsm.upload }
    }
})(jQuery);