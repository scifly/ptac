//# sourceURL=index.js
var $start = $('#start'),
    $end = $('#end');

$start.calendar();
$end.calendar();

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
                    document.title = '消息中心 - ' + this.text;
                }
            },
            {
                text: '发件箱',
                className: 'color-warning',
                onClick: function() {
                    document.title = '消息中心 - ' + this.text;
                }
            },
            {
                text: '草稿箱',
                className: 'color-danger',
                onClick: function() {
                    document.title = '消息中心 - ' + this.text;
                }
            },
            {
                text: '消息过滤',
                onClick: function() {
                    $('#filters').popup();
                }
            }
        ]
    });
});