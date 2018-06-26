$('.selectlist-layout').on('click', function () {
    $('.select-container').toggle();
    $('.select-ul').slideToggle('fast');
});
$('.select-ul li').on('click', function () {
    var html = '' + ($(this).text()) + '<i class="icon iconfont icon-arrLeft-fill"></i>',
        typeId = $(this).attr('data-id');

    $('.select-container').toggle();
    $('.select-ul').slideToggle('fast');
    $('.select-ul li').removeClass('c-green');
    $(this).addClass('c-green');
    $('.select-box').html(html);
    message(typeId)
});
$('.teacher-list-box').on('click', function () {
    var id = $(this).attr('id');
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: 'mc/read/' + id,
        success: function () {
            window.location = 'mc/show/' + id;
        },
        error: function (e) { wap.errorHandler(e); }
    });
});
$('.weui-navbar__item').click(function(){
    $('.select-ul').hide();
    $('.select-container').hide();
});
$("#searchInput").on("input propertychange change", function() {
    var keyword = $(this).val(),
        type = $('.weui-bar__item--on').attr('data-type'),
        $messageList = $('.weui-popup__container .weui-tab__bd-item .list-layout');

    $messageList.html('');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: 'mc',
        data: {
            keyword: keyword,
            type: type,
            _token: wap.token()
        },
        success: function (result) {
            var str = '', i, message;
            if (result['type'] === 'sent'){
                for (i = 0 ; i < result['sent'].length; i++){
                    message = result['sent'][i];
                    str +=
                        '<div class="table-list ">'+
                            '<div class="line"></div>'+
                            '<div class="teacher-list-box glayline">'+
                                '<div class="teacher-work-box">'+
                                    '<a class="teacher-work-head" style="color:#000" href="show/' + message['id']+'">'+
                                        '<div class="titleinfo">'+
                                            '<div class="titleinfo-head">'+
                                                '<div class="titleinfo-head-left fl">'+
                                                    '<div class="title ml12">'+ message['title'] +'</div>'+
                                                    '<div class="title-info ml12">'+ message['user'] + '</div>' +
                                                '</div>'+
                                                '<span class="worktime">'+ message['created_at'].substr(0, 10) +
                                                    '<span class="info-status green">' + (message['sent'] ? '已发送' : '未发送') + '</span>' +
                                                '</span>' +
                                            '</div>' +
                                        '</div>' +
                                    '</a>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                }
            } else {
                for (i = 0 ; i < result['received'].length; i++) {
                    message = result['received'][i];
                    str +=
                        '<div class="table-list ">'+
                            '<div class="line"></div>'+
                            '<div class="teacher-list-box glayline">'+
                                '<div class="teacher-work-box">'+
                                    '<a class="teacher-work-head" style="color:#000" href="show/' + message.id + '">' +
                                        '<div class="titleinfo">'+
                                            '<div class="titleinfo-head">'+
                                                '<div class="titleinfo-head-left fl">'+
                                                    '<div class="title ml12">'+ message['title'] +'</div>'+
                                                    '<div class="title-info ml12">'+ message['user']+'</div>'+
                                                '</div>'+
                                                '<span class="worktime">' + message['created_at'].substr(0, 10) + '</span>' +
                                            '</div>' +
                                        '</div>' +
                                    '</a>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                }
            }
            $messageList.html(str);
        },
        error: function (e) { wap.errorHandler(e); }
    });
});

function message(typeId) {
    if (typeId === '0') {
        $('.table-list').show();
    } else {
        $('.table-list').hide();
        $('.list-' + typeId).show();
    }
}

