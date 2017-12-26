$('.selectlist-layout').click(function () {
    $('.select-container').toggle();
    $('.select-ul').slideToggle('fast');
});

$('.select-ul li').click(function () {
    $('.select-container').toggle();
    $('.select-ul').slideToggle('fast');
    $('.select-ul li').removeClass('c-green');
    $(this).addClass('c-green');
    var html = '' + ($(this).text()) + '<i class="icon iconfont icon-arrLeft-fill"></i>';
    $('.select-box').html(html);
    var type_id = $(this).attr('data-id');
    message(type_id)
});

function message(type_id) {
    if (type_id === '0') {
        $('.table-list').show();
    } else {
        $('.table-list').hide();
        $('.list-' + type_id).show();
    }
}

$('.teacher-list-box').click(function () {
    var id = $(this).attr('id');
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: '../public/message_update/' + id,
        success: function (result) {
            if (result.statusCode === 200) {
                window.location.href = '../public/message_show/' + id;
            }
        }
    });
});

$('.weui-navbar__item').click(function(){
    $('.select-ul').hide();
    $('.select-container').hide();
});
