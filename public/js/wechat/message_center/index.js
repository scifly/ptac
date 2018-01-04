$('.selectlist-layout').click(function () {
    $('.select-container').toggle();
    $('.select-ul').slideToggle('fast');
});

$('.select-ul li').click(function () {
    $('.select-container').toggle();
    $('.select-ul').slideToggle('fast');
    $('.select-ul li').removeClass('c-green');
    $(this).addClass('c-green');
    var html = '' + ($(this).text()) + '<i class="icon iconfont icon-arrLeft-fill"></i>';
    $('.select-box').html(html);
    var type_id = $(this).attr('data-id');
    message(type_id)
});

function message(type_id) {
    if (type_id === '0') {
        $('.table-list').show();
    } else {
        $('.table-list').hide();
        $('.list-' + type_id).show();
    }
}

$('.teacher-list-box').click(function () {
    var id = $(this).attr('id');
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: '../message_update/' + id,
        success: function (result) {
            if (result.statusCode === 200) {
                window.location.href = '../message_show/' + id;
            }
        }
    });
});

$('.weui-navbar__item').click(function(){
    $('.select-ul').hide();
    $('.select-container').hide();
});

$("#searchInput").bind("input propertychange change",function(event){

  var keywords = $(this).val();
  var type = $('.weui-bar__item--on').attr('data-type');
    $('.weui-popup__container .weui-tab__bd-item .list-layout').html('');
    if(keywords === ''){
        $('.weui-popup__container .weui-tab__bd-item .list-layout').html('');
    }else{
        $.ajax({
            type: 'post',
            dataType: 'json',
            data: {keywords:keywords,type:type,_token:$('#csrf_token').attr('content')},
            url: 'message_center',
            success: function ($data) {
                var str = '';
                if($data.type === 'send'){
                    if($data.sendMessages.length !== 0){
                        for(var i=0 ; i< $data.sendMessages.length; i++){
                            var data = $data.sendMessages[i];
                            str += '<div class="table-list ">'+
                                '<div class="line"></div>'+
                                '<div class="teacher-list-box glayline">'+
                                '<div class="teacher-work-box">'+
                                '<a class="teacher-work-head" style="color:#000" href="message_show/'+data.id+'">'+
                                '<div class="titleinfo">'+
                                '<div class="titleinfo-head">'+
                                '<div class="titleinfo-head-left fl">'+
                                '<div class="title ml12">'+ data.title +'</div>'+
                                '<div class="title-info ml12">'+ data.r_user_id+'</div>'+
                                '</div>'+
                                '<span class="worktime">'+ data.created_at.substr(0,10);
                            if(data.sent === 1){
                                str +='<span class="info-status green">已发送</span>';
                            }else{
                                str +='<span class="info-status green">未发送</span>';
                            }
                            str +='</span> </div> </div> </a> </div> </div> </div>';

                        }
                        $('.weui-popup__container .weui-tab__bd-item .list-layout').html(str);
                    }else{
                        $('.weui-popup__container .weui-tab__bd-item .list-layout').html('');
                    }
                }else if($data.type === 'receive'){
                    if($data.receiveMessages.length !== 0){
                        for(var i=0 ; i< $data.receiveMessages.length; i++){
                            var data = $data.receiveMessages[i];
                            str += '<div class="table-list ">'+
                                '<div class="line"></div>'+
                                '<div class="teacher-list-box glayline">'+
                                '<div class="teacher-work-box">'+
                                '<a class="teacher-work-head" style="color:#000" href="message_show/'+data.id+'">'+
                                '<div class="titleinfo">'+
                                '<div class="titleinfo-head">'+
                                '<div class="titleinfo-head-left fl">'+
                                '<div class="title ml12">'+ data.title +'</div>'+
                                '<div class="title-info ml12">'+ data.s_user_id+'</div>'+
                                '</div>'+
                                '<span class="worktime">'+ data.created_at.substr(0,10)+
                                '</span></div></div></a></div></div></div>';

                        }
                        $('.weui-popup__container .weui-tab__bd-item .list-layout').html(str);
                    }else{
                        $('.weui-popup__container .weui-tab__bd-item .list-layout').html('');
                    }
                }

            }
        });
    }

});
