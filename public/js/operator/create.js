$(crud.create('formOperator', 'operators'));
$(crud.mobile('formOperator', 0, 'POST', 'operators/store'));
// var $tbody = $("#mobileTable").find("tbody");
var n = 0;
var id = $('#id').val();
// var $formOperator = $('#formOperator');
var role = $('#role').val();
var $group = $('select[id="user[group_id]"]');
var $corps = $('#corps'), $schools = $('#schools');
var $corpId = $('#corp_id'), $schoolId = $('#school_id');
var $rootId = $('#root_id');
var uri = 'operators/create';
switch (role) {
    case '运营':
        $group.on('change', function() {
            var option = $('select[id="user[group_id]"] option:selected').text();
            switch (option) {
                case '运营':
                    $corps.hide(); $schools.hide();
                    dept.init(uri);
                    break;
                case '企业':
                    $corps.show(); $schools.hide();
                    dept.init(uri + '?rootId=' + $corpId.val());
                    break;
                case '学校':
                    $corps.hide(); $schools.show();
                    dept.init(uri + '?rootId=' + $schoolId.val());
                    break;
                default: break;
            }
        });
        $corpId.on('change', function() { dept.init(uri + '?rootId=' + $corpId.val()); });
        $schoolId.on('change', function() { dept.init(uri + '?rootId=' + $schoolId.val()); });
        $corps.hide();
        $schools.hide();
        dept.init(uri);
        break;
    case '企业':
        $group.on('change', function() {
            var option = $('select[id="user[group_id]"] option:selected').text();
            if (option === '企业') {
                $schools.hide();
            } else {
                $schools.show();
                dept.init(uri + '?rootId=' + $schoolId.val());
            }
        });
        $schoolId.on('change', function() { dept.init(uri + '?rootId=' + $schoolId.val()); });
        $schools.hide();
        dept.init(uri + '?rootId=' +  $rootId.val());
        break;
    case '学校':
        dept.init(uri + '?rootId=' + $rootId.val());
        break;
    default: break;
}
// dept.init('operators/create');