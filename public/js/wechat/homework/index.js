$('#pay').on('click', function () { callpay(); });

function callpay() {
    if (typeof WeixinJSBridge === "undefined") {
        if (document.addEventListener) {
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        } else if (document.attachEvent) {
            document.attachEvent('WeixinJSBridgeReady', jsApiCall);
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    } else {
        jsApiCall();
    }
}
// 调用微信JS api 支付
function jsApiCall() {
    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',
        $.parseJSON($('#params').val()),
        function (res) {
            WeixinJSBridge.log(res['err_msg']);
            alert(res['err_code']);
            alert(res['err_desc']);
            alert(res['err_msg']);
            alert(res['err_code'] + res['err_desc'] + res['err_msg']);
        }
    );
}
// 获取共享地址
function editAddress() {
    WeixinJSBridge.invoke(
        'editAddress',
        $.parseJSON($('#url').val()),
        function (res) {
            var value1 = res['proviceFirstStageName'],
                value2 = res['addressCitySecondStageName'],
                value3 = res['addressCountiesThirdStageName'],
                value4 = res['addressDetailInfo'],
                tel = res['telNumber'];

            alert(value1 + value2 + value3 + value4 + ":" + tel);
        }
    );
}
window.onload = function () {
    if (typeof WeixinJSBridge === "undefined") {
        $(document).on('WeixinJSBridgeReady', function(){
            if (document.addEventListener) {
                document.addEventListener('WeixinJSBridgeReady', editAddress, false);
            } else if (document.attachEvent) {
                document.attachEvent('WeixinJSBridgeReady', editAddress);
                document.attachEvent('onWeixinJSBridgeReady', editAddress);
            }
        });
    } else {
        editAddress();
    }
};