var $mslId = $('#msl_id'),
    $id = $('#id'), 
    $edit = $('.icon-bianji'),
    $delete = $('.delete-message'),
    $showComment = $('.js-show-comment'),
    $comment = $('#mycomment'),
    $reply = $('.send-btn');

// 删除消息
$delete.on('click', function () {
    $.confirm({
        title: '确认删除这条信息？',
        text: '',
        onOK: function () {
            //点击确认
            $.ajax({
                type: 'DELETE',
                dataType: 'json',
                url: '../delete/' + $id.val(),
                data: { _token: wap.token() },
                success: function (result) {
                    $.alert(result['message'], function () {
                        window.location.href = '../';
                    });
                },
                error: function (e) { wap.errorHandler(e); }
            });
        }
    });
});
// 编辑消息
$edit.on('click', function () {
    window.location.href = '../edit/' + $id.val();
});
// 打开消息评论
$showComment.click(function () {
    $comment.popup();
});
// 评论字数限制
$('.weui_textarea').bind("input propertychange", function () {
    var now_length = $(this).val().length;
    $('.weui_textarea_counter span').text(now_length);
});
// 回复消息
$reply.off('click').on('click', function () {
    var content = $('.weui_textarea').val();
    if (!content){
        $.alert('回复内容不能为空！');
        return false;
    }
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../reply',
        data: {
            _token: token, content: content, msl_id: $mslId.val() },
        success: function () {
            $.alert("回复成功！", function () {
                $.closePopup();
                replies();
            });
        },
        error: function (e) { wap.errorHandler(e); }
    });
});
// 删除回复
$(document).on('click', '.delete-replay', function () {
    var id = $(this).attr('id');
    $.confirm({
        title: '确认删除这条回复？',
        text: '',
        onOK: function () {
            $.ajax({
                type: 'DELETE',
                dataType: 'json',
                url: '../remove/' + id,
                data: {_token: wap.token()},
                success: function () {
                    $.alert("删除成功！", function () {
                        replies();
                    });
                },
                error: function (e) { wap.errorHandler(e); }
            });
        }
    });
});
/** 异步获取回复列表 */
replies();

function replies() {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../replies',
        data: {
            _token: wap.token(),
            id: $id.val(),
            msl_id: $mslId.val()
        },
        success: function (result) {
            if (result['messages'].length > 0) {
                var html = '';
                for (var i = 0; i < result['messages'].length; i++) {
                    var message = result['messages'][i];
                    html += '<li class="discuss_item">' +
                        '<div>' +
                        '<div class="user_info">' +
                        '<strong class="nickname">' + message['name'] + '</strong>' +
                        '<img class="avatar" src="http://shp.qpic.cn/bizmp/UsXhSsnUkjiaibOb6bME9lIrxH2uClkDicVI1zsqpmBemDywTMo2VWibSA/64">' +
                        '<p class="discuss_extra_info">' + message['created_at'] + '</p>' +
                        '</div>' +
                        '<div class="discuss_message">' +
                        '<div class="discuss_message_content">' + message['content'] + '</div>' +
                        '<a class="del-icon-btn" href="javascript:">' +
                        '<span id=' + message['id'] + ' class="del-icon icon iconfont icon-lajixiang delete-replay"></span>' +
                        '</a>' +
                        '</div>' +
                        '</div>' +
                        '</li>';
                }
                $('.discuss_list').html(html);
            } else {
                $('.discuss_list').html('');
            }
        },
        error: function (e) { wap.errorHandler(e); }
    });
}
