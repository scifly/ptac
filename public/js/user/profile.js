
$form = $('#formUser');
$('.edit_input').click(function () {
    var input_obj = $(this).prevAll('.form-control');
    input_obj.removeAttr("readonly");
    input_obj.focus();
    input_obj.unbind();
    save_input(input_obj);
});

function save_input(input_obj) {
    input_obj.blur(function () {
        var user_id = document.getElementById('user_id').value;
        var telephone = document.getElementById('telephone').value;
        var email = document.getElementById('email').value;
        var username = document.getElementById('username').value;
        var wechatid = document.getElementById('wechatid').value;
        var gender = document.getElementById('gender').value;
        var english_name = $('#english_name').val();
        var data = {
            user_id: user_id,
            telephone: telephone,
            email: email,
            username: username,
            wechatid: wechatid,
            gender: gender,
            english_name: english_name,
            _token: $('#csrf_token').attr('content')
        }
        const  reg = /^[A-Za-z\d]+([-_.][A-Za-z\d]+)*@([A-Za-z\d]+[-.])+[A-Za-z\d]{2,4}$/;

        if(telephone.length !== 0 && !/^(\(\d{3,4}\)|\d{3,4}-|\s)?\d{7,14}$/.test(telephone)){
            $.gritter.add({
                title: '操作结果',
                text: '座机号码格式有误,请重填!',
                image: '../img/error.png'
            });
            return false;
        }

        if(email.length !== 0 && !reg.test(email)){
            $.gritter.add({
                title: '操作结果',
                text: '电子邮件格式有误,请重填!',
                image: '../img/error.png'
            });
            return false;
        }
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: 'profile',
            data:data,
            success: function (result) {
                if(result.statusCode === 200){
                    $.gritter.add({
                        title: '操作结果',
                        text: '更新成功',
                        image: '../img/confirm.png'
                    });
                }else{
                    $.gritter.add({
                        title: '操作结果',
                        text: '更新失败',
                        image: '../img/error.png'
                    });
                }
            }
        });
        input_obj.attr("readonly","true");

    });
}

