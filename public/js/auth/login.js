$('#signin').on('click',function () {
    $.ajax({
        type:'GET',
        dataType:'json',
        url: 'http://sandbox.dev:8080/ptac/public/login',
        data: {input: $('#input').val(), password: $('#password').val(), rememberMe: $('#remember').val() },
        success: function (result) {
            if(result.statusCode === 200 )
            {
                window.location = result['url'];
            }
        }
    })
} )