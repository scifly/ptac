$('#reset').on('click', function (e) {
    e.preventDefault();

    var password = document.getElementsByName("password")[0];
    var pwd1 = document.getElementsByName('password')[1];
    var pwd2 = document.getElementsByName('password')[2];

    if (password.value === '') {
        page.inform('重置密码', '原密码不能为空!', page.failure);
        password.focus();
        return false;
    }else if (pwd1.replace(/^\s*|\s*$/g, "") === '') {
        page.inform('重置密码', '新密码不正确，请勿使用空格!', page.failure);
        pwd1.focus();
        return false;
    } else if (pwd1.value === '') {
        page.inform('重置密码', '新密码不能为空!', page.failure);
        pwd1.focus();
        return false;
    } else if (password.value === pwd1.value) {
        page.inform('重置密码', '新密码不能不能和原密码相同!', page.failure);
        pwd1.focus();
        return false;
    } else if (pwd2.value === '') {
        page.inform('重置密码', '确认密码不能为空!', page.failure);
        pwd2.focus();
        return false;
    } else if (pwd1.value !== pwd2.value) {
        page.inform('重置密码', '两次密码不一致!', page.failure);
        pwd2.focus();
        return false;
    }

    $.ajax({
        type: 'post',
        dataType: 'json',
        url: 'reset',
        data: {
            password: password.value,
            pwd: pwd1.value,
            _token: page.token()
        },
        success: function (result) {
            if (result.statusCode === 400) {
                page.inform('重置密码','原密码输入不正确,请重新输入',page.failure);

            }if(result.statusCode === 401) {
                page.inform('重置密码','请填写正确的密码',page.failure);
            } else if (result.statusCode === 200) {
                page.inform('重置密码','修改成功',page.success);
                window.location = '../logout';
            }
        },
        error: function (e) {
            page.errorHandler(e);
        }
    }).on('form:submit', function () {
        return false;
    });
});