FastClick.attach(document.body);
var studentName = $.parseJSON('{{$studentName}}'.replace(/&quot;/g, '"'));
//班级列表
$("#studentList").select({
    title: "选择学生",
    items: studentName
});

$("#studentList").on('change', function () {
    $('.loadmore').show();
    var student_id = $(this).attr('data-values');
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '../score/score_lists',
        data: {
            student_id: student_id,
            _token: $('#csrf_token').attr('content')
        },
        success: function ($data) {
            var html = '';
            if ($data.data.length !== 0) {
                var studentId = $data.studentId;
                for (var j = 0; j < $data.data.length; j++) {
                    var data = $data.data[j];
                    html += '<a class="weui-cell weui-cell_access" href="wechat/score/student_detail?examId=' + data.id + '&studentId=' + studentId + '">' +
                        '<div class="weui-cell__bd">' +
                        '<p>' + data.name + '</p>' +
                        '</div>' +
                        '<div class="weui-cell__ft time">' + data.start_date + '</div>' +
                        '</a>';
                }
                $('.weui-cells').html(html);
            } else {
                $('.weui-cells').html('暂无数据');
                $('.loadmore').hide();
            }
        }
    });
});

var start = 0;
$('.loadmore').click(function () {
    start++;

    loadmore(start);
});

function loadmore() {
    var student_id = $('input').attr('data-values');
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: '../score/score_lists',
        data: {start: start, student_id: student_id, _token: $('#csrf_token').attr('content')},
        success: function ($data) {
            var html = '';
            if ($data.data.length !== 0) {
                var studentId = $data.studentId;
                for (var i = 0; i < $data.data.length; i++) {
                    var score = $data.data[i];
                    html += '<a class="weui-cell weui-cell_access" href="student_detail?examId=' + score.id + '&studentId=' + studentId + '">' +
                        '<div class="weui-cell__bd">' +
                        '<p>' + score.name + '</p>' +
                        '</div>' +
                        '<div class="weui-cell__ft time">' + score.start_date + '</div>' +
                        '</a>';
                }
                $('.weui-cells').append(html);
            } else {

                $('.loadmore').hide();
            }
        }
    });

}

$('#searchInput').bind("input propertychange change", function (event) {
    var keywords = $(this).val();
    var student_id = $('input').attr('data-values');
    if (keywords === '') {
        $('.weui-cells').html('');
    } else {
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '../score/score_lists',
            data: {keywords: keywords, student_id: student_id, _token: $('#csrf_token').attr('content')},
            success: function ($data) {
                var html = '';
                if ($data.data.length !== 0) {
                    var studentId = $data.studentId;
                    for (var k = 0; k < $data.data.length; k++) {
                        var data = $data.data[k];
                        html += '<a class="weui-cell weui-cell_access" href="student_detail?examId=' + data.id + '&studentId=' + studentId + '">' +
                            '<div class="weui-cell__bd">' +
                            '<p>' + data.name + '</p>' +
                            '</div>' +
                            '<div class="weui-cell__ft time">' + data.start_date + '</div>' +
                            '</a>';
                    }
                    $('.weui-cells').html(html);
                } else {
                    $('.weui-cells').html('');
                    $('.loadmore').hide();
                }
            }
        });
    }
});

// educator
var className = $.parseJSON('{{$className}}'.replace(/&quot;/g,'"'));
var pageSize = '{{$pageSize}}';
//班级列表
$("#classlist").select({
    title: "选择班级",
    items: className
});

$("#classlist").on('change',function () {
    $('.loadmore').show();
    var class_id = $(this).attr('data-values');
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: 'score_lists',
        data: {class_id: class_id, _token: $('#csrf_token').attr('content')},
        success: function ($data) {
            var html = '';
            if($data.data.length !== 0)
            {
                for(var j=0 ; j< $data.data.length; j++)
                {
                    var data = $data.data[j];
                    html += '<a class="weui-cell weui-cell_access" href="wechat/score/detail?examId='+data.id+'&classId='+class_id+'">' +
                        '<div class="weui-cell__bd">' +
                        '<p>'+data.name +'</p>' +
                        '</div>' +
                        '<div class="weui-cell__ft time">'+ data.start_date+'</div>' +
                        '</a>';
                }
                $('.weui-cells').html(html);
            }else{
                $('.weui-cells').html('');
                $('.loadmore').hide();

            }
        }
    });
});

var start = 0;
$('.loadmore').click(function () {
    start++;

    loadmore(start);
});

function loadmore() {
    var class_id = $('input').attr('data-values');
    $.ajax({
        type: 'post',
        dataType: 'json',
        url: 'score_lists',
        data: {start: start, class_id: class_id, _token: $('#csrf_token').attr('content')},
        success: function ($data) {
            var html = '';
            if($data.data.length !== 0)
            {
                for(var i=0; i< $data.data.length;i++)
                {
                    var score = $data.data[i];
                    html += '<a class="weui-cell weui-cell_access" href="detail?examId='+score.id+'&classId='+class_id+'">' +
                        '<div class="weui-cell__bd">' +
                        '<p>'+score.name +'</p>' +
                        '</div>' +
                        '<div class="weui-cell__ft time">'+ score.start_date+'</div>' +
                        '</a>';
                }
                $('.weui-cells').append(html);
            }else{

                $('.loadmore').hide();
            }
        }
    });

}

$('#searchInput').bind("input propertychange change",function(event){
    var keywords = $(this).val();
    var class_id = $('input').attr('data-values');
    if(keywords === ''){
        $('.weui-cells').html('');
    }else{
        $.ajax({
            type: 'post',
            dataType: 'json',
            url: '../score/score_lists',
            data: {keywords: keywords, class_id:class_id, _token: $('#csrf_token').attr('content')},
            success: function ($data) {
                var html = '';
                if($data.data.length !== 0)
                {
                    for(var k=0 ; k< $data.data.length; k++)
                    {
                        var data = $data.data[k];
                        html += '<a class="weui-cell weui-cell_access" href="detail?examId='+data.id+'&classId='+class_id+'">' +
                            '<div class="weui-cell__bd">' +
                            '<p>'+data.name +'</p>' +
                            '</div>' +
                            '<div class="weui-cell__ft time">'+ data.start_date+'</div>' +
                            '</a>';
                    }
                    $('.weui-cells').html(html);
                }else{
                    $('.weui-cells').html('');
                    $('.loadmore').hide();

                }
            }
        });
    }
});
