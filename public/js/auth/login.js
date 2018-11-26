$(function() {
    $('#signin').on('click', function (e) {
        e.preventDefault();
        var username = $('#input').val();
        var password = $('#password').val();

        if(username.length ===0 || $.trim(username) === ""){
            page.inform('登录', '用户名不能为空!', page.failure);
            return false;
        }
        if(password.length ===0 || $.trim(password) === ''){
            page.inform('登录', '密码不能为空!', page.failure);
            return false;
        }
        var paths = window.location.href.split('?returnUrl=');
        var returnUrl = null;

        if (typeof paths[1] !== 'undefined') {
            returnUrl = paths[1];
        }
        $('.overlay').show();
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: 'login',
            data: {
                input: username,
                password: password,
                rememberMe: $('#remember').iCheck('update')[0].checked,
                returnUrl: returnUrl,
                _token: $('input[name="_token"]').val()
            },
            success: function (result) {
                if (typeof result['url'] !== 'undefined') {
                    window.location = result['url'];
                }
                window.location = returnUrl ? decodeURIComponent(returnUrl) : '/';
            },
            error: function(e) {
                $('.overlay').hide();
                var obj = JSON.parse(e.responseText);

                if (e.status === 498) window.location.reload();
                $.gritter.add({
                    title: '登录',
                    text: obj['message'],
                    image: 'img/error.png'
                });
            }
        })
    });
});