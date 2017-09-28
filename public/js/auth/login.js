$('#signin').on('click', function (e) {
    e.preventDefault();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '../login',
        data: {
            input: $('#input').val(),
            password: $('#password').val(),
            rememberMe: $('#remember').val(),
            _token: $('input[name="_token"]').val()
        },
        success: function (result) {
            if (result.statusCode === 200) {
                window.location = result['url'];
            } else {
                alert('用户名或者密码错误!');
            }
        }
    })
});
