//页面加载完毕
$(function() {

    //初始化选择器
    ApplySelect2();
    //学校变更获取年级信息
    $("[name='school']").change(function(){
        $.ajax({
            url: '../public/scoreSend/getgrade/'+$("[name='school']").find("option:selected").val(),
            type: 'Get',
            dataType: 'json',
            timeout: 1000,
            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            error: function(){
                alert('Error loading PHP document');
            },
            success: function(result)
            {
                $("[name='grade']").html('');
                $("[name='grade']").html(' <option value="0"><--请选择年级--></option>');
                $.each(result, function(i, val) {
                    $("[name='grade']").append("<option value=\""+this.id+"\">"+this.name+"</option>");
                });
            }
        });
    });


    //根据年级id获取班级
    $("[name='grade']").change(function(){
        $.ajax({
            url: '../public/scoreSend/getclass/'+$("[name='grade']").find("option:selected").val(),
            type: 'Get',
            dataType: 'json',
            timeout: 1000,
            headers:{'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            error: function(){
                alert('Error loading PHP document');
            },
            success: function(result)
            {
                $("[name='class']").html('');
                $("[name='class']").html('<option value="0"><--请选择班级--></option>');
                $.each(result, function(i, val) {
                    $("[name='class']").append("<option value=\""+this.id+"\">"+this.name+"</option>");
                });
            }
        });
    });
});

function ApplySelect2()
{
    //学校
    $("#school").select2({
        placeholder: "请选择",
        language: "zh-CN"
    });
    //年级
    $("[name='grade']").select2({
        placeholder: "请选择",
        language: "zh-CN"
    });
    //班级
    $("[name='class']").select2({
        placeholder: "请选择",
        language: "zh-CN"
    });

}