var options = [
    { className: 'text-center', targets: [0, 1, 2, 4, 5, 6, 7, 8, 9, 10]}
];
page.index('students', options);


var $import = $('#import');
var $export = $('#export');
var $importPupils = $('#import-pupils');
var $file = $('#confirm-import');
$import.on('click', function () {
    $importPupils.modal({backdrop: true});
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
                console.log(result);
            },
            error: function (result) {
                console.log(result);
            }
        });
    })
});
