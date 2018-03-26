page.create('formOperator', 'operators');

var $groupId = $('#group_id'),
    $corp = $('#corp'),
    $school = $('#school'),
    $corpId = $('#corp_id'),
    $schoolId = $('#school_id');

$groupId.on('change', function () {
    switch (parseInt($groupId.val())) {
        case 1:
            $corp.hide();
            $school.hide();
            break;
        case 2:
            if (typeof $corpId === 'undefined') {
                 getLists();
             }
             break;
         case 3:
             $corp.show();
             $school.show();
             if (typeof $schoolId === 'undefined') {
                getLists();
             }
             break;
         default:
             break;
     }
});

function getLists() {
    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: {groupId: $groupId.val()},
        url: page.siteRoot() + 'operators/create',
        success: function (result) {
            $corp.show();
            $school.hide();
            $corp.find('.input-group').append(result['corpList']);
            $('#corp_id').select2();
            if (result['schoolList'] !== '') {
                $school.find('.input-group').append(result['schoolList']);
                $('#school_id').select2();
            }
        },
        error: function (e) {
            page.errorHandler(e);
        }
    });
}

