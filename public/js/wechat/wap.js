var wap = {
    errorHandler: function (e) {
        var result = JSON.parse(e.responseText),
            statusCode = result['statusCode'],
            message = result['message'];
        $('#notification').hide();

        $.toptip(message, statusCode <= 300 ? 'warning' : 'error');
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
            dd = '0' + dd;
        }
        if (mm < 10) {
            mm = '0' + mm;
        }

        return yyyy + '-' + mm + '-' + dd;
    }
};
var pusherKey = $('#pusher_key').attr('content'),
    pusherCluster = $('#pusher_cluster').attr('content'),
    memberId = $('#member_id').val(),
    pusher = new Pusher(pusherKey, {cluster: pusherCluster, encrypted: true}),
    channel = pusher.subscribe('member.' + memberId);

channel.bind('broadcast', function (data) {
    $.toptip(data['message'], 'success');
});