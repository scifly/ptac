//# sourceURL=create.js
page.create('formApp', 'apps');
var $name = $('.name'),
    $appid = $('.appid'),
    $appsecret = $('.appsecret'),
    $url = $('.url'),
    $token = $('.token'),
    $eak = $('.eak');

$('#category').on('change', function () {
    switch(parseInt($(this).val())) {
        case 1:
            $name.hide(); $url.hide(); $token.hide(); $eak.hide();
            $appid.show(); $appsecret.show();
            break;
        case 2:
            $url.hide();
            $name.show(); $appid.show(); $appsecret.show(); $token.show(); $eak.show();
            break;
        default:
            $name.hide(); $appid.hide();
            $appsecret.show(); $url.show(); $token.show(); $eak.show();
            break;
    }
});