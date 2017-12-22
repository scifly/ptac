$(".ma_expect_date").datetimePicker();

$('.js-search-input').bind("input propertychange change",function(event){
    var txt = $(this).val();
    if(txt == ''){
        $('.js-choose-items .weui-check__label').show();
        $('.js-choose-breadcrumb-li').text('全部');
    }else{
        $('.js-choose-breadcrumb-li').text('搜索结果');
        $('.js-choose-items .weui-check__label').hide();
        $('.js-choose-items .weui-check__label').each(function(){
            var uname = $(this).find('.contacts-text').text();
            if(uname.indexOf(txt) >= 0){
                $(this).show();
            }
        });
    }
});

$('#choose-btn-ok').click(function(){
    var html = $('.js-choose-header-result').html();
    $('#homeWorkChoose').html(html);
    $.closePopup();
});

$(".choose-item-btn").change(function() {
    var $this = $(this).parents('.weui-check__label');
    var num = $this.attr('data-item');
    if($(this).is(':checked')){
        var imgsrc = $this.find('img').attr('src');
        var uid = $this.attr('data-uid');
        var html = '<a class="choose-results-item js-choose-results-item" id="list-'+num+'" data-list="'+num+'" data-uid="'+uid+'">'+
            '<img src="'+imgsrc+'">'+
            '</a>';
        $('.js-choose-header-result').prepend(html);
        remove_choose_result();
        var total = $('.js-choose-header-result .js-choose-results-item').length;
        $('.js-choose-num').text('已选'+total+'名用户');
    }else{
        $('.js-choose-header-result').find('#list-'+num).remove();
    }
});

$('#checkall').change(function() {
    if($(this).is(':checked')){
        $('.choose-item-btn').prop('checked',true);
        var html = '';
        $('.js-choose-items .weui-check__label').each(function(i,vo){
            var num = $(vo).attr('data-item');
            var uid = $(vo).attr('data-uid');
            var imgsrc = $(vo).find('img').attr('src');
            html += '<a class="choose-results-item js-choose-results-item" id="list-'+num+'" data-list="'+num+'" data-uid="'+uid+'">'+
                '<img src="'+imgsrc+'">'+
                '</a>';
        });
        $('.js-choose-header-result').html(html);
        remove_choose_result();
        var total = $('.js-choose-header-result .js-choose-results-item').length;
        $('.js-choose-num').text('已选'+total+'名用户');
    }else{
        $('.choose-item-btn').prop('checked',false);
        $('.js-choose-header-result').html('');
        var total = $('.js-choose-header-result .js-choose-results-item').length;
        $('.js-choose-num').text('已选'+total+'名用户');
    }
});

function remove_choose_result(){
    $('.js-choose-results-item').click(function(){
        var num = $(this).attr('data-list');
        $(this).remove();
        $('#item-'+num).find('.choose-item-btn').prop('checked',false);
        var total = $('.js-choose-header-result .js-choose-results-item').length;
        $('.js-choose-num').text('已选'+total+'名用户');
    });
}
$(".weui-switch").change(function() {
    if($(this).is(':checked')){
        $('.hw-time').slideToggle('fast');
    }else{
        $('.hw-time').slideToggle('fast');
    }
});

$(function () {
    // 允许上传的图片类型
    var allowTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
    // 1024KB，也就是 1MB
    var maxSize = 1024 * 1024;
    // 图片最大宽度
    var maxWidth = 300;
    // 最大上传图片数量
    var maxCount = 6;
    $('.js_file').on('change', function (event) {
        var files = event.target.files;

        // 如果没有选中文件，直接返回
        if (files.length === 0) {
            return;
        }

        for (var i = 0, len = files.length; i < len; i++) {
            var file = files[i];
            var reader = new FileReader();

            // 如果类型不在允许的类型范围内
            if (allowTypes.indexOf(file.type) === -1) {
                $.weui.alert({text: '该类型不允许上传'});
                continue;
            }

            if (file.size > maxSize) {
                $.weui.alert({text: '图片太大，不允许上传'});
                continue;
            }

            if ($('.weui_uploader_file').length >= maxCount) {
                $.weui.alert({text: '最多只能上传' + maxCount + '张图片'});
                return;
            }

            reader.onload = function (e) {
                var img = new Image();
                img.onload = function () {
                    // 不要超出最大宽度
                    var w = Math.min(maxWidth, img.width);
                    // 高度按比例计算
                    var h = img.height * (w / img.width);
                    var canvas = document.createElement('canvas');
                    var ctx = canvas.getContext('2d');
                    // 设置 canvas 的宽度和高度
                    canvas.width = w;
                    canvas.height = h;
                    ctx.drawImage(img, 0, 0, w, h);
                    var base64 = canvas.toDataURL('image/png');

                    console.log(base64);
                    var html = '<img src="'+base64+'">';
                    $('#emojiInput').append(html);
                    // 然后假装在上传，可以post base64格式，也可以构造blob对象上传，也可以用微信JSSDK上传

                };

                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    });
});
