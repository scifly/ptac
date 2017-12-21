
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
        var id = document.getElementById('id').value;
        var telephone = document.getElementById('telephone').value;
        var email = document.getElementById('email').value;
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
            type: 'PUT',
            dataType: 'json',
            url: 'update/' + id,
            data:$form.serialize(),
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

