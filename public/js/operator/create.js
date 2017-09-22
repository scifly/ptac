$(crud.create('formOperator', 'operators'));
$(crud.mobile('formOperator', 0, 'POST', 'operators/store'));
var $tbody = $("#mobileTable").find("tbody");
var n = 0;
var id = $('#id').val();
var $formOperator = $('#formOperator');

//部门
dept.init('educators/create');