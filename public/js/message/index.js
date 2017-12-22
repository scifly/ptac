
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

//$("input[type=file]").change("propertychange", function(e) {
//	var type = $(this).prev().val();
//	var filename = e.currentTarget.files[0].name;
//	var html = '<div class="showfile" style="color: #787878;margin-left: 5px;">';
//	switch(type)
//	{
//	case 'image':
//	//图片
//	  	html+='<i class="fa fa-file-image-o"></i>';
//	 	break;
//	case 'audio':
//	//音频
//	  	html+='<i class="fa fa-file-sound-o"></i>';
//	 	break;
//	case 'video':
//	//视频
//	  	html+='<i class="fa fa-file-movie-o"></i>';
//	 	break;
//	}
//	html+='<span style="margin-left: 5px;position:relative;cursor:pointer;">'+filename+'<input type="hidden" value="image" name="type" /><input id="file-'+type+'" type="file" name="uploadFile" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/></span>'+
//      	'<i class="fa fa-close" style="margin-left: 20px;cursor:pointer;"></i>'+
//      '</div>';
//  $('#message-content .tab-pane.active').html(html);
//  removefile();
//  $('#file-'+type).on("change", function(e){  
//      type = $(this).prev().val();
//		filename = e.currentTarget.files[0].name;
//		html = '<div class="showfile" style="color: #787878;margin-left: 5px;">';
//		switch(type)
//		{
//		case 'image':
//		//图片
//		  	html+='<i class="fa fa-file-image-o"></i>';
//		 	break;
//		case 'audio':
//		//音频
//		  	html+='<i class="fa fa-file-sound-o"></i>';
//		 	break;
//		case 'video':
//		//视频
//		  	html+='<i class="fa fa-file-movie-o"></i>';
//		 	break;
//		}
//		html+='<span style="margin-left: 5px;position:relative;cursor:pointer;">'+filename+'<input type="hidden" value="image" name="type" /><input id="file-'+type+'" type="file" name="uploadFile" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/></span>'+
//	        	'<i class="fa fa-close" style="margin-left: 20px;cursor:pointer;"></i>'+
//	        '</div>';
//	    $('#message-content .tab-pane.active').html(html);
//	    removefile();
//	    console.log(1);
//  });  
//})


function uploadfile(obj){
	var $this = $(obj);
	var type = $this.prev().val();
    var fileObj = $this[0].files[0];
	var filename = $this[0].files[0].name;
	var html = '<div class="showfile" style="color: #787878;margin-left: 5px;">';
	switch(type)
	{
	case 'image':
	//图片
	  	html+='<i class="fa fa-file-image-o"></i>';
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
    // html+='<span style="margin-left: 5px;position:relative;cursor:pointer;">'+filename+'<input type="hidden" value="image" name="type" /><input onchange="uploadfile(this)" id="file-'+type+'" type="file" name="uploadFile" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/></span>'+
    //     	'<i class="fa fa-close" style="margin-left: 20px;cursor:pointer;"></i>'+
    //     '</div>';
    // $('#message-content .tab-pane.active').html(html);
    removefile(type);
    page.inform("温馨提示", '正在上传中...', page.info);
    var formData = new FormData();
    formData.append('uploadFile', $('#file-image')[0].files[0]);
    // console.log($('#uploadForm')[0]);
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
	
	$('.tab-pane.active .fa-close').click(function(){
		$(this).parent().remove();
		var html = '<button id="add-'+type+'" class="btn btn-box-tool" type="button" style="margin-top: 3px;">'+
                        '<i class="fa fa-plus text-blue" style="position: relative;">&nbsp;添加图片'+
                        	'<input type="hidden" value="'+type+'" name="type" />'+
                        	'<input type="file" onchange="uploadfile(this)" id="file-'+type+'" name="uploadFile" accept="image/*" style="position: absolute;z-index: 1;opacity: 0;width: 100%;height: 100%;top: 0;left: 0;"/>'+
                        '</i>'+
                    '</button>';
        $('#message-content .tab-pane.active').html(html);
	});
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
		var content = $('#messageText').val();
		break;
	case 'imagetext':
	//图文
	  	console.log(2);
	 	break;
	case 'image':
	//图片
	  	console.log(3);
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
