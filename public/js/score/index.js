page.index('scores');
page.initSelect2();
var $score = $('#score');
var $send = $('#send');
var $send_main = $('#send_main');
var $examsName = $('#examsName');
var $token = $('#csrf_token');

$send.on('click', function() {
    $score.hide();
    $send_main.show();
});

$examsName.on('change',function(){
	var id = $(this).val();
	var formData = new FormData();
    formData.append('_token', $token.attr('content'));
    formData.append('exam', id);
    $.ajax({
        url: page.siteRoot() + "scores/send",
        type: 'POST',
        cache: false,
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            var html = '';
            console.log(result);
			
        }
    });
})
