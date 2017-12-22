@extends('wechat.layouts.master')
@section('css')
    <link rel="stylesheet" href="{{ asset('/css/wechat/message_center/add.css') }}">
@endsection
@section('content')
		<div class="msg-send-wrap">
			<div class="scui-choose js-scui-choose-container3 js-scui-choose scui-form-group">
				<label class="scui-control-label mr4">作业发布对象</label> 
				<div id="homeWorkChoose" class="choose-results js-choose-results"> <!-- /* eslint-disable */  -->
					
				</div> 
				<span class="icons-choose choose-icon js-choose-icon">
					<a class="icon iconfont icon-add c-green open-popup" href="javascript:;" data-target="#choose"></a>
				</span>
			</div>
			
			<div class="mt5px msg-send-bg b-bottom hw-title">
				<div class="weui-cell">
					<div class="weui-cell__bd">
						<input type="text" placeholder="信息名称" maxlength="30" class="weui-input fs18 one-line title">
					</div>
				</div>
			</div>
			
			<div class="msg-send-conwrap msg-send-bg">
				<div contenteditable="true" id="emojiInput" class="wangEditor-mobile-txt"></div> 
			</div>
			
			<div class="msg-send-conicon msg-send-bg b-top">
				<ul class="weui-flex">
					<li class="weui-flex__item addImg">
						<i class="icon iconfont icon-tupian placeholder fs15 c-999"></i>
						 <input id="uploaderInput" class="weui-uploader__input js_file" type="file" accept="image/*" multiple="">
					</li>
				</ul>
			</div>
			
			<div class="weui-cell weui-cell_switch b-top weui-cells_form mt5px msg-send-bg">
				<div class="weui-cell__bd">定时发送</div> 
				<div class="weui-cell__ft">
					<input type="checkbox" title="开启评论" name="openCom" class="weui-switch"></div>
			</div>
			
			<div class="hw-time b-top" style="display: none;">
				<div class="weui-cell msg-send-bg">
					<div class="weui-cell__hd">
						<label for="" class="weui-label">发送日期</label>
					</div> 
					<div class="weui-cell__bd">
						<input readonly="readonly" type="text" name="" placeholder="请选择日期" class="weui-input ma_expect_date" data-toggle='datetime-picker'>
					</div>
				</div>
			</div>
			
			<div class="weui-flex mt5px">
				<div class="weui-flex__item">
					<div class="placeholder msg-send-btn" style="padding: 15px;">
						<a href="javascript:;" class="weui-btn weui-btn_primary">发布信息</a>
					</div>
				</div>
			</div>
			
		</div>
	</div>	
	
	
	<div id="choose" class='weui-popup__container'>
    	<div class="weui-popup__overlay"></div>
    	<div class="weui-popup__modal">
			<div class="choose-container js-scui-choose-layer">
				<div class="choose-container-fixed">
					<div class="choose-header js-choose-header">
						<div class="choose-header-result js-choose-header-result">
							
							
						</div>
						<div class="common-left-search">
			                <i class="icon iconfont icon-search3 search-logo icons2x-search"></i>
			                <input type="text" name="search" class="js-search-input" placeholder="搜索">
			            </div>
					</div>
					
					<div class="choose-breadcrumb js-choose-breadcrumb">
			            <ol class="breadcrumb js-choose-breadcrumb-ol">
			                <li data-id="0" class="js-choose-breadcrumb-li active"><a>全部</a></li>
			            </ol>
			        </div>
					
					<div class="choose-items js-choose-items">
						<div class="weui-cells weui-cells_checkbox" style="padding-bottom: 60px;">
					      	<label class="weui-cell weui-check__label" id="item-1" data-item="1" data-uid="1">
					        	<div class="weui-cell__hd">
					          		<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
					          	<i class="weui-icon-checked"></i>
					        	</div>
					        	<div class="weui-cell__bd">
					          		<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjjG5UGo8OES72Sw7U1CJYHXEkg1UlGkono5lDEiaZeBFlw/64" class="js-go-detail lazy" width="75" height="75">
					          		<span class="contacts-text">王小飞</span>
					        	</div>
					     	</label>
					     	<label class="weui-cell weui-check__label" id="item-2" data-item="2" data-uid="2">
					        	<div class="weui-cell__hd">
					          		<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
					          	<i class="weui-icon-checked"></i>
					        	</div>
					        	<div class="weui-cell__bd">
					          		<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjgYesvoOibygyRfgukxHDouo6ovRRicAKOphkKd0Licg3I2w/64" class="js-go-detail lazy" width="75" height="75">
					          		<span class="contacts-text">于娜</span>
					        	</div>
					     	</label>
					     	<label class="weui-cell weui-check__label" id="item-3" data-item="3" data-uid="3">
					        	<div class="weui-cell__hd">
					          		<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
					          	<i class="weui-icon-checked"></i>
					        	</div>
					        	<div class="weui-cell__bd">
					          		<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjjG5UGo8OES72Sw7U1CJYHXEkg1UlGkono5lDEiaZeBFlw/64" class="js-go-detail lazy" width="75" height="75">
					          		<span class="contacts-text">王小飞</span>
					        	</div>
					     	</label>
					     	<label class="weui-cell weui-check__label" id="item-4" data-item="4" data-uid="4">
					        	<div class="weui-cell__hd">
					          		<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
					          	<i class="weui-icon-checked"></i>
					        	</div>
					        	<div class="weui-cell__bd">
					          		<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjgYesvoOibygyRfgukxHDouo6ovRRicAKOphkKd0Licg3I2w/64" class="js-go-detail lazy" width="75" height="75">
					          		<span class="contacts-text">于娜</span>
					        	</div>
					     	</label>
					     	<label class="weui-cell weui-check__label" id="item-5" data-item="5" data-uid="5">
					        	<div class="weui-cell__hd">
					          		<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
					          	<i class="weui-icon-checked"></i>
					        	</div>
					        	<div class="weui-cell__bd">
					          		<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjjG5UGo8OES72Sw7U1CJYHXEkg1UlGkono5lDEiaZeBFlw/64" class="js-go-detail lazy" width="75" height="75">
					          		<span class="contacts-text">王小飞</span>
					        	</div>
					     	</label>
					     	<label class="weui-cell weui-check__label" id="item-6" data-item="6" data-uid="6">
					        	<div class="weui-cell__hd">
					          		<input type="checkbox" class="weui-check choose-item-btn" name="checkbox" >
					          	<i class="weui-icon-checked"></i>
					        	</div>
					        	<div class="weui-cell__bd">
					          		<img src="http://shp.qpic.cn/bizmp/UsXhSsnUkjjG5UGo8OES72Sw7U1CJYHXEkg1UlGkono5lDEiaZeBFlw/64" class="js-go-detail lazy" width="75" height="75">
					          		<span class="contacts-text">王小飞</span>
					        	</div>
					     	</label>
					     	
					      	
					
					    </div>
					    <div style="height: 40px;"></div>
					</div>
					
				</div>
				
				
				<div class="choose-footer js-choose-footer">
					
					<div class="weui-cells weui-cells_checkbox">
				      <label class="weui-cell weui-check__label">
				        <div class="weui-cell__hd">
				          <input type="checkbox" id="checkall" class="weui-check" name="checkedall" >
				          <i class="weui-icon-checked"></i>
				        </div>
				        <div class="weui-cell__bd">
				          <p>全选</p>
				        </div>
				      </label>
				      
				    </div>
					
			        <span class="scui-pull-right js-choose-sure def-color choose-enable" id="choose-btn-ok">确定<i class="expand"></i></span>
			        <span class="js-choose-num choose-num"><!--  eslint-disable -->
					已选0名用户

					</span>
			    </div>
			</div>
      	</div>
    </div>
