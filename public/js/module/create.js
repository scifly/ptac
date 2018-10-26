//# sourceURL=create.js
var table = 'modules',
    $schoolId = $('#school_id');

page.loadCss('css/upload.css');
page.create('formModule', table);
$.getMultiScripts(['js/shared/upload.js']).done(
    function () {
        $.upload().init('create', table, '应用模块');
    }
);
$schoolId.on('change', function () {
    var $groupId = $('#group_id'),
        $next = $groupId.next(),
        $prev = $groupId.prev();

    $('.overlay').show();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: page.siteRoot() + 'modules/create',
        data: {
            _token: page.token(),
            school_id: $(this).val(),
        },
        success: function (result) {
            $next.remove();
            $groupId.remove();
            $prev.after(result);
            page.initSelect2();
            $('.overlay').hide();
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
});