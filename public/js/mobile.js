$(function () {
    if ($('.swiper-container').length > 0) {
        var mySwiper = new Swiper('.swiper-container', {
            autoplay: 5000,//可选选项，自动滑动
            pagination: '.swiper-pagination'
        })
    }
    // 判断footer是否需要固定定位
    var windowHeight = $(window).height();
    var wrapperHeight = $('.wrapper').height();
    if (wrapperHeight < windowHeight) {
        $('.weui-footer').addClass('weui-footer_fixed-bottom');
    }
});