@endsection
@section('script')
	<script>
  	$(".ma_expect_date").datetimePicker();
  	
  	$('.js-search-input').bind("input propertychange change",function(event){
  		var txt = $(this).val();
  		if(txt == ''){
  			$('.js-choose-items .weui-check__label').show();
  			$('.js-choose-breadcrumb-li').text('全部');
  		}else{
  			$('.js-choose-breadcrumb-li').text('搜索结果');
  			$('.js-choose-items .weui-check__label').hide();
  			$('.js-choose-items .weui-check__label').each(function(){
  				var uname = $(this).find('.contacts-text').text();
  				if(uname.indexOf(txt) >= 0){
  					$(this).show();
  				}
			});
  		}
  	})
  	
  	$('#choose-btn-ok').click(function(){
  		var html = $('.js-choose-header-result').html();
  		$('#homeWorkChoose').html(html);
  		$.closePopup();
  	});
  	
  	$(".choose-item-btn").change(function() { 
  		var $this = $(this).parents('.weui-check__label');
  		var num = $this.attr('data-item');
		if($(this).is(':checked')){
			var imgsrc = $this.find('img').attr('src');
			var uid = $this.attr('data-uid');
			var html = '<a class="choose-results-item js-choose-results-item" id="list-'+num+'" data-list="'+num+'" data-uid="'+uid+'">'+
							'<img src="'+imgsrc+'">'+
						'</a>';
			$('.js-choose-header-result').prepend(html);
			remove_choose_result();
			var total = $('.js-choose-header-result .js-choose-results-item').length;
			$('.js-choose-num').text('已选'+total+'名用户');
		}else{
			$('.js-choose-header-result').find('#list-'+num).remove();
		}
	});
	
	$('#checkall').change(function() { 
		if($(this).is(':checked')){
			$('.choose-item-btn').prop('checked',true);
			var html = '';
			$('.js-choose-items .weui-check__label').each(function(i,vo){
  				var num = $(vo).attr('data-item');
  				var uid = $(vo).attr('data-uid');
				var imgsrc = $(vo).find('img').attr('src');
				html += '<a class="choose-results-item js-choose-results-item" id="list-'+num+'" data-list="'+num+'" data-uid="'+uid+'">'+
							'<img src="'+imgsrc+'">'+
						'</a>';
			});
			$('.js-choose-header-result').html(html);
			remove_choose_result();
			var total = $('.js-choose-header-result .js-choose-results-item').length;
			$('.js-choose-num').text('已选'+total+'名用户');
		}else{
			$('.choose-item-btn').prop('checked',false);
			$('.js-choose-header-result').html('');
			var total = $('.js-choose-header-result .js-choose-results-item').length;
			$('.js-choose-num').text('已选'+total+'名用户');
		}
	});
	
	function remove_choose_result(){
		$('.js-choose-results-item').click(function(){
			var num = $(this).attr('data-list');
			$(this).remove();
			$('#item-'+num).find('.choose-item-btn').prop('checked',false);
			var total = $('.js-choose-header-result .js-choose-results-item').length;
			$('.js-choose-num').text('已选'+total+'名用户');
		});
 	}	
  	$(".weui-switch").change(function() { 
		if($(this).is(':checked')){
			$('.hw-time').slideToggle('fast');
		}else{
			$('.hw-time').slideToggle('fast');
		}
	});
  	
  	$(function () {  
	    // 允许上传的图片类型  
	    var allowTypes = ['image/jpg', 'image/jpeg', 'image/png', 'image/gif'];  
	    // 1024KB，也就是 1MB  
	    var maxSize = 1024 * 1024;  
	    // 图片最大宽度  
	    var maxWidth = 300;  
	    // 最大上传图片数量  
	    var maxCount = 6;  
	    $('.js_file').on('change', function (event) {  
	      	var files = event.target.files;  
      
	        // 如果没有选中文件，直接返回  
	        if (files.length === 0) {  
	          return;  
	        }  
        
	        for (var i = 0, len = files.length; i < len; i++) {  
	         	 var file = files[i];  
	         	 var reader = new FileReader();  
	          
	            // 如果类型不在允许的类型范围内  
	            if (allowTypes.indexOf(file.type) === -1) {  
	              $.weui.alert({text: '该类型不允许上传'});  
	              continue;  
	            }  
	            
	            if (file.size > maxSize) {  
	              $.weui.alert({text: '图片太大，不允许上传'});  
	              continue;  
	            }  
	            
	            if ($('.weui_uploader_file').length >= maxCount) {  
	              $.weui.alert({text: '最多只能上传' + maxCount + '张图片'});  
	              return;  
	            }  
            
            	reader.onload = function (e) {  
              		var img = new Image();  
              		img.onload = function () {  
	                    // 不要超出最大宽度  
	                    var w = Math.min(maxWidth, img.width);  
	                    // 高度按比例计算  
	                    var h = img.height * (w / img.width);  
	                    var canvas = document.createElement('canvas');  
	                    var ctx = canvas.getContext('2d');  
	                    // 设置 canvas 的宽度和高度  
	                    canvas.width = w;  
	                    canvas.height = h;  
	                    ctx.drawImage(img, 0, 0, w, h);  
	                    var base64 = canvas.toDataURL('image/png');  
	                    
	                    console.log(base64);
	                    var html = '<img src="'+base64+'">';
	                    $('#emojiInput').append(html);	
	                    // 然后假装在上传，可以post base64格式，也可以构造blob对象上传，也可以用微信JSSDK上传  
	                    
                	};  
                      
                      img.src = e.target.result;  
                };  
                reader.readAsDataURL(file);  
            }  
        });  
  	});  
	</script>
@endsection