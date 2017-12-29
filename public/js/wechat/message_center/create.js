var token = $('#csrf_token').attr('content');
choose_item();
getdept();
var msg_type = $('#type');

msg_type.select({
    title: "选择类型",
    items: [
        {
            title: "文本",
            value: "text"
        },
        {
            title: "卡片",
            value: "textcard"
        },
        {
            title: "图文",
            value: "news"
        },
        {
            title: "图片",
            value: "image"
        },
        {
            title: "视频",
            value: "video"
        },
        {
            title: "短信",
            value: "sms"
        }

    ]
});

msg_type.change(function () {
    var type = $(this).attr('data-values');
    $('.js-content-item input').val('');
    $('#emojiInput').html('');
    switch (type) {
        case 'text':
            //文本
            $('.js-content-item').hide();
            $('.js-content').show();
            break;
        case 'textcard':
            //卡片
            $('.js-content-item').hide();
            $('.js-content').show();
            $('.js-upload-img').show();
            break;
        case 'news':
            //图文
            $('.js-content-item').hide();
            $('.js-title').show();
            $('.js-content').show();
            $('.js-upload-img').show();
            $('.js-content_source_url').show();
            $('.js-author').show();
            $('.js-mpnews-cover').show();
            break;
        case 'image':
            //图片
            $('.js-content-item').hide();
            $('.js-image').show();
            break;
        case 'voice':
            //音频
            break;
        case 'video':
            //视频
            $('.js-content-item').hide();
            $('.js-title').show();
            $('.js-video').show();
            $('.js-uploadvideo').show();
            $('.js-description').show();
            break;
        case 'sms':
            //短信
            $('.js-content-item').hide();
            $('.js-title').hide();
            $('.js-content').show();
            break;
    }

});


$(".ma_expect_date").datetimePicker();

$('.js-search-input').bind("input propertychange change", function (event) {
    var txt = $(this).val();
    if (txt == '') {
        $('.js-choose-items .weui-check__label').show();
        $('.js-choose-breadcrumb-li').text('全部');
    } else {
        $('.js-choose-breadcrumb-li').text('搜索结果');
        $('.js-choose-items .weui-check__label').hide();
        $('.js-choose-items .weui-check__label').each(function () {
            var uname = $(this).find('.contacts-text').text();
            if (uname.indexOf(txt) >= 0) {
                $(this).show();
            }
        });
    }
});
show_group();

function show_group() {
    $('.show-group').click(function () {
        //展示下一个分组
        var id = $(this).prev().attr('data-uid');
        var name = $(this).prev().find('span').html();
        var choose_box = $('.air-choose-group');
        var choose_dept = $('.js-choose-breadcrumb-ol');
        var html =
            '<li data-id="' + id + '" class="js-choose-breadcrumb-li headclick"><a>>' + name + '</a></li>';
        choose_dept.append(html);
        $.ajax({
            type: 'GET',
            data: {},
            url: '../public/message_dept/' + id,
            success: function (result) {
                if (result.statusCode === 200) {
                    choose_box.html(result.message);
                    show_group();
                    choose_item();
                    remove_choose_result();
                    getdept();
                } else {
                    choose_box.empty();
                }
            }
        });
    });
}

$('#choose-btn-ok').click(function () {
    var html = $('.js-choose-header-result').html();
    $('#homeWorkChoose').html(html);
    $.closePopup();
});

function choose_item() {
    $(".choose-item-btn").change(function () {
        var $this = $(this).parents('.weui-check__label');
        var num = $this.attr('data-item');
        var type = $this.attr('data-type');
        if ($(this).is(':checked')) {
            var imgsrc = $this.find('img').attr('src');
            var uid = $this.attr('data-uid');
            if (type == 'group') {
                var html = '<a class="choose-results-item js-choose-results-item choose-item-type-group" id="group-' + num + '" data-list="' + num + '" data-uid="' + uid + '" data-type="' + type + '">' +
                    '<img src="' + imgsrc + '">' +
                    '</a>';
            } else {
                var html = '<a class="choose-results-item js-choose-results-item choose-item-type-person" id="person-' + num + '" data-list="' + num + '" data-uid="' + uid + '" data-type="' + type + '">' +
                    '<img src="' + imgsrc + '" style="border-radius:50%">' +
                    '</a>';
            }

            $('.js-choose-header-result').prepend(html);

            remove_choose_result();
            count_result();
        } else {
            $('.js-choose-header-result').find('#' + type + '-' + num).remove();
            $('.air-choose-group').removeClass('air-checkall');
            $('#checkall').prop('checked', false);
            count_result();
        }
    });
}


