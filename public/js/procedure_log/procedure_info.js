$(function () {
    // 设置stepflex的宽度
    var $stepFlex = $('.stepFlex');
    var len = $stepFlex.find('dl').length;
    $stepFlex.css({
        'width':160 * len + "px"
    });
    // tab切换内容
    var $stepInfo = $('.stepInfo');
    var $flexItems = $stepFlex.find('.flex-item');
    var $infoItems = $stepInfo.find('.info-item');
    // 初始化
    $infoItems.eq(0).show();
    $flexItems.click(function () {
        var index = $(this).index();
        $infoItems.eq(index).show().siblings().hide();
    })
});

