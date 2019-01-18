//# sourceURL=index.js
var $app = $('#app'),
    $start = $('#start'),
    $end = $('#end'),
    $title = $('.weui-panel__hd'),
    $loadmore = $('.weui-loadmore'),
    $page = $('#page'),
    $msgList = $('#msg_list');

$start.calendar();
$end.calendar();
$.getScript(
    '/js/wechat/message_center/mc.js',
    function () { $.mc().index(); }
);

$app.scroll(function() {
    if ($app.scrollTop() + $(window).height() >= $app.prop('scrollHeight')) {
        $loadmore.show();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: 'message_centers',
            data: {
                _token: wap.token(),
                page: $page.val()
            },
            success: function (result) {
                var st = $app.scrollTop();

                if (result !== '') {
                    $msgList.append(result);
                    $page.val(parseInt($page.val()) + 1);
                }
                $loadmore.hide();
                $app.scrollTop(st - 1);
            },
            error: function (e) {
                wap.errorHandler(e);
            }
        });
    }
});

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
                    switchTitle(this);
                }
            },
            {
                text: '发件箱',
                className: 'color-warning',
                onClick: function() {
                    switchTitle(this);
                }
            },
            {
                text: '草稿箱',
                className: 'color-danger',
                onClick: function() {
                    switchTitle(this);
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

function switchTitle(o) {
    $title.html(o.text)
        .removeClass($title.attr('class').split(' ')[1])
        .addClass(o.className);
}