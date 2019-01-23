var choice = $('#choice').val();

$(document).on('click', '#show-choices', function () {
    var school = {
            text: '选择学校',
            onClick: function () { window.location = 'wechat/schools'; }
        },
        part = {
            text: '切换角色',
            onClick: function () { window.location = 'wechat/roles'; }
        },
        actions = [school, part];

    if (choice === 'schools') {
        actions = [school];
    } else if (choice === 'part') {
        actions = [part];
    }
    $.actions({actions: actions})
});