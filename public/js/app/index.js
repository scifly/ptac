var $sync = $('#sync');
var $form = $('#formApp');
var sync = function () {
    $sync.html(page.ajaxLoader());
    $.ajax({
        type: 'POST',
        url: '../apps/index',
        dataType: 'json',
        data: $('#formApp').serialize(),
        success: function (result) {
            var data = result["app"];
            var app = '';
            if(result['action'] === 'create') {
                app = '<tr id="app"' + data['agentid'] + '">' +
                    '<td>' + data['id'] + '</td>' +
                    '<td>' + data['agentid'] + '</td>' +
                    '<td>' + data['name'] + '</td>' +
                    '<td><img style="width: 16px; height: 16px;" src="' + data['square_logo_url'] + '"/></td>' +
                    '<td>' + data['description'] + '</td>' +
                    '<td>' + data['created_at'] + '</td>' +
                    '<td>' + data['updated_at'] + '</td>' +
                    '<td>' + data['enabled'] + '</td>' +
                    '</tr>';
                var $na = $('#na');
                if(typeof $na !== 'undefined') {
                    $na.remove();
                }
                $('table tbody').append(app);
            } else {
                console.log(result["app"]);
                var $tr = $('#app' + data['agentid']);
                app = '<td>' + data['id'] + '</td>' +
                    '<td>' + data['agentid'] + '</td>' +
                    '<td>' + data['name'] + '</td>' +
                    '<td><img style="width: 16px; height: 16px;" src="' + data['square_logo_url'] + '"/></td>' +
                    '<td>' + data['description'] + '</td>' +
                    '<td>' + data['created_at'] + '</td>' +
                    '<td>' + data['updated_at'] + '</td>' +
                    '<td>' + data['enabled'] + '</td>';
                $tr.html(app);
            }
            $sync.html('同步应用');
        },
        error: function() {}
    });
};

$form.parsley().on('form:validated', function () {
    if($('.parsley-error').length === 0) { sync(); }
}).on('form:submit', function () {
    return false;
});

$(document).on('click', '.btn-primary', function() {
    var $this = $(this);
    var $tr = $this.parents().eq(1);
    var id = $tr.children('td').eq(0).html();
    console.log(id);

    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    page.getTabContent($activeTabPane, 'apps/edit/' + id);
    $(document).off('click', '.btn-primary');
});
$(document).on('click', '.bg-purple', function() {
    // $('.overlay').show();
    var $this = $(this);
    var $tr = $this.parents().eq(1);
    var id = $tr.children('td').eq(0).html();
    console.log(id);

    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    page.getTabContent($activeTabPane, 'apps/menu/' + id);
    $(document).off('click', '.bg-purple');
});
