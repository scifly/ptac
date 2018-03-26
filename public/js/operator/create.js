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
            getLists();
            $corp.slideDown();
            $school.slideUp();
            break;
        case 3:
            $.when(getLists()).then(function () {
                getLists('corp_id');
            });
            $corp.slideDown();
            $school.slideDown();
            break;
        default:
            break;
    }
});

$(document).on('change', '#corp_id', function () {
    if ($('#school').is(':visible')) {
        getLists('corp_id')
    }
});

function getLists(field) {
    var value = 0;
    if (typeof field === 'undefined') {
        field = 'group_id';
        value = $('#' + field).val();
    } else {
        value = $('#' + field).val();
    }
    return $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {
            _token: $('#csrf_token').attr('content'),
            field: field,
            value: value
        },
        url: page.siteRoot() + 'operators/create',
        success: function (result) {
            if (field === 'group_id') {
                $corp.find('.input-group').append(result['corpList']);
                $('#corp_id').select2();
                $school.find('.input-group').append(result['schoolList']);
                $('#school_id').select2();
            } else {
                var $schoolId = $('#school_id'),
                    $prev = $schoolId.prev(),
                    $next = $schoolId.next();
                $next.remove();
                $schoolId.remove();
                $prev.after(result['schoolList']);
                page.initSelect2();
            }
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
}

