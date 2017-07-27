getSubjectBySchoolId();

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
                    for(var i=0;i<$array.length;i++){
                        $subjectSelect.append("<option value='"+$array[i].id+"'>"+$array[i].name+"</option>");
                    }
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