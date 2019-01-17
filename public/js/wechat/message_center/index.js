//# sourceURL=index.js
var $start = $('#start'),
    $end = $('#end'),
    $title = $('.weui-panel__hd');

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
                    $title.html(this.text);
                }
            },
            {
                text: '发件箱',
                className: 'color-warning',
                onClick: function() {
                    $title.html(this.text);
                }
            },
            {
                text: '草稿箱',
                className: 'color-danger',
                onClick: function() {
                    $title.html(this.text);
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