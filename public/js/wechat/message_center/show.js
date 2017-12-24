$('.icon-lajixiang').on('click',function () {
    var id = $('#id').val();
    $.confirm({
        title: '确认删除这条信息？',
        text: '',
        onOK: function () {
            //点击确认
            $.ajax({
                type: 'DELETE',
                dataType: 'json',
                url: '../message_delete/' + id,
                data: {_token: $('#csrf_token').attr('content')},
                success: function (reseult) {
                    if(reseult.statusCode === 200){
                        $.alert("删除成功！", function() {
                            window.location.href = '../message_center';
                        });
                    } else {
                        $.alert('删除失败，稍后请重新尝试！')
                    }
                }
            });
        },
        onCancel: function () {

        }
    });
});