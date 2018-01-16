page.index('scores');
page.initSelect2();
page.initMinimalIcheck();
page.loadCss(page.plugins.send_css.css);

var $score = $('#score');
var $send = $('#send');
var $send_main = $('#send_main');
var $exam_id = $('#exam_id');
var $token = $('#csrf_token');
var $close_send = $('#close-send');

$send.on('click', function() {
    $score.hide();
    $send_main.show();
});
$close_send.on('click', function() {
    $score.show();
    $send_main.hide();
});


$exam_id.on('change',function(){
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
        	console.log(result);
            var html = '';
            $.each(result, function (index, obj) {
                var data = obj;
            	html += '<option value="'+data.id+'">'+data.name+'</option>'
            });
            
            $('#squad_id').html(html);
			
        }
    });
})
