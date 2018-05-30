var wap = {
    errorHandler: function (e) {
        var obj = JSON.parse(e.responseText);
        $.alert(obj['statusCode'] + ' : ' + obj['message']);
    }
};