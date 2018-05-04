//# sourceURL=index.js
var $form = $('#formApp'),
    $corpId = $('#corp_id'),
    id;
var sync = function () {
    $('.overlay').show();
    $.ajax({
        type: 'POST',
        url: '../apps/index',
        dataType: 'json',
        data: page.formData($form),
        success: function (result) {
            var data = result['app'];
            var app = '';
            var className = data['enabled'] ? 'text-green' : 'text-gray';
            var title = data['enabled'] ? '已启用' : '未启用';
            var status = '<i class="fa fa-circle ' + className + '" title="' + title + '"></i>\n\n&nbsp;&nbsp;\n' +
                '<a href="#"><i class="fa fa-pencil" title="修改"></i></a>\n\n&nbsp;&nbsp;\n' +
                '<a href="#"><i class="fa fa-exchange" title="同步菜单"></i></a>';
            if(result['action'] === 'create') {
                app =
                    '<tr id="app"' + data['agentid'] + '">' +
                        '<td>' + data['id'] + '</td>' +
                        '<td class="text-center">' + data['agentid'] + '</td>' +
                        '<td class="text-center">' + data['name'] + '</td>' +
                        '<td class="text-center"><img style="width: 16px; height: 16px;" src="' + data['square_logo_url'] + '"/></td>' +
                        '<td class="text-center">' + data['secret'] + '</td>' +
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
                    '<td class="text-center">' + data['secret'] + '</td>' +
                    '<td class="text-center">' + data['created_at'] + '</td>' +
                    '<td class="text-center">' + data['updated_at'] + '</td>' +
                    '<td class="text-right">' + status + '</td>';
                $tr.html(app);
            }
            $('.overlay').hide();
            page.inform('同步应用', '操作成功', page.success);
        },
        error: function(e) {
            page.errorHandler(e);
        }
    });
};

// 选择企业
$corpId.on('change', function() {
    $('.overlay').show();
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: page.siteRoot() + 'apps/index' + '?corpId=' + $corpId.val(),
        success: function (result) {
            $('.overlay').hide();
            $('table tbody').html(result['apps']);
        },
        error: function(e) {
            page.errorHandler(e);
        }
    });
});

// 同步应用
$form.parsley().on('form:validated', function () {
    if ($('.parsley-error').length === 0) {
        sync();
    }
}).on('form:submit', function () {
    return false;
});

// 编辑应用
$(document).off('click', '.fa-pencil').on('click', '.fa-pencil', function() {
    var $this = $(this),
        $tr = $this.parentsUntil('tbody').eq(2),
        id = $tr.children('td').eq(0).html(),
        $activeTabPane = $('#tab_' + page.getActiveTabId());

    page.getTabContent($activeTabPane, 'apps/edit/' + id);
    $(document).off('click', '.btn-primary');
});

// 删除应用
$(document).off('click', '.fa-remove').on('click', '.fa-remove', function() {
    id = $(this).parentsUntil('tbody').eq(2).children(0).first().html();
    $('#modal-dialog').modal({backdrop: true});
});

// 同步菜单
$(document).on('click', '.fa-exchange', function() {
    var $this = $(this);
    var $tr = $this.parentsUntil('tbody').eq(2);
    var id = $tr.children('td').eq(0).html();
    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    page.getTabContent($activeTabPane, 'apps/menu/' + id);
    $(document).off('click', '.bg-purple');
});

$('#confirm-delete').on('click', function () {
    $('.overlay').show();
    $.ajax({
        type: 'DELETE',
        dataType: 'json',
        url: page.siteRoot() + 'apps/delete/' + id,
        data: {_token: $('#csrf_token').attr('content')},
        success: function (result) {
            page.inform(result.title, result.message, page.success);
            page.getTabContent($('#tab_' + page.getActiveTabId()), 'apps/index');
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
});