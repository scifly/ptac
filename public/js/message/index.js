
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
var $video = $('#upload_video');
var $addVideo = $('#add-video');
var $cancelVideo = $('#cancel-video');
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
$addVideo.on('click', function() {
    $message.hide();
    $video.show();
});
$cancelVideo.on('click',function () {
    $message.show();
    $video.hide();
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
				if(extension !== 'JPG' && extension !== 'PNG'){
					alert('请上传JPG或PNG格式的图片');
					return false;
				}
				break;
			case 'voice':// 上传语音文件仅支持AMR格式
				if(extension !== 'AMR'){
					alert('请上传AMR格式的文件');
					return false;
				}
	            break;
	        case 'video':// 上传视频文件仅支持MP4格式
				if(extension !== 'MP4'){
					alert('请上传MP4格式的视频');
					return false;
				}else{
					if($('#file-'+type)[0].files[0].size > 10485760){
						alert('请上传10MB以内的视频');
						return false;
					}
				}
	            break;    
	    }
		
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
			if(result.statusCode){
                page.inform("操作结果", result.message, page.success);
                var html = '<form id="uploadForm" enctype="multipart/form-data">';
				switch(type)
				{
				case 'image':
				//图片
				  	html+='<div class="fileshow" style="display: inline-block;width: auto;position: relative;">'+
                            	'<img src="../../'+result.data.path+'" style="height: 200px;">'+
                            	'<input id="image_media_id" type="hidden" value="'+result.data.media_id+'"/>'+
                            	'<input type="hidden" value="image" name="type" />'+
                                '<input type="file" id="file-image" onchange="uploadfile(this)" name="uploadFile" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
                            	'<i class="fa fa-close file-del" style="position: absolute;top: 10px;right: 15px;font-size: 20px;z-index: 2;cursor: pointer;"></i>'+
                            '</div>'+
                            '</form>';
                    $('#message-content .tab-pane.active').html(html);
                    removefile(type);
				 	break;
				case 'voice':
				//音频	
					html+='<div class="fileshow" style="color: #787878;margin-left: 5px;">'+
								'<i class="fa fa-file-sound-o">'+
					  				'<span style="margin-left: 5px;position:relative;cursor:pointer;">'+result.data.filename+''+
						  				'<input  id="voice_media_id"  type="hidden" value="'+result.data.media_id+'"/>'+
										'<input type="hidden" value="voice" name="type" />'+
										'<input id="file-voice" type="file" onchange="uploadfile(this)" name="uploadFile" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
					  				'</span>'+
							    '<i class="fa fa-close file-del" style="margin-left: 35px;cursor:pointer;"></i>'+
						    '</div>'+
						    '</form>';
				  	$('#message-content .tab-pane.active').html(html);	
				  	removefile(type);
				 	break;
				case 'video':
				//视频
				  	html+=	'<video src="../../'+result.data.path+'" controls="controls" style="height:180px">'+
							'</video>'+                             
							'<div class="btns">'+
							'<a class="changefile" style="position: relative;margin-left: 10px;">'+
								'更改'+
								'<input  id="video_media_id"  type="hidden" value="'+result.data.media_id+'"/>'+
								'<input type="hidden" value="video" name="type" />'+
								'<input type="file" id="file-video" onchange="uploadfile(this)" name="uploadFile" accept="video/mp4" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
							'</a>'+
							'<a class="delfile file-del" style="margin-left: 45px;display: inline-block;cursor: pointer;">删除</a>'+
							'</form>';
				 	$('#filevideo').html(html);
				 	removevideo();
				 	break;
				}
				
			}else{
                page.inform("操作结果", result.message, page.failure);
            }
        }
    })
}

function removefile(type){
	$('.tab-pane.active .file-del').click(function(){
        var btntxt = '';
        var fileaccept = '';
		switch(type)
		{
		case 'image':
			btntxt = '添加图片';
			fileaccept = 'image/*';
			break;
		case 'voice':
			btntxt = '添加音频';
			fileaccept = '';
				break;
		case 'video':
			btntxt = '添加视频';
			fileaccept = 'video/mp4';
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

$('#add-article-url').click(function(){
	$(this).next().slideToggle('fast');
	$(this).next().val('');
});

function upload_cover(obj){
	var $this = $(obj);
	var type = 'image';
	var extension = $('#file-cover')[0].files[0].name.split('.');
	extension = extension[extension.length-1];
	extension = extension.toUpperCase();
	if(extension !== 'JPG' && extension !== 'PNG'){
		alert('请上传JPG或PNG格式的图片');
		return false;
	}
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
                page.inform("操作结果", result.message, page.success);
                var html = '<form id="uploadForm" enctype="multipart/form-data">'+
	                			'<div class="show-cover" style="position: relative;height: 130px;width: 130px;background-image: url(../../'+result.data.path+');background-size: cover;">'+
			                		'<input type="hidden" value="'+result.data.media_id+'" name="media_id" />'+
			                		'<input type="hidden" value="image" name="type" />'+
			                        '<input type="file" id="file-cover" onchange="upload_cover(this)" name="input-cover" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
			                		'<i class="fa fa-close cover-del" style="position: absolute;top: 10px;right: 15px;font-size: 20px;z-index: 2;cursor: pointer;"></i>'+
			                	'</div>'+
                			'</form>';
				$('#cover').html(html);
				removecover();
			}else{
                page.inform("操作结果", result.message, page.failure);
            }
        }
    })
}

function removecover(){
	$('.cover-del').click(function(){
		var html = '<form id="form-cover" enctype="multipart/form-data">'+
	                	'<a href="#" style="position: relative;">'+
	                		'添加封面图'+
	                		'<input type="hidden" value="image" name="type" />'+
	                        '<input type="file" id="file-cover" onchange="upload_cover(this)" name="input-cover" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
	                	'</a>'+
	                	'&nbsp;&nbsp;<span class="text-gray">建议尺寸:1068*534</span>'+
	               '</form>';
        $('#cover').html(html);
	});
}

