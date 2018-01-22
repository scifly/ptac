
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


            page.inform('出现异常', '座机号码格式有误,请重填!', page.failure);

            return false;
        }

        if(email.length !== 0 && !reg.test(email)){

            page.inform('出现异常', '电子邮件格式有误,请重填!', page.failure);
            return false;
        }

        $.ajax({
            type: 'PUT',
            dataType: 'json',
            url: 'update/' + id,
            data:$form.serialize(),
            success: function (result) {
                if(result.statusCode === 200){
                    page.inform('操作成功', '更新成功!', page.success);
                }else{
                    page.inform('出现异常', '更新失败!', page.failure);
                }
            },
            error: function (e) { page.errorHandler(e); }
        });
        input_obj.attr("readonly","true");

    });
}