$('#checkall').change(function () {
    if ($(this).is(':checked')) {
        $('.choose-item-btn').prop('checked', true);
        var html = '';
        $('.js-choose-items .weui-check__label').each(function (i, vo) {
            var type = $(vo).attr('data-type');
            var num = $(vo).attr('data-item');
            var uid = $(vo).attr('data-uid');
            var imgsrc = $(vo).find('img').attr('src');
            if (type == 'group') {
                html += '<a class="choose-results-item js-choose-results-item choose-item-type-group" id="group-' + num + '" data-list="' + num + '" data-uid="' + uid + '" data-type="' + type + '">' +
                    '<img src="' + imgsrc + '">' +
                    '</a>';
            } else {
                html += '<a class="choose-results-item js-choose-results-item choose-item-type-person" id="person-' + num + '" data-list="' + num + '" data-uid="' + uid + '" data-type="' + type + '">' +
                    '<img src="' + imgsrc + '" style="border-radius:50%">' +
                    '</a>';
            }
        });
        $('.js-choose-header-result').html(html);
        remove_choose_result();
        $('.air-choose-group').addClass('air-checkall');
        count_result();
    } else {
        $('.choose-item-btn').prop('checked', false);
        $('.js-choose-header-result').html('');
        $('.air-choose-group').removeClass('air-checkall');
        count_result();
    }
});

function count_result() {
    var grouptotal = $('.js-choose-header-result .js-choose-results-item.choose-item-type-group').length;
    var persontotal = $('.js-choose-header-result .js-choose-results-item.choose-item-type-person').length;
    $('.js-choose-num').text('已选' + grouptotal + '个分组,' + persontotal + '名用户');
}

function remove_choose_result() {
    $('.js-choose-results-item').click(function () {
        var num = $(this).attr('data-list');
        var type = $(this).attr('data-type');
        $(this).remove();
        $('#' + type + '-' + num).find('.choose-item-btn').prop('checked', false);
        count_result();
    });
}

$(".weui-switch").change(function () {
    if ($(this).is(':checked')) {
        $('.hw-time').slideToggle('fast');
    } else {
        $('.hw-time').slideToggle('fast');
    }
});

function upload_cover() {
    var formData = new FormData();
    formData.append('file', $('#pic-url')[0].files[0]);
    formData.append('_token', token);
    $.ajax({
        url: '../message_upload',
        type: 'POST',
        cache: false,
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            if (result.statusCode === 200) {
                var html = '<img class="uploadimg-item pic-url" src="http://sandbox.dev:8080/ptac/' + result.message.path + '" id="pic-url" style="width: 100%" data-id="' + result.message.id + '">' +
                    '<input id="pic-url" onchange="upload_cover()" class="weui-uploader__input pic-url" type="file" accept="image/*" multiple="">';

                $('#cover').html(html);

            }
        }
    });

}

