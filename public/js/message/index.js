
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
//  removefile(type);
    page.inform("温馨提示", '正在上传中...', page.info);
    var formData = new FormData();
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
			if(result.statusCode == 1){
				var html = '<form id="uploadForm" enctype="multipart/form-data">';
				
				switch(type)
				{
				case 'image':
				//图片
				  	html+='<div class="fileshow" style="display: inline-block;width: auto;position: relative;">'+
                            	'<img src="/ptac/'+result.data.path+'" style="height: 200px;">'+
                            	'<input type="hidden" value="'+result.data.media_id+'" name="media_id" />'+
                            	'<input type="hidden" value="'+type+'" name="type" />'+
                                '<input type="file" id="file-image" onchange="uploadfile(this)" name="uploadFile" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
                            	'<i class="fa fa-close file-del" style="position: absolute;top: 10px;right: 15px;font-size: 20px;z-index: 2;cursor: pointer;"></i>'+
                            '</div>'+
                            '</form>';
				 	break;
				case 'audio':
				//音频
				  	html+='<i class="fa fa-file-sound-o"></i>';
				 	break;
				case 'video':
				//视频
				  	html+='<i class="fa fa-file-movie-o"></i>';
				 	break;
				}
				$('#message-content .tab-pane.active').html(html);
				removefile(type);
			}else{
				alert('上传失败');
				var btntxt = $(this).parent().text();
				var html = '<form id="uploadForm" enctype="multipart/form-data">'+
		                       ' <button id="add-'+type+'" class="btn btn-box-tool" type="button" style="margin-top: 3px;position: relative;border: 0;">'+
		                            '<i class="fa fa-plus text-blue">'+
		                                '&nbsp;'+btntxt+''+
		                                    '<input type="hidden" value="'+type+'" name="type" />'+
		                                    '<input type="file" id="file-'+type+'" onchange="uploadfile(this)" name="uploadFile" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
		                            '</i>'+
		                        '</button>'+
		                    '</form>';
        		$('#message-content .tab-pane.active').html(html);
			}
			//result.data = '
			// filename
             //    :
             //    "QQ截图20171027140941.png"
            // id
             //    :
             //    18
            // media_id
             //    :
             //    "3AQYLJxtAkw86jYgwBXdTgIN5_VTmXG1nMionRYiOmQQ"
            // path
             //    :
             //    "storage/app/uploads/2017/12/22/5a3c81bb9ef29.png"
            // type
             //    :
             //    "png"
			// '
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
			break;
		case 'audio':
		var btntxt = '添加音频';
		 	break;
		case 'video':
		var btntxt = '添加视频';
		 	break;
		}
		var html = '<form id="uploadForm" enctype="multipart/form-data">'+
                       ' <button id="add-'+type+'" class="btn btn-box-tool" type="button" style="margin-top: 3px;position: relative;border: 0;">'+
                            '<i class="fa fa-plus text-blue">'+
                                '&nbsp;'+btntxt+''+
                                    '<input type="hidden" value="'+type+'" name="type" />'+
                                    '<input type="file" id="file-'+type+'" onchange="uploadfile(this)" name="uploadFile" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
                            '</i>'+
                        '</button>'+
                    '</form>';
        $('#message-content .tab-pane.active').html(html);
	});
}

$('#save-imagetext').click(function(){
	var title = $('#imagetext .imagetext-title').val();
	var description = $('#imagetext .imagetext-description').val();
	var picurl = $('#imagetext #fengmian').attr('src');
	if(title.length>128){
		alert('标题过长');
		return false;
	}
	if(title == ''){
		alert('请输入标题');
		return false;
	}
	if(description == ''){
		alert('请输入内容');
		return false;
	}
	if(description.length>512){
		alert('内容过长');
		return false;
	}
	if(picurl == '' || !picurl){
		alert('请上传封面');
		return false;
	}
	
	var content_source_url = $('#imagetext .imagetext-contenturl').val();
	var author = $('#imagetext .imagetext-author').val();
	
	var html = '<div class="show_imagetext" onclick="addimgtext()" style="padding: 10px;font-size: 13px;cursor: pointer;width: 272px;border: 1px solid #ddd;">'+
                	'<div class="imagetext_title" style="margin-bottom: 10px;font-size: 16px;line-height: 24px;overflow: hidden;text-overflow: ellipsis;-webkit-line-clamp: 2;">'+
                		''+title+''+
                	'</div>'+
                	'<div class="imagetext_cover" style="background-image: url('+picurl+');height: 125px;width:250px;background-repeat: no-repeat;background-size: cover;"></div>'+
                	'<div class="imagetext_description" style="margin-top: 12px;color: #787878;line-height: 20px;overflow: hidden;text-overflow: ellipsis;-webkit-line-clamp: 4;">'+description+'</div>'+
                	'<input type="hidden" value="'+content_source_url+'" class="show_imagetext_content_source_url">'+
                	'<input type="hidden" value="'+author+'" class="imagetext_author">'+
                '</div>';
	$('#content_imagetext').html(html);
	
	$message.show();
    $imageText.hide();	
});

