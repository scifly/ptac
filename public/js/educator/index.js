page.index('educators');

/** 导出excel 选择学校 */
var item = 'educator';
if (typeof custodian === 'undefined') {
    $.getMultiScripts(['js/custodian.relationship.js'], page.siteRoot())
        .done(function() { custodian.init(item); });
} else { custodian.init(item); }

/**
 * 批量导入教职工
 * @type {Mixed|jQuery|HTMLElement}
 */
var $import = $('#import');
var $importEducator = $('#import-educator');
var $file = $('#confirm-import');
$import.on('click', function () {
    $importEducator.modal({backdrop: true});
    $file.on('click', function () {
        var formData = new FormData();
        formData.append('file', $('#fileupload')[0].files[0]);
        formData.append('_token', $('#csrf_token').attr('content'));
        $.ajax({
            url: "../educators/import",
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (result) {
                console.log(result);
            },
            error: function (result) {
                console.log(result);
            }
        });
    })
});