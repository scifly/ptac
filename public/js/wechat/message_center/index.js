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
    $('.select-box ').html(html);
});

$('.teacher-list-box').click(function () {
    var id = $(this).attr('id');
    window.location.href = '../public/message_show/' + id;
});