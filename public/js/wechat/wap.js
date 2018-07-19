var wap = {
    errorHandler: function (e) {
        var obj = JSON.parse(e.responseText);
        $('#notification').hide();
        $.alert(obj['statusCode'] + '\n' + obj['message']);
    },
    token: function () {
        return $('#csrf_token').attr('content');
    },
    today: function () {
        var today = new Date(),
            dd = today.getDate(),
            mm = today.getMonth() + 1, //January is 0!
            yyyy = today.getFullYear();

        if (dd < 10) {
            dd = '0' + dd
        }

        if ( mm < 10) {
            mm = '0' + mm
        }

        return yyyy + '-' + mm + '-' + dd ;
    }
};