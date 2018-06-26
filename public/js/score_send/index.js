//页面加载完毕
var table;
$(function() {

    //初始化选择器
    ApplySelect2();
    //隐藏发布列
    $("#panel").hide();
});


function ActionInit(dom,clr,level){
    dom.change(function(){
        $.ajax({
            url: '../public/scoreSend/'+clr+'/'+dom.find("option:selected").val(),
            type: 'post',
            dataType: 'json',
            timeout: 1000,
            error: function(){
                alert('Error loading PHP document');
            },
            success: function(result)
            {
                $("[name='"+level+"']").html('');
                $.each(result, function(i, val) {
                    $("[name='"+level+"']").append("<option value=\""+this.id+"\">"+this.name+"</option>");
                });
            }
        });
    });

}
//初始化联级架在事件
function ApplySelect2()
{
    //设置ajax全局header跨域访问
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': page.token()
        }
    });
    //初始化select2
    $("#school,[name='exam'],[name='grade'],[name='class']").select2({
        placeholder: "请选择",
        language: "zh-CN"
    });
    ActionInit($("[name='school']"),'getgrade','grade');
    ActionInit($("[name='grade']"),'getclass','class');
    ActionInit($("[name='class']"),'getexam','exam');
    initPreview();
    initTable();
}
//初始化类别事件
function initPreview(){

    $("[name='exam']").change(function(){
        $.ajax({
            url: '../public/scoreSend/getsubject/'+$("[name='exam']").find("option:selected").val(),
            type: 'post',
            dataType: 'json',
            timeout: 1000,
            error: function(){
                alert('Error loading PHP document');
            },
            success: function(result)
            {
                $("#exam_panel").html('');

                $.each(result, function(i, val) {
                    $("#exam_panel").append("<label class=\"checkbox-inline\"><input type=\"checkbox\" name=\"subject\" value=\""+this.id+"\">"+this.name+"</label>");
                });
                //显示项
                $("#exam_panel").append("<label class=\"checkbox-inline\"><input type=\"checkbox\" name=\"subject\" value=\"0\">总分</label>");
                $("#panel").show();
            }
        });
    });
    //点击预览事件
    $("#exampreview").click(
        function () {
            var subjectId=[];
            var itemId=[];
            //科目选择
            $("input[name=\"subject\"]:checked").each(function()
            {
                subjectId.push($(this).val());
            })
            //发布项目勾选
            $("input[name=\"project\"]:checked").each(function()
            {
                itemId.push($(this).val());
            })
            var URL='../public/scoreSend/preview';
            URL+="/"+$("[name='exam']").find("option:selected").val();
            URL+="/"+$("[name='class']").find("option:selected").val();
            URL+="/"+subjectId.toString();
            URL+="/"+itemId.toString();

            $.ajax({
                url: URL,
                type: 'post',
                dataType: 'json',
                timeout: 1000,
                error: function(jqXHR){
                    alert(jqXHR.statusText);
                },
                success: function(result)
                {
                    table.clear();
                    $.each(result, function(i, val) {
                        table.row.add([val.id,val.name,val.msg]).draw( false );
                    });
                }
            });
        }
    );

}


//初始化表单
function initTable() {
     table = $('#table').DataTable({
        'columnDefs': [
            {
                'targets': 0,
                'checkboxes': {
                    'selectRow': true
                }
            }
        ],
        'select': {
            'style': 'multi'
        },
        'order': [[1, 'asc']],
        language: { url: '../public/files/ch.json' }
    });
    // Handle form submission event
    $("#examsend").on('click', function(e){
        $.each(table.rows('.selected').data(), function(){
            console.log(this[0]);
        });
    });
}