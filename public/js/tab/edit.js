var $groupId = $('#group_id'),
    $actionId = $('#action_id'),
    $menuIds = $('#menu_ids'),
    options = [{
    option: {
        templateResult: page.formatState,
        templateSelection: page.formatState,
    },
    id: 'icon_id'
}];
page.edit('formTab', 'tabs', options);
$.getMultiScripts([plugins.select2.js]).done(function () {
    $.getMultiScripts([plugins.select2.jscn]).done(function () {
        $groupId.select2();
        $actionId.select2();
        $menuIds.select2();
    });
});