$('#save-imagetext').click(function(){
	var title = $('.imagetext-title').val();
	if(title === ''){
		alert('请输入标题');
		return false;
	}
	var content = $('.imagetext-content').val();
	if(content === ''){
		alert('请编辑内容');
		return false;
	}
	var picurl = $('#cover .show-cover').css("backgroundImage");
	if(!picurl){
		alert('请添加封面图');
		return false;
	}else{
		picurl = picurl.replace('url("','').replace('")','');
		var picid = $('#cover .show-cover input').eq(0).val();
	}
	
	var content_source_url = $('.imagetext-content_source_url').val();
	var author = $('.imagetext-author').val();
	var html = '<div class="show_imagetext" style="width: 270px;border:1px solid #E4E6E9;border-radius: 4px;padding: 10px;position: relative;cursor: pointer;">'+
                	'<div class="show_imagetext_title" style="font-size: 16px;line-height: 24px;overflow: hidden;text-overflow:ellipsis;-webkit-line-clamp:2;margin-bottom: 8px;">'+title+'</div>'+
                	'<div class="show_imagetext_pic" style="height: 125px;width: 250px;background-repeat: no-repeat;background-size:cover;background-image: url('+picurl+');"></div>'+
                	'<div class="show_imagetext_content" style="font-size: 12px;margin-top:12px;color:#787878;line-height: 20px;overflow: hidden;text-overflow:ellipsis;-webkit-line-clamp:4;">'+content+'</div>'+
                	'<input type="hidden" class="show_imagetext_pic_media_id" value="'+picid+'">'+
                	'<input type="hidden" class="show_imagetext_author" value="'+author+'">'+
                	'<input type="hidden" class="show_imagetext_content_source_url" value="'+content_source_url+'">'+
                '</div>';
    $('.tab-pane.active#content_mpnews').html(html);
    $message.show();
    $imageText.hide();
    show_imageText();
});

function show_imageText(){
	$('.show_imagetext').click(function(){
		$message.hide();
   		$imageText.show();
	})
}

function removevideo(){
	$('.video-del').click(function(){
		var html = '<form id="form-cover" enctype="multipart/form-data">'+
	                	'<a href="#" style="position: relative;">'+
	                		'添加视频'+
	                		'<input type="hidden" value="video" name="type" />'+
	                        '<input type="file" id="file-video" onchange="uploadfile(this)" name="input-video" accept="video/mp4" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
	                	'</a>'+
	                	'&nbsp;&nbsp;<span class="text-gray">(支持MP4)</span>'+
	               '</form>';
        $('#filevideo').html(html);
	});
}

$('#save-video').click(function(){
	var title = $('.video-title').val();
	if(title === ''){
		alert('请输入标题');
		return false;
	}
	var videourl = $('#filevideo video').attr("src");
	if(!videourl){
		alert('请上传视频');
		return false;
	}else{
		var videoid = $('#filevideo .changefile input').eq(0).val();
	}
	
	var description = $('.imagetext-description').val();
	var html = '<div class="show_video" style="width: 270px;border:1px solid #E4E6E9;border-radius: 4px;padding: 10px;position: relative;cursor: pointer;">'+
                	'<div class="show_video_title" style="font-size: 16px;line-height: 24px;overflow: hidden;text-overflow:ellipsis;-webkit-line-clamp:2;margin-bottom: 8px;">'+title+'</div>'+
                	'<video controls="controls" class="show_video_main" style="height: 125px;width: 250px;" src="'+videourl+'"></video>'+
                	'<div class="show_video_description" style="font-size: 12px;margin-top:12px;color:#787878;line-height: 20px;overflow: hidden;text-overflow:ellipsis;-webkit-line-clamp:4;">'+description+'</div>'+
                	'<input type="hidden" class="show_video_media_id" value="'+videoid+'">'+
                '</div>';
    $('.tab-pane.active#content_video').html(html);
    $message.show();
    $video.hide();
    $('#filevideo video')[0].pause();
    show_video();
});

function show_video(){
	$('.show_video').click(function(){
		$message.hide();
   		$video.show();
    	$('.tab-pane.active#content_video video')[0].pause();
	})
}

$send.on('click', function() {
    var appIds = $('#app_ids').val();
    var selectedDepartmentIds = $('#selectedDepartmentIds').val();
    var type = $('#message-content .tab-pane.active').attr('id');
    type = type.substring('8');
    // alert(type);
	var content = '';
    switch(type)
	{
		case 'text':
	//文本
		content = {text: $('#messageText').val()};
        break;
	case 'mpnews':
	//图文
	  	var articles = [{
	  		title : $('.show_imagetext_title').text(),
	  		content : $('.show_imagetext_content').html(),
	  		author : $('.show_imagetext_author').val(),
	  		content_source_url : $('.show_imagetext_content_source_url').val(),
	  		thumb_media_id : $('.show_imagetext_pic_media_id').val(),
	  	}];
	  	content = {articles : articles};
	 	break;
	case 'image':
		//图片
        content = {media_id: $('#image_media_id').val()};
        break;
	case 'voice':
	//音频
	  	content = {media_id: $('#voice_media_id').val()};
	 	break;
	case 'video':
	//视频
        var video = {
        	media_id: $('#video_media_id').val(),
        	title:$('.show_video_title').text(),
        	description:$('.show_video_description').text(),
        	};
        content = {video : video};

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
    if (content['text'] === '') {
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
         type: type,
         content: content,
         _token: $('#csrf_token').attr('content')},

     success: function (result) {
         if (result.error !== 0) {
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
