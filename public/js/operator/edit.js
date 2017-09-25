// 初始化编辑相关事件
$(crud.edit('formOperator', 'operators'));

var size = $('#mobile-size').val();
var id = $('#id').val();

$(crud.mobile('formOperator',size, 'PUT', 'operators/update/'+id));
var $tbody2 = $("#classTable").find("tbody");
var $formOperator = $('#formOperator');

// 初始化部门树 相关事件
dept.init('educators/edit/' + id);