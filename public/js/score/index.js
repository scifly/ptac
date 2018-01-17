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
var $browse = $('#btn-browse');

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
            var html1 = '';
            $.each(result.classes, function (index, obj) {
                var data = obj;
            	html1 += '<option value="'+data.id+'">'+data.name+'</option>'
            });
            $('#squad_id').html(html1);
            page.initSelect2();
            var html2 = '';
            $.each(result.subjects, function (index, obj) {
                var datacon = obj;
            	html2 +='<label>'+ 
			   				'<input type="checkbox" class="minimal" value="'+datacon.id+'">'+datacon.name+''+
		   				'</label>';
            });
            $('#subject-list').html(html2);
			page.initMinimalIcheck();
        }
    });
});

$browse.on('click', function() {
    var exam = $('#exam_id').val();
    var squad_id = $('#squad_id').val();
    var subject_ids = new Array();
    
    $('#subject-list .checked').each(function(){
    	subject_ids.push($(this).find('.minimal').val());
    });
    $('#project-list .checked').each(function(){
    	subject_ids.push($(this).find('.minimal').val());
    });
    
    console.log(subject_ids);
    
});
