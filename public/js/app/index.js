var $sync = $('#sync');
var $form = $('#formApp');
var sync = function () {
    $('.overlay').show();
    $.ajax({
        type: 'POST',
        url: '../apps/index',
        dataType: 'json',
        data: $('#formApp').serialize(),
        success: function (result) {
            var data = result["app"];
            var app = '';
            var className = data['enabled'] ? 'text-green' : 'text-gray';
            var title = data['enabled'] ? '已启用' : '未启用';
            var status = '<i class="fa fa-circle ' + className + '" title="' + title + '"></i>\n\n&nbsp;&nbsp;\n' +
                '<a href="#"><i class="fa fa-pencil" title="修改"></i></a>\n\n&nbsp;&nbsp;\n' +
                '<a href="#"><i class="fa fa-exchange" title="同步菜单"></i></a>';
            if(result['action'] === 'create') {
                app = '<tr id="app"' + data['agentid'] + '">' +
                    '<td>' + data['id'] + '</td>' +
                    '<td class="text-center">' + data['agentid'] + '</td>' +
                    '<td>' + data['name'] + '</td>' +
                    '<td class="text-center"><img style="width: 16px; height: 16px;" src="' + data['square_logo_url'] + '"/></td>' +
                    '<td>' + data['description'] + '</td>' +
                    '<td class="text-center">' + data['created_at'] + '</td>' +
                    '<td class="text-center">' + data['updated_at'] + '</td>' +
                    '<td class="text-right">' + status + '</td>' +
                    '</tr>';
                var $na = $('#na');
                if(typeof $na !== 'undefined') {
                    $na.remove();
                }
                $('table tbody').append(app);
            } else {
                var $tr = $('#app' + data['agentid']);
                app = '<td>' + data['id'] + '</td>' +
                    '<td class="text-center">' + data['agentid'] + '</td>' +
                    '<td class="text-center">' + data['name'] + '</td>' +
                    '<td class="text-center"><img style="width: 16px; height: 16px;" src="' + data['square_logo_url'] + '"/></td>' +
                    '<td>' + data['description'] + '</td>' +
                    '<td class="text-center">' + data['created_at'] + '</td>' +
                    '<td class="text-center">' + data['updated_at'] + '</td>' +
                    '<td class="text-right">' + status + '</td>';
                $tr.html(app);
            }
            $('.overlay').hide();
            page.inform('操作结果', '已完成指定应用同步', page.success);
        },
        error: function() {}
    });
};
$form.parsley().on('form:validated', function () {
    if($('.parsley-error').length === 0) { sync(); }
}).on('form:submit', function () {
    return false;
});
$(document).off('click', '.fa-pencil');
$(document).on('click', '.fa-pencil', function() {
    var $this = $(this);
    var $tr = $this.parentsUntil('tbody').eq(2);
    var id = $tr.children('td').eq(0).html();
    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    page.getTabContent($activeTabPane, 'apps/edit/' + id);
    $(document).off('click', '.btn-primary');
});
$(document).on('click', '.fa-exchange', function() {
    var $this = $(this);
    var $tr = $this.parentsUntil('tbody').eq(2);
    var id = $tr.children('td').eq(0).html();
    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    page.getTabContent($activeTabPane, 'apps/menu/' + id);
    $(document).off('click', '.bg-purple');
});