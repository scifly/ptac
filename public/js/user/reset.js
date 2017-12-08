$('#save').on('click',function (e) {
        e.preventDefault();
        var id = $('#user_id').val();
        var password=document.getElementsByName("password")[0];
        var pwd1=document.getElementsByName('password')[1];
        var pwd2=document.getElementsByName('password')[2];
       
        if(password.value === '')
        {
            $.gritter.add({
                title: '操作结果',
                text: '原密码不能为空!',
                image: '../img/error.png'
            });
            password.focus();
            return false;
        }
        else if(pwd1.value === '')
        {
            $.gritter.add({
                title: '操作结果',
                text: '新密码不能为空!',
                image: '../img/error.png'
            });
            pwd1.focus();
            return false;
        }else if(password.value === pwd1.value)
        {
            $.gritter.add({
                title: '操作结果',
                text: '新密码不能不能和原密码相同!',
                image: '../img/error.png'
            });
            pwd1.focus();
            return false;
        }else if(pwd2.value === '')
        {
            $.gritter.add({
                title: '操作结果',
                text: '确认密码不能为空!',
                image: '../img/error.png'
            });
            pwd2.focus();
            return false;
        }else if(pwd1.value!== pwd2.value)
        {
            $.gritter.add({
                title: '操作结果',
                text: '两次密码不一致!',
                image: '../img/error.png'
            });
            pwd2.focus();
            return false;
        }
        var data = {
            password:password.value,
            pwd: pwd1.value
        }

        $.ajax({
            type:'post',
            dataType: 'json',
            url: 'reset',
            data: {
                password:password.value,
                pwd: pwd1.value,
                _token: $('#csrf_token').attr('content')},
            success:function (result) {
                if(result.statusCode === 201){
                    $.gritter.add({
                        title: '操作结果',
                        text: '原密码输入不正确,请重新输入',
                        image: '../img/error.png'
                    });
                }else if(result.statusCode === 200){
                    $.gritter.add({
                        title: '操作结果',
                        text: '修改成功',
                        image: '../img/confirm.png'
                    });
                    window.location = '../logout';
                }
            }
        })

})