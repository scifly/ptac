$(function() {
    $('#signin').on('click', function (e) {
        e.preventDefault();
        var paths = window.location.href.split('?returnUrl=');
        var returnUrl = null;
        if (typeof paths[1] !== 'undefined') {
            returnUrl = paths[1];
        }
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'login',
            data: {
                input: $('#input').val(),
                password: $('#password').val(),
                rememberMe: $('#remember').iCheck('update')[0].checked,
                returnUrl: returnUrl,
                _token: $('input[name="_token"]').val()
            },
            success: function (result) {
                if (result.statusCode === 200) {
                    window.location = result['url'];
                } else {
                    $.gritter.add({
                        title: '登录',
                        text: '用户名/密码错误',
                        image: 'img/error.png'
                    });
                }
            }
        })
    });
});
