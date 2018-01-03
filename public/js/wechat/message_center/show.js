$('.delete-message').on('click', function () {
    var id = $('#id').val();
    $.confirm({
        title: '确认删除这条信息？',
        text: '',
        onOK: function () {
            //点击确认
            $.ajax({
                type: 'DELETE',
                dataType: 'json',
                url: '../message_delete/' + id,
                data: {_token: $('#csrf_token').attr('content')},
                success: function (reseult) {
                    if (reseult.statusCode === 200) {
                        $.alert("删除成功！", function () {
                            window.location.href = '../message_center';
                        });
                    } else {
                        $.alert('删除失败，稍后请重新尝试！')
                    }
                }
            });
        },
        onCancel: function () {
        }
    });
});
$('.icon-bianji').on('click', function () {
    var id = $('#id').val();
    window.location.href = '../message_edit/' + id;
});

$('.js-show-comment').click(function () {
    $("#mycomment").popup();
});
$('.weui_textarea').bind("input propertychange", function () {
    var now_length = $(this).val().length;
    $('.weui_textarea_counter span').text(now_length);
});

$('.send-btn').click(function () {
    var content = $('.weui_textarea').val();
    if(!content){
        $.alert('回复内容不能为空！');
        return false;
    }
    var msl_id = $('#msl_id').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../message_replay',
        data: {_token: $('#csrf_token').attr('content'), content: content, msl_id: msl_id},
        success: function (result) {
            if (result.statusCode === 200) {
                $.alert("回复成功！", function () {
                    $.closePopup();
                    get_replies();
                });
            } else {
                $.alert('删除失败，稍后请重新尝试！')
            }
        }
    });
});

get_replies();

/**
 * 异步获取回复列表
 */
function get_replies() {
    var msl_id = $('#msl_id').val();
    var id = $('#id').val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../message_replaylist',
        data: {_token: $('#csrf_token').attr('content'), id: id, msl_id: msl_id},
        success: function (result) {
            if (result.statusCode === 200) {
                if (result.message.length > 0) {
                    var html = '';
                    for (var i = 0; i < result.message.length; i++) {
                        var data = result.message[i];
                        html += ' <li class="discuss_item">' +
                            ' <div>' +
                            ' <div class="user_info">' +
                            ' <strong class="nickname">' + data.name + '</strong>' +
                            ' <img class="avatar" src="http://shp.qpic.cn/bizmp/UsXhSsnUkjiaibOb6bME9lIrxH2uClkDicVI1zsqpmBemDywTMo2VWibSA/64">' +
                            ' <p class="discuss_extra_info">' + data.created_at + '</p>' +
                            ' </div>' +
                            ' <div class="discuss_message">' +
                            ' <div class="discuss_message_content">' + data.content + '</div>' +
                            ' <a class="del-icon-btn" href="javascript:">' +
                            ' <span id=' + data.id + ' class="del-icon icon iconfont icon-lajixiang delete-replay"></span>' +
                            ' </a>' +
                            ' </div>' +
                            ' </div>' +
                            ' </li>'
                    }
                    $('.discuss_list').html(html);
                    delete_replay();
                } else {
                    $('.discuss_list').html();
                }
            } else {
                $.alert('获取回复失败，稍后请重新尝试！')
            }
        }
    });
}

/**
 * 删除回复
 */
function delete_replay() {
    $('.delete-replay').on('click', function () {
        var id = $(this).attr('id');
        $.confirm({
            title: '确认删除这条回复？',
            text: '',
            onOK: function () {
                $.ajax({
                    type: 'DELETE',
                    dataType: 'json',
                    url: '../message_replaydel/' + id,
                    data: {_token: $('#csrf_token').attr('content')},
                    success: function (result) {
                        if (result.statusCode === 200) {
                            $.alert("删除成功！", function () {
                                get_replies();
                            });
                        } else {
                            $.alert('删除失败，请稍后重试！')
                        }
                    }
                });
            },
            onCancel: function () {}
        });
    });
}
