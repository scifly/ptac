/**
 * Created by Administrator on 2017-07-21 0021.
 */
$(crud.create('formWapSite','wapsites'));
$(function () {
    $('#media_ids').fileinput({
        "language":'zh',
        'theme': 'explorer',
        'maxFileCount': 5,
        // 'uploadUrl': '../wapsites/uploadImage'
    });
    var data = new FormData();
    var imgInputElement=document.getElementById('media_ids');
    for(var i=0; i<imgInputElement.files.length; i++){
        data.append('media_ids[]', imgInputElement.files[i]);
    }
});
