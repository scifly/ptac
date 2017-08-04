/**
 * Created by Administrator on 2017-07-21 0021.
 */
$(crud.create('formWapSite', 'wapsites'));
$(function () {
    // $('#media_ids').fileinput({
    //     "language":'zh',
    //     'theme': 'explorer',
    //     'maxFileCount': 5,
    //     'uploadUrl': '#'
    // });
    // 初始化
    $('#uploadFile').fileinput({
        "language": 'zh',
        'theme': 'explorer',
        'maxFileCount': 5,
        'uploadUrl': '#',
        'showUpload': false,
        'fileActionSettings': {
            showRemove: true,
            showUpload: false,
            showDrag: false
        }
    });
    $('#upload').click(function () {
        var data = new FormData();
        var imgInputElement = document.getElementById('uploadFile');
        var len = imgInputElement.files.length;
        for (var i = 0; i < len; i++) {
            data.append('img[]', imgInputElement.files[i]);
        }
        if (len !== 0) {
            $.ajax({
                type: 'post',
                url: "#",
                data: data,
                success: function () {

                }
            })
        }
    })
});
