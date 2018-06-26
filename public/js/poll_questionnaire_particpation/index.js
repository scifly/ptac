var dom = $('#panel');//获取dom
$(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': page.token()
        }
    });
    $("[name='pollQuestion']").select2({
        placeholder: "请选择",
        language: "zh-CN"
    });
    SelectInit();
    dom.hide();
    $("[name='Submit']").hide();
});
//初始化筛选框
function SelectInit() {
    $("[name='pollQuestion']").change(function(){
        $.ajax({
            url: '../public/pollQuestionnaireParticpation/show/'+$("[name='pollQuestion']").find("option:selected").val(),
            type: 'post',
            dataType: 'json',
            timeout: 1000,
            error: function(){
                alert('Error loading PHP document');
            },
            success: function(result)
            {
                dom.html('');//清空
                $.each(result, function(i, val) {
                    dom.append(GetHtml(val.subject_type,val.choices,val.subject,val.id));
                });
                dom.show();
                $("[name='Submit']").show();
            }
        });
    });
}

//subjectType 类型 0-单选 1-多选 2-填空
//Items选项题对象
//subjectName选项名称
//sid选项ID
function GetHtml(subjectType,Items,subjectName,sid)
{
    //radio,checkbox
    var Html='';
    var Item='';
    var type=subjectType=="0"?"radio":"checkbox";
    var ItemType=subjectType=="0"?"(单选)":"(多选)";
    var  name =subjectType=="0"?"rd_"+sid:"ck_"+sid;
    name+="[]";
    //先判断类型
    switch(subjectType) {
        //如果是填空
        case 2:
            $.each(Items, function (j, v) {
                Item += '<div class="input-group">';
                Item += '<span class="input-group-addon" id="sizing-addon2">' + v.choice + '</span>';
                if(v.answer=='')
                    Item += '<textarea  class="form-control" name="'+"text_"+sid+'[]" id="Options_' + v.id + '" placeholder="请填写内容"></textarea>';
                else
                    Item += '<textarea  class="form-control" name="'+"text_"+sid+'[]" id="Options_' + v.id + '"  placeholder="请填写内容">'+v.answer+'</textarea>';
                Item += '<input type="hidden" value="'+v.id+'" name="hd_'+sid+'[]">';
                Item += '</div>';
            });
            Html+='<div class="panel panel-default">' +
                '<div class="panel-heading">' +
                '<h3 class="panel-title">' + subjectName+ '(填空)</h3>' +
                '</div>' +
                '<div class="panel-body">' +
                Item +
                '</div>' +
                '</div>';
            break;
        //多选或者单选
        default:
            $.each(Items, function (j, v) {
                Item += '<div class="'+type+'">';
                Item += '<label>';
                if(v.answer=='')
                    Item += '<input type="'+type+'"name="' +name+ '" id="' + "Options_" + v.id + '" value="' + v.id + '" >';
                else
                    Item += '<input type="'+type+'"name="' +name+ '" id="' + "Options_" + v.id + '" value="' + v.id + '" checked="checked">';

                Item += v.choice;
                Item += '</label>';
                Item += '</div>';
            });
            Html+='<div class="panel panel-default">' +
            '<div class="panel-heading">' +
            '<h3 class="panel-title">' + subjectName +ItemType+'</h3>' +
                '</div>' +
                '<div class="panel-body">' +
                Item +
                '</div>' +
                '</div>';
            break;
    }
    return Html;
}