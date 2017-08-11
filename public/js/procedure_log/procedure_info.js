$(function () {
    // 设置stepflex的宽度
    var $stepflex = $('.stepflex');
    var len = $stepflex.find('dl').length;
    $stepflex.css({
        'width':160 * len + "px"
    });
});