$('#add-article-url').click(function(){
	$(this).next().show();
	$(this).hide();
});

function addimgtext(){
	$message.hide();
    $imageText.show();	
}

function upload_cover(obj){
	var $this = $(obj);
	var type = 'image';
//  removefile(type);
    page.inform("温馨提示", '正在上传中...', page.info);
    var formData = new FormData();
    formData.append('uploadFile', $('#file-cover')[0].files[0]);
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
				var html = '<form id="form-cover" enctype="multipart/form-data">'+
								'<div class="fileshow" style="display: inline-block;width: auto;position: relative;">'+
	                            	'<img id="fengmian" src="/ptac/'+result.data.path+'" style="height: 100px;">'+
	                            	'<input type="hidden" value="image" name="type" />'+
	                            	'<input type="hidden" value="'+result.data.media_id+'" name="media_id" />'+
	                                '<input type="file" id="file-cover" onchange="upload_cover(this)" name="input-cover" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
	                            '</div>'
							'</form>';
				
				$this.parents('#cover').html(html);
			}else{
				alert('上传失败');
			}
        }
    })
}



$send.on('click', function() {
    var appIds = $('#app_ids').val();
    var selectedDepartmentIds = $('#selectedDepartmentIds').val();
    var type = $('#message-content .tab-pane.active').attr('id');
    type = type.substring('8');
    switch(type)
	{
	case 'text':
	//文本
		var content = {
			"content":$('#messageText').val(),
		};
		break;
	case 'imagetext':
	//图文
		var title = $('#content_imagetext .imagetext_title').text();
		var text_content = $('#content_imagetext .imagetext_description').text();
		var picurl = $('#content_imagetext .imagetext_cover').css("backgroundImage").replace('url("','').replace('")','');
	  	var content_source_url = $('#content_imagetext .show_imagetext_content_source_url').val();
	  	var author = $('#content_imagetext .imagetext_author').val();
		var	articles = {
			"title" : title,
			"content" : text_content,
			"picurl" : picurl,
			"content_source_url" : content_source_url,
			"author" : author,
		};
		var content = {"articles":articles};
		
	 	break;
	case 'image':
	//图片
		var media_id = $('#content_image').find('input').eq(0).val();
		var filetype = $('#content_image').find('input').eq(1).val();
	  	var	content = {
			"type" : filetype,
			"media_id" : media_id,
		};
	 	break;
	case 'audio':
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
    console.log(content);
    // if (appIds.toString() === '') {
    //     alert('应用不能为空');
    //     return false
    // }
    // if (selectedDepartmentIds === '') {
    //     alert('对象不能为空');
    //     return false
    // }
    // if (content === '') {
    //     alert('内容不能为空');
    //     return false
    // }
//  $.ajax({
//      url: page.siteRoot() + "messages/store",
//      type: 'POST',
//      dataType: 'json',
//      data:  {
//          app_ids: appIds,
//          departIds: selectedDepartmentIds,
//          content: content,
//          _token: $('#csrf_token').attr('content')},
//
//      success: function (result) {
//          if (result.error !== 0) {
//              page.inform("操作成功",result.message, page.success);
//          }
//      },
//      error: function (result) {
//          console.log(result);
//          page.inform("操作失败",result.message, page.failure);
//
//      }
//  });
});
