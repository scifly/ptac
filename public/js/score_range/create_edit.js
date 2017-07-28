getSubjectBySchoolId();
if($('#subject_select_ids').val()){
    var $array_sub_ids = $('#subject_select_ids').val().split(",");
}
$('#school_id').change(function(){
    getSubjectBySchoolId();
});

function getSubjectBySchoolId() {
    var school_id = $('#school_id').val();
    $('#subject_ids').empty();
    var $subjectSelect = $('#subject_ids');
    $.ajax({
        type: 'GET',
        url: '/ptac/public/subject/query/' + school_id,
        success: function(result) {
            if (result.statusCode === 200) {
                $subjectSelect.removeAttr("disabled");
                if(result.subjects.length == 0){
                    $subjectSelect.attr("disabled","disabled");
                    $.gritter.add({
                        title: "注意！",
                        text: "该校暂未设置科目",
                        image: '/img/failure.jpg'
                    });
                }else{
                    $array = eval(result.subjects);
                    $.each($array,function () {
                        $subjectSelect.append("<option value='"+this.id+"'>"+this.name+"</option>");
                    })
                    $("#subject_ids option").each(function() {
                        if($.inArray($(this).val(),$array_sub_ids) !== -1){
                            $(this).attr('selected','selected');
                        }
                    })
                }
            }
            return false;
        },
        error: function(e) {
            var obj = JSON.parse(e.responseText);
            for (var key in obj) {
                if (obj.hasOwnProperty(key)) {
                    $.gritter.add({
                        title: "科目获取失败",
                        text: obj[key],
                        image: '/img/failure.jpg'
                    });
                }
            }
        }
    });
}