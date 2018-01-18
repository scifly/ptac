//# sourceURL=index.js

var options = [
    { className: 'text-center', targets: [0, 1, 2, 4, 5, 6, 7, 8, 9, 10]}
];
page.index('students', options);


var $import = $('#import');
var $importPupils = $('#import-pupils');
var $file = $('#confirm-import');
$import.on('click', function () {
    $importPupils.modal({backdrop: true});
    $file.off('click');
    $file.on('click', function () {
        var formData = new FormData();
        formData.append('file', $('#fileupload')[0].files[0]);
        formData.append('_token', $('#csrf_token').attr('content'));
        $.ajax({
            url: "../students/import",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (result) {
                if (result.error !== 0) {
                    page.inform("操作失败",result.message, page.failure);
                }
            },
            error: function (result) {
                console.log(result);
                page.inform("操作失败",result.message, page.failure);

            }
        });
    })
});
/** 导出excel 选择班级 */
var item = 'students/';
var type = 'export';
if (typeof custodian === 'undefined') {
    $.getMultiScripts(['js/custodian.relationship.js'], page.siteRoot())
        .done(function() { custodian.init(item, type, ''); });
} else { custodian.init(item, type, ''); }

