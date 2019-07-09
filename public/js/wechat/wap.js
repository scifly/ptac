var wap = {
    errorHandler: function (e) {
        var result = JSON.parse(e.responseText),
            statusCode = result['statusCode'],
            message = result['message'];
        $('#notification').hide();
        $.toptip(message, statusCode <= 300 ? 'warning' : 'error');
    },
    token: function () {
        return $('meta[name="csrf-token"]').attr('content');
    },
    today: function () {
        var today = new Date(),
            dd = today.getDate(),
            mm = today.getMonth() + 1, //January is 0!
            yyyy = today.getFullYear();

        if (dd < 10) dd = '0' + dd;
        if (mm < 10) mm = '0' + mm;

        return yyyy + '-' + mm + '-' + dd;
    }
};
var pusherKey = $('meta[name="pusher-key"]').attr('content'),
    pusherCluster = $('meta[name="pusher-cluster"]').attr('content'),
    memberId = $('#member_id').val(),
    pusher = new Pusher(pusherKey, {cluster: pusherCluster, encrypted: true}),
    channel = pusher.subscribe('member.' + memberId);

channel.bind('broadcast', function (data) {
    $.toptip(data['message'], 'success');
});
$(function() { FastClick.attach(document.body); });