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
var $score_send = $('#btn-send-message');

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
            var html2 = '<label><input  type="checkbox" class="minimal" value="-1">总分</label>';
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
    var squad = $('#squad_id').val();
    var subject = new Array();
    var project = new Array();
    $('#subject-list .checked').each(function(){
    	subject.push($(this).find('.minimal').val());
    });
    $('#project-list .checked').each(function(){
    	project.push($(this).find('.minimal').val());
    });
    var formData = new FormData();
    formData.append('_token', $token.attr('content'));
    formData.append('exam', exam);
    formData.append('squad', squad);
    formData.append('subject', subject);
    formData.append('project', project);
    $.ajax({
        url: page.siteRoot() + "scores/send",
        type: 'POST',
        cache: false,
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            var html = '';
            for(var i=0;i<result.length;i++){
            	var data = result[i];
            	html += '<tr>'+
            				'<td>'+
            					'<label>'+
									'<input type="checkbox" class="minimal">'+
								'</label>'+
							'</td>'+
							'<td>'+data.custodian+'</td>'+
							'<td>'+data.name+'</td>'+
							'<td class="mobile">'+data.mobile+'</td>'+
							'<td class="content">'+data.content+'</td>'+
            			'</tr>';
            	
            }
            $('#send-table tbody').html(html);
			page.initMinimalIcheck();
			table_checkAll();
        }
    });
    
});
function table_checkAll(){
	$('#table-checkAll').on('ifChecked', function(event){
		$('#send-table tbody').find('input.minimal').iCheck('check');
	}); 
	$('#table-checkAll').on('ifUnchecked', function(event){
		$('#send-table tbody').find('input.minimal').iCheck('uncheck');
	}); 
}

$score_send.on('click',function(){
	var data = [];
	$('#send-table tbody .checked').each(function(i,vo){
        var $this = $(vo).parent().parent().parent();
	    data[i] = {
    	    'mobile' : $this.find('.mobile').text(),
            'content' : $this.find('.content').text(),
        };

    });
    var formData = new FormData();
    formData.append('_token', $token.attr('content'));
    formData.append('data', JSON.stringify(data));
    console.log(data);
    $.ajax({
        url: page.siteRoot() + "scores/send_message",
        type: 'POST',
        cache: false,
        data: formData,
        processData: false,
        contentType: false,
        success: function (result) {
            console.log(result);

            if (result.original.statusCode!== 0) {
                page.inform("操作成功",result.message, page.success);
            }else {
                page.inform("操作失败",result.message, page.failure);
            }
        },
        error: function (result) {
            console.log(result);
            page.inform("操作失败",result.message, page.failure);

        }
    });

});