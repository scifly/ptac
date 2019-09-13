var $mobile = $('input[name=mobile]'),
    $vericode = $('input[name=vericode]'),
    $verify = $('#verify'),
    $signup = $('#signup'),
    lang = wap.lang();

$('form').submit(function (e) { e.preventDefault(); });
$verify.on('click', function() {
    if ($mobile.val() === '') {
        $.toptip(
            lang === 'zh'
                ? '请输入手机号码'
                : 'Mobile number must not be empty',
            'warning'
        );
        return false;
    }
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../signup',
        data: {
            _token: wap.token(),
            mobile: $mobile.val()
        },
        success: function (result) {
            $.toptip(result['message'], 'success');
            $verify.prop('disabled', true);
            var timeleft = 60,
                timer = setInterval(function () {
                    $verify.html(--timeleft + 's');
                    if (timeleft <= 0) {
                        clearInterval(timer);
                        $verify.prop('disabled', false).html(
                            lang === 'zh' ? '获取验证码' : 'Get vericode'
                        );
                    }
                }, 1000);
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
});
$signup.on('click', function () {
    if ($mobile.val() === '' || $vericode.val() === '') {
        $.toptip(
            lang === 'zh'
                ? '请输入手机号码及验证码'
                : 'Mobile number / vericode must not be empty',
            'warning'
        );
        return false;
    }
    if ($mobile.val().match(/^1[3456789]\d{9}$/) === null) {
        $.toptip(
            lang === 'zh'
                ? '请输入正确的手机号码'
                : 'Incorrect mobile number',
            'warning'
        );
        return false;
    }
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../signup',
        data: $('form').serialize(),
        success: function (result) {
            $.toptip(result['message'], 'success');
            window.location = result['url'];
        },
        error: function (e) {
            wap.errorHandler(e);
        }
    });
});