//# sourceURL=index.js
$.getScript(
    '/js/wechat/message_center/mc.js',
    function () { $.mc().index(); }
);

$(document).on('click', '#show-actions', function() {
    $.actions({
        // title: '请选择',
        onClose: function() {
            console.log('close');
        },
        actions: [
            {
                text: '收件箱',
                className: 'color-primary',
                onClick: function() {
                    $.alert('发布成功');
                }
            },
            {
                text: '发件箱',
                className: 'color-warning',
                onClick: function() {
                    $.alert('你选择了“编辑”');
                }
            },
            {
                text: '草稿箱',
                className: 'color-danger',
                onClick: function() {
                    $.alert('你选择了“编辑”');
                }
            },
            {
                text: '按消息类型过滤',
                onClick: function() {
                    $.alert('你选择了“编辑”');
                }
            },
            {
                text: '按消息格式过滤',
                onClick: function() {
                    $.alert('你选择了“删除”');
                }
            }
        ]
    });
});