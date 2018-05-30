var wap = {
    errorHandler: function (e) {
        var obj = JSON.parse(e.responseText);
        $('#notification').hide();
        $.alert(obj['statusCode'] + '\n' + obj['message']);
    }
};