$(function () {
    // 允许上传的图片类型
    var allowTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];
    // 1024KB，也就是 1MB
    var maxSize = 1024 * 1024;
    // 图片最大宽度
    var maxWidth = 300;
    // 最大上传图片数量
    var maxCount = 6;
    var tmp = 1;
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

                    // console.log(base64);
                    var html = '<img class="uploadimg-item" src="' + base64 + '" id="uploadimg-' + tmp + '" style="width: 300px;height: 187px">';
                    $('#emojiInput').append(html);
                    // 然后假装在上传，可以post base64格式，也可以构造blob对象上传，也可以用微信JSSDK上传
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
        var formData = new FormData();
        formData.append('file', $('#uploaderInput')[0].files[0]);
        formData.append('_token', token);
        $.ajax({
            url: '../message_upload',
            type: 'POST',
            cache: false,
            data: formData,
            processData: false,
            contentType: false,
            success: function (result) {
                if (result.statusCode === 200) {
                    $('#uploadimg-' + tmp).attr('data-media-id', result.message.id);
                    $('#uploadimg-' + tmp).attr('src', 'http://weixin.028lk.com/' + result.message.path);
                }
            }
        });
        tmp++;
    });

    // $('#mpnews_cover').change(function () {
    //     var formData = new FormData();
    //     formData.append('file', $('#mpnews_cover')[0].files[0]);
    //     formData.append('_token', token);
    //     $.ajax({
    //         url: "../public/message_upload",
    //         data: formData,
    //         type: 'POST',
    //         dataType: 'json',
    //         contentType: false,
    //         processData: false,
    //         cache: false,
    //         success: function (result) {
    //             if (result.statusCode === 200) {
    //                 $('#mpnews_cover_img').attr('src', 'http://sandbox.ddd:8080/public/' + result.message.path);
    //             }
    //         }
    //     });
    // });

    $("#upload_video").change(function () {
        $('#upload-wait').show();
        var $this = $(this);
        var formData = new FormData();
        formData.append('file', $('#upload_video')[0].files[0]);
        formData.append('_token', token);
        formData.append('type', msg_type.attr('data-values'));
        $.ajax({
            url: "../message_upload",
            data: formData,
            type: 'POST',
            dataType: 'json',
            contentType: false,
            processData: false,
            cache: false,
            success: function (result) {
                $('#upload-wait').hide();
                if (result.statusCode === 1) {
                    var html = '<video class="video-id" id="' + result.data.id + '" src="' + 'http://weixin.028lk.com/' + result.data.path + '" controls="controls" style="height: 300px; width: 200px"></video>' +
                        '<input id="video_media_id" name="video_media_id" value="' + result.data.media_id + '" hidden>';
                    $this.parent().parent().html(html);
                } else {
                    $.alert('上传失败，请稍后重新尝试！')
                }
            }
        });
    });

    $('#upload_image').change(function () {
        $('#upload-wait').show();
        var $this = $(this);
        var formData = new FormData();
        formData.append('file', $('#upload_image')[0].files[0]);
        formData.append('_token', token);
        formData.append('type', msg_type.attr('data-values'));
        $.ajax({
            url: "../message_upload",
            data: formData,
            type: 'POST',
            dataType: 'json',
            contentType: false,
            processData: false,
            cache: false,
            success: function (result) {
                $('#upload-wait').hide();
                if (result.statusCode === 1) {
                    var html = '<img class="img-id" id="' + result.data.id + '" src="' + 'http://weixin.028lk.com/' + result.data.path + '" style="height: 200px; width: 300px">' +
                        '<input id="image_media_id" name="image_media_id" value="' + result.data.media_id + '" hidden>';
                    $this.parent().parent().html(html);
                } else {
                    $.alert('上传失败，请稍后重新尝试！')
                }
            }
        });
    });


    var title = '';
    var content = '';
    var media_ids = [];
    var wechat_media_id = '';
    // var time = '';
    $('.release').on('click', function () {
        var pic_url = $('.pic-url').attr('data-id');
        media_ids = [];
        title = $('#title').val();
        content = $('#emojiInput').html();
        // time = $('#time').val();
        var department_ids = [];
        var user_ids = [];
        var choose = $('#homeWorkChoose');
        var type = msg_type.attr('data-values');

        if (type === 'video') {
            content = $('#description-video').val();
            wechat_media_id = $('#video_media_id').val();
            media_ids.push($('.video-id').attr('id'));
            if(media_ids.length === 0){
                $.alert('亲，还没有上传视频！');
                return;
            }
            if(content == null){
                $.alert('亲，请填写描述！');
                return;
            }
        }
        if (type === 'image') {
            content = '0';
            wechat_media_id = $('#image_media_id').val();
            media_ids.push($('.img-id').attr('id'));
            if(media_ids.length === 0){
                $.alert('亲，还没有上传图片！');
                return;
            }
        }
        if (type === 'sms') {
            title = '短信信息';
        }

        $('.uploadimg-item').each(function () {
            media_ids.push($(this).attr('data-media-id'));
        });

        choose.find('a.choose-item-type-group').each(function () {
            department_ids.push($(this).attr('data-uid'));
        });
        choose.find('a.choose-item-type-person').each(function () {
            user_ids.push($(this).attr('data-uid'));
        });
        //前端验证
        if ((user_ids.length === 0) && (department_ids.length === 0)) {
            $.alert('发送对象不能为空');
            return false;
        }
        if(content == null){
            $.alert('发送内容不能为空');
            return false;
        }
        if(title == null){
            $.alert('标题不能为空');
            return false;
        }

        $.ajax({
            type: 'POST',
            data: {
                '_token': token,
                'title': title,
                'content': content,
                // 'time': time,
                'department_ids': department_ids,
                'user_ids': user_ids,
                'media_ids': media_ids,
                'pic_url': pic_url,
                'type': type,
                'mediaid': wechat_media_id
            },
            url: '../public/message_store',
            success: function (result) {
                if (result.statusCode === 200) {
                    $.alert('消息发送成功！', function () {
                        window.location.href = '../message_center';
                    });
                } else {
                    $.alert('消息发送失败，请稍后重试！');
                }
            }
        });

    });
});

function getdept() {
    $(".js-choose-breadcrumb-ol li").on('click', function () {
        var id = $(this).attr("data-id");
        // var name = $(this).find('a').html();
        var choose_box = $('.air-choose-group');
        $(this).nextAll().remove();
        $.ajax({
            type: 'GET',
            data: {},
            url: '../message_dept/' + id,
            success: function (result) {
                if (result.statusCode === 200) {
                    choose_box.html(result.message);
                    show_group();
                    choose_item();
                    remove_choose_result();
                    getdept();
                } else {
                    choose_box.empty();
                }
            }
        });
    });
}
