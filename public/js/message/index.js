
page.initSelect2({
    templateResult: page.formatStateImg,
    templateSelection: page.formatStateImg
});
var $message = $('#message');
var $objects = $('#objects');
var $imageText = $('#imagetext');
var $addAttachment = $('#add-attachment');
var $saveAttachment = $('#save-attachment');
var $cancelAttachment = $('#cancel-attachment');
var $addImageText = $('#add-imagetext');
var $saveImageText = $('#save-imagetext');
var $cancelImageText = $('#cancel-imagetext');
var $send = $('#send');

// 附件管理
$addAttachment.on('click', function() {
    $message.hide();
    $objects.show();
});
$saveAttachment.on('click', function() {});
$cancelAttachment.on('click',function () {
    $message.show();
    $objects.hide();
});

// 图文管理
$addImageText.on('click', function() {
    $message.hide();
    $imageText.show();
});
$saveImageText.on('click', function() {

});
$cancelImageText.on('click',function () {
    $message.show();
    $imageText.hide();
});

//部门树以及联系人加载
if (typeof contacts === 'undefined') {
    $.getMultiScripts(['js/message/contacts.tree.js'], page.siteRoot())
        .done(function() { contacts.init('messages/index'); });
} else { contacts.init('messages/index'); }


function uploadfile(obj){
	
	
	var $this = $(obj);
	var type = $this.prev().val();
	var extension = $('#file-'+type)[0].files[0].name.split('.');
	extension = extension[extension.length-1];
	extension = extension.toUpperCase();
		switch (type) {
			case 'image':
				if(extension != 'JPG' && extension != 'PNG'){
					alert('请上传JPG或PNG格式的图片');
					return false;
				}
				break;
			case 'voice':// 上传语音文件仅支持AMR格式
				if(extension != 'AMR'){
					alert('请上传AMR格式的文件');
					return false;
				}
	            break;
	        case 'video':// 上传视频文件仅支持MP4格式
				if(extension != 'MP4'){
					alert('请上传MP4格式的视频');
					return false;
				}
	            break;    
	    }
		
    page.inform("温馨提示", '正在上传中...', page.info);
    var formData = new FormData();
//  switch (type) {
//		case 'image':
//			formData.append('uploadFile', $('#file-image')[0].files[0]);
//			break;
//		case 'voice':// 上传语音文件仅支持AMR格式
//          formData.append('uploadFile', $('#file-voice')[0].files[0]);
//          break;
//  }
	
	formData.append('uploadFile', $('#file-'+type)[0].files[0]);
	
    formData.append('_token', $('#csrf_token').attr('content'));
    formData.append('type', type);
    //请求接口
    $.ajax({
        url: page.siteRoot() + "messages/uploadFile",
        type: 'POST',
        cache: false,
        data: formData,
        processData: false,
        contentType: false,
		success: function (result) {
			if(result.statusCode){
                page.inform("操作结果", result.message, page.success);
                var html = '<form id="uploadForm" enctype="multipart/form-data">';
				switch(type)
				{
				case 'image':
				//图片
				  	html+='<div class="fileshow" style="display: inline-block;width: auto;position: relative;">'+
                            	'<img src="/ptac/'+result.data.path+'" style="height: 200px;">'+
                            	'<input type="hidden" value="'+result.data.media_id+'" name="media_id" />'+
                            	'<input type="hidden" value="image" name="type" />'+
                                '<input type="file" id="file-image" onchange="uploadfile(this)" name="uploadFile" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
                            	'<i class="fa fa-close file-del" style="position: absolute;top: 10px;right: 15px;font-size: 20px;z-index: 2;cursor: pointer;"></i>'+
                            '</div>'+
                            '</form>';
				 	break;
				case 'voice':
				//音频	
					html+='<div class="fileshow" style="color: #787878;margin-left: 5px;">'+
								'<i class="fa fa-file-sound-o">'+
					  				'<span style="margin-left: 5px;position:relative;cursor:pointer;">'+result.data.filename+''+
						  				'<input type="hidden" value="'+result.data.media_id+'" name="media_id" />'+
										'<input type="hidden" value="voice" name="type" />'+
										'<input id="file-voice" type="file" onchange="uploadfile(this)" name="uploadFile" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
					  				'</span>'+
							    '<i class="fa fa-close file-del" style="margin-left: 35px;cursor:pointer;"></i>'+
						    '</div>'+
						    '</form>';
				  			
				 	break;
				case 'video':
				//视频
				  	html+='<i class="fa fa-file-movie-o"></i>';
				 	break;
				}
				$('#message-content .tab-pane.active').html(html);
				removefile(type);
			}else{
                page.inform("操作结果", result.message, page.failure);

            }
        }
    })
}

function removefile(type){
	$('.tab-pane.active .file-del').click(function(){
		$(this).parent().remove();
		switch(type)
		{
		case 'image':
		var btntxt = '添加图片';
		var fileaccept = 'image/*';
			break;
		case 'voice':
		var btntxt = '添加音频';
		var fileaccept = '';
		 	break;
		case 'video':
		var btntxt = '添加视频';
		var fileaccept = 'video/mp4';
		 	break;
		}
		var html = '<form id="uploadForm" enctype="multipart/form-data">'+
                       ' <button id="add-'+type+'" class="btn btn-box-tool" type="button" style="margin-top: 3px;position: relative;border: 0;">'+
                            '<i class="fa fa-plus text-blue">'+
                                '&nbsp;'+btntxt+''+
                                    '<input type="hidden" value="'+type+'" name="type" />'+
                                    '<input type="file" id="file-'+type+'" onchange="uploadfile(this)" name="uploadFile" accept="'+fileaccept+'" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
                            '</i>'+
                        '</button>'+
                    '</form>';
        $('#message-content .tab-pane.active').html(html);
	});
}

$send.on('click', function() {
    var appIds = $('#app_ids').val();
    var selectedDepartmentIds = $('#selectedDepartmentIds').val();
    var type = $('#message-content .tab-pane.active').attr('id');
    type = type.substring('8');
    // alert(type);
	var content = "";
    switch(type)
	{
		case 'text':
	//文本
		content = '{ "text": "' +$('#messageText').val()+ '"}' ;

        break;
	case 'imagetext':
	//图文
	  	console.log(2);
	 	break;
	case 'image':
	//图片
	  	console.log(3);
	 	break;
	case 'voice':
	//音频
	  	console.log(4);
	 	break;
	case 'video':
	//视频
	  	console.log(5);
	 	break;
	case 'sms':
	//短信
	  	console.log(6);
	 	break;
	}
    
    if (appIds.toString() === '') {
        alert('应用不能为空');
        return false
    }
    if (selectedDepartmentIds === '') {
        alert('对象不能为空');
        return false
    }
    if (content === '') {
        alert('内容不能为空');
        return false
    }
 $.ajax({
     url: page.siteRoot() + "messages/store",
     type: 'POST',
     dataType: 'json',
     data:  {
         app_ids: appIds,
         departIds: selectedDepartmentIds,
         content: content,
         _token: $('#csrf_token').attr('content')},

     success: function (result) {
         if (result.error !== 0) {
             page.inform("操作成功",result.message, page.success);
         }
     },
     error: function (result) {
         console.log(result);
         page.inform("操作失败",result.message, page.failure);

     }
 });
});
