// 初始化编辑相关事件
page.edit('formOperator', 'operators');

var size = $('#mobile-size').val();
var id = $('#id').val();

if (typeof crud === 'undefined') {
    $.getMultiScripts(['js/admin.crud.js'], page.siteRoot())
        .done(function() { $(crud.mobile('formOperator',size, 'PUT', 'operators/update/'+id)); })
} else { $(crud.mobile('formOperator',size, 'PUT', 'operators/update/'+id)); }


var $tbody2 = $("#classTable").find("tbody");
var $formOperator = $('#formOperator');

// 初始化部门树 相关事件
if (typeof dept === 'undefined') {
    $.getMultiScripts(['js/department.tree.js'], page.siteRoot())
        .done(function() { dept.init('operators/edit/' + id); })
} else { dept.init('operators/edit/' + id); }