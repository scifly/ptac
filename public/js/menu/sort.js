page.unbindEvents();
$(document).off('click', '#sort');
var $tabList = $('.todo-list');

$tabList.sortable({
    placeholder: 'sort-highlight',
    handle: '.handle',
    forcePlaceholderSize: true,
    zIndex: 999999
}); // .todoList();

$(document).on('click', '#sort', function () {
    var $tabs = $('.text'),
        ranks = {},
        $cip = $('#cip');

    $cip.after('<link/>', {
        rel: 'stylesheet', type: 'type/css',
        href: page.siteRoot() + plugins.jqueryui.css
    });
    for (var i = 0; i < $tabs.length; i++) {
        ranks[$tabs[i].id] = i;
    }
    $('.overlay').show();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: page.siteRoot() + 'menus/sort/' + $('#menuId').val(),
        data: {
            data: ranks,
            _token: page.token()
        },
        success: function (result) {
            $('.overlay').hide();
            page.inform('保存卡片排序', result.message, page.success);
        }
    });
});

$('#record-list').on('click', function () {
    page.backToList('menus');
});