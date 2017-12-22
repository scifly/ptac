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
                url: '../public/message_delete/' + id,
                success: function (reseult) {

                }
            });
        },
        onCancel: function () {

        }
    });
});