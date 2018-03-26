//# sourceURL=create.js
page.create('formOperator', 'operators');

var $groupId = $('#group_id'),
    $corp = $('#corp'),
    $school = $('#school');

$groupId.on('change', function () {
    var $corpId = $('#corp_id'),
        $schoolId = $('#school_id');
    switch (parseInt($groupId.val())) {
        case 1:
            $corp.slideUp();
            $school.slideUp();
            break;
        case 2:
            if ($corpId.length === 0) {
                getLists();
            }
            $corp.slideDown();
            $school.slideUp();
            break;
        case 3:
            if ($schoolId.length === 0) {
                getLists();
            }
            $corp.slideDown();
            $school.slideDown();
            break;
        default:
            break;
    }
});

function getLists() {
    return $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
            _token: $('#csrf_token').attr('content'),
            groupId: $groupId.val()
        },
        url: page.siteRoot() + 'operators/create',
        success: function (result) {
            var $corpId = $('#corp_id'),
                $schoolId = $('#school_id');
            if ($corpId.length === 0) {
                $corp.find('.input-group').append(result['corpList']);
                $corpId.select2();
            }
            if (result['schoolList'] !== '') {
                $school.find('.input-group').append(result['schoolList']);
                $schoolId.select2();
            }
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
}

