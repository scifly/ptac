/**
 * Created by Administrator on 2017-07-21 0021.
 */
$(crud.create('formWapSite','wapsites'));
$(function () {
    $('#media_ids').fileinput({
        "language":'zh',
        'theme': 'explorer',
        'maxFileCount': 5,
        'uploadUrl': '#'
    });
});
