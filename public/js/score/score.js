//# sourceURL=score.js
(function ($) {
    $.score = function (options) {
        var score = {
            options: $.extend(
                {}, options
            ),
            /** helper functions */
            token: function () {
                return $('#csrf_token').attr('content');
            },
            classList: function (action) {
                var url = '../scores/' + action + '/' + $('#' + action + '_exam_id').val(),
                    data = { _token: score.token() },
                    $classId = $('#' + action + '_class_id');

                if (action === 'import') {
                    $.extend(data, {classId: $classId.val()});
                }
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: data,
                    url: url,
                    success: function (result) {
                        var $classNext = $classId.next(),
                            $classPrev = $classId.prev();

                        $classNext.remove();
                        $classId.remove();
                        $classPrev.after(result['html']);

                        page.initSelect2();
                    },
                    error: function (e) {
                        page.errorHandler(e);
                    }
                });
            },
            list: function (type, id) {
                $.ajax({
                    type: 'GET',
                    data: { _token: score.token() },
                    url: '../scores/stat/' + type + '/' + id,
                    success: function (result) {
                        var $typeId = $('#' + type + '_id'),
                            $typeNext = $typeId.next(),
                            $typePrev = $typeId.prev();

                        $typeNext.remove();
                        $typeId.remove();
                        $typePrev.after(result.original['html']);
                        page.initSelect2();
                        if (type === 'class') {
                            score.list('student', $('#class_id').val());
                        }
                    },
                    error: function (e) {
                        page.errorHandler(e);
                    }
                });
            },
            classData: function () {
                var title = $('#sumscore').prev().text(),
                    $data = $('#sumscore tbody tr td'),
                    arrayTime = [],
                    legendData = [],
                    sum = $data.eq(1).text();

                $('#modal-class').modal({backdrop: true});
                $data.each(function (i, vo) {
                    if (i === 0 || i === 1) {
                    } else {
                        var val = $(vo).text(),
                            percent = (Math.round(val / sum * 10000) / 100.00).toFixed(2) + '%',
                            name = $('#sumscore thead tr th').eq(i).text() + '(' + percent + ')';

                        legendData.push(name);
                        arrayTime.push({ 'name': name, 'value': val });
                    }
                });
                score.pieChart(arrayTime, legendData, title);
            },
            studentData: function () {
                var $classRank = $('#class-rank'),
                    $gradeRank = $('#grade-rank'),
                    subjectQty = parseInt($('#subject-quantity').val()) + 1, // 科目数量（包括总分）
                    $data = $('#scores tbody tr'),
                    subjects = [],
                    classRanks = [],
                    gradeRanks = [],
                    exams = [];

                $('#modal-student').modal({backdrop: true});
                for (var i = 0; i < $data.length; i++) {
                    exams.push($data.eq(i).find('.exam-name').text());
                }
                for (var p = 0; p < subjectQty; p++) {
                    classRanks[p] = [];
                    gradeRanks[p] = [];
                    for (var q = 0; q < $data.length; q++) {
                        var $datacon = $data.eq(q),
                            examName = $datacon.find('.exam-name').text(),
                            classRank = $datacon.find('.class-rank').eq(p).text(),
                            gradeRank = $datacon.find('.grade-rank').eq(p).text();

                        classRanks[p].push({ 'name': examName, 'value': classRank });
                        gradeRanks[p].push({ 'name': examName, 'value': gradeRank });
                    }
                    subjects.push($('#scores thead tr .subject-name').eq(p).text());

                    // 班级排名图表
                    $classRank.append('<div class="linetableitem" id="class-' + p + '"></div>');
                    var classtmp = 0;
                    for (var k = 0; k < classRanks[p].length; k++) {
                        if (classRanks[p][k].value !== '——') {
                            classtmp = 1; break;
                        }
                    }
                    if (classtmp === 1) {
                        score.chart(classRanks[p], subjects[p], exams, 'class', p);
                    } else {
                        $('#class-' + [p]).remove();
                    }

                    // 年级排名图表
                    $gradeRank.append('<div class="linetableitem" id="grade-' + p + '"></div>');
                    var gradetmp = 0;
                    for (var j = 0; j < gradeRanks[p].length; j++) {
                        if (gradeRanks[p][j].value !== '——') {
                            gradetmp = 1; break;
                        }
                    }
                    if (gradetmp === 1) {
                        score.chart(gradeRanks[p], subjects[p], exams, 'grade', p);
                    } else {
                        $('#grade-' + [p]).remove();
                    }
                }
            },
            pieChart: function (arrayTime, legendData, title) {
                var myChart = echarts.init($('.table-pie')[0]),
                    option = {
                        title: {
                            text: title,
                            x: 'center',
                            top: 0
                        },
                        tooltip: {
                            trigger: 'item',
                            formatter: "{a} <br/>{b} : {c} ({d}%)"
                        },
                        legend: {
                            show: true,
                            bottom: 10,
                            left: 'center',
                            data: legendData,
                        },
                        series: [
                            {
                                name: '成绩总分',
                                type: 'pie',
                                radius: '55%',
                                center: ['50%', '50%'],
                                data: arrayTime,
                                itemStyle: {
                                    emphasis: {
                                        shadowBlur: 10,
                                        shadowOffsetX: 0,
                                        shadowColor: 'rgba(0, 0, 0, 0.5)'
                                    }
                                }
                            }
                        ]
                    };

                myChart.setOption(option);
            },
            chart: function (data, subject, exam, type, i) {
                var myChart = echarts.init($('#' + type + '-' + i)[0]),
                    option = {
                        title: {
                            x: 'center',
                            text: subject,
                            textStyle: { fontWeight: '100', fontSize: '16' },
                            top: 15
                        },
                        grid: { bottom: '80' },
                        tooltip: { trigger: 'axis' },
                        xAxis: {
                            type: 'category',
                            data: exam,
                            boundaryGap: false,
                        },
                        yAxis: {
                            type: 'value',
                            axisLabel: { formatter: '{value}' },
                        },
                        series: [{
                            name: '排名',
                            type: 'line',
                            data: data,
                            connectNulls: true,
                        }]
                    };

                myChart.setOption(option);
            },
            /** public functions */
            index: function () {
                page.index('scores', [
                    {className: 'text-center', targets: [1, 2, 3, 4, 5, 6, 7, 8, 10, 11]},
                    {className: 'text-right', targets: [9]}
                ]);
                page.loadCss('css/score/send.css');
                page.initMinimalIcheck();
                page.initSelect2();

                // 成绩发送
                score.onSendClick();
                score.onPreviewClick();
                score.onSelectAllChecked();
                score.onSelectAllUnchecked();
                score.onSendExamIdChange();
                score.onSendScoresClick();
                // 排名统计
                score.onRankClick();
                score.onRankScoresClick();
                // 批量导入
                score.onImportClick();
                score.onImportExamIdChange();
                score.onImportClassIdChange();
                score.onImportScoresClick();
                // 批量导出
                score.onExportClick();
                score.onExportExamIdChange();
                score.onExportScoresClick();
                // 统计分析
                score.onStatClick();
            },
            create: function () {
                page.create('formScore', 'scores');
                var $examId = $('#exam_id');
                $examId.on('change', function () {
                    $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        data: { _token: score.token() },
                        url: 'create/' + $examId.val(),
                        success: function (result) {
                            score.getSsList(result);
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            edit: function () {
                page.edit('formScore', 'scores');
                var $examId = $('#exam_id');
                $examId.on('change', function () {
                    $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        data: { _token: score.token() },
                        url: 'create/' + $('#id').val() + '/' + $examId.val(),
                        success: function (result) {
                            score.getSsList(result);
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            stat: function () {
                page.initSelect2();
                page.initMinimalIcheck();
                page.loadCss('css/score/stat.css');
                page.initBackBtn('scores');
                $.getMultiScripts([plugins.echarts_common.js]).done(function () {
                    score.onExamIdChange();
                    score.onClassIdChange();
                    score.onAnalyzeClick();
                });
            },
            /** 成绩发送 */
            onSendClick: function () {
                $('#send').on('click', function () {
                    $('#modal-send').modal({backdrop: true});
                });
            },
            onPreviewClick: function () {
                $('#preview').on('click', function () {
                    var examId = $('#send_exam_id').val(),
                        classId = $('#send_class_id').val(),
                        subjectIds = [];

                    $('#subject-list .checked').each(function(){
                        subjectIds.push($(this).find('.minimal').val());
                    });
                    $('.overlay').show();
                    $.ajax({
                        url: page.siteRoot() + "scores/send",
                        type: 'POST',
                        cache: false,
                        data: {
                            _token: score.token(),
                            examId: examId,
                            classId: classId,
                            subjectIds: subjectIds,
                            items: $('#items').val()
                        },
                        success: function (result) {
                            var html = '';
                            $('.overlay').hide();
                            for (var i = 0; i < result.length; i++) {
                                var message = result[i];
                                html +=
                                    '<tr>'+
                                        '<td><label><input type="checkbox" class="minimal"></label></td>'+
                                        '<td>' + message['custodian'] + '</td>' +
                                        '<td>' + message['name'] + '</td>' +
                                        '<td class="mobile">' + message['mobile'] + '</td>'+
                                        '<td class="content">' + message['content'] + '</td>'+
                                    '</tr>';
                            }
                            $('#send-table tbody').html(html);
                            page.initMinimalIcheck();
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            onSelectAllChecked: function () {
                $('#select-all').on('ifChecked', function () {
                    $('#send-table tbody').find('input.minimal').iCheck('check');
                });
            },
            onSelectAllUnchecked: function () {
                $('#select-all').on('ifUnchecked', function () {
                    $('#send-table tbody').find('input.minimal').iCheck('uncheck');
                });
            },
            onSendExamIdChange: function () {
                var $examId = $('#send_exam_id');
                $examId.on('change',function(){
                    $.ajax({
                        url: page.siteRoot() + "scores/send",
                        type: 'POST',
                        cache: false,
                        data: {
                            _token: score.token(),
                            examId: $examId.val()
                        },
                        success: function (result) {
                            var html = '<select class="form-control select2" id="send_class_id" name="send_class_id">',
                                $sendClassId = $('#send_class_id'),
                                $sciNext = $sendClassId.next(),
                                $sciPrev = $sendClassId.prev();

                            // 班级下拉列表
                            $.each(result['classes'], function (index, squad) {
                                html += '<option value="'+ squad['id'] + '">' + squad['name'] +'</option>'
                            });
                            html += '</select>';
                            $sciNext.remove();
                            $sendClassId.remove();
                            $sciPrev.after(html);
                            page.initSelect2();

                            // 发布科目列表
                            html = '<input type="checkbox" name="content" class="minimal" value="-1"> 总分&nbsp;';
                            $.each(result['subjects'], function (index, subject) {
                                html += '<input type="checkbox" ' +
                                    'name="content" ' +
                                    'class="minimal" ' +
                                    'value="' + subject['id'] +
                                    '"> ' + subject['name'] + '&nbsp;';
                            });
                            $('#subject-list').html(html);
                            page.initMinimalIcheck();
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            onSendScoresClick: function () {
                $('#send-scores').on('click', function () {
                    if ($('#send-table .icheckbox_minimal-blue').hasClass('checked')){
                        var data = [];
                        $('#send-table tbody .checked').each(function(i,vo){
                            var $this = $(vo).parent().parent().parent();
                            data[i] = {
                                'mobile' : $this.find('.mobile').text(),
                                'content' : $this.find('.content').text(),
                            };
                        });
                        $('.overlay').show();
                        $.ajax({
                            url: page.siteRoot() + "scores/send",
                            type: 'POST',
                            cache: false,
                            data: {
                                _token: score.token(),
                                data: JSON.stringify(data)
                            },
                            success: function (result) {
                                $('.overlay').hide();
                                page.inform(result.title, result.message, page.success);
                            },
                            error: function (e) {
                                page.errorHandler(e);
                            }
                        });
                    } else {
                        page.inform('成绩发送', '请先选择发送内容', page.failure);
                    }
                });
            },
            /** 排名统计 */
            onRankClick: function () {
                $('#rank').on('click', function() {
                    $('#modal-rank').modal({backdrop: true});
                });
            },
            onRankScoresClick: function () {
                $('#rank-scores').off('click').on('click', function () {
                    $('.overlay').show();
                    $.ajax({
                        type: 'GET',
                        data: { _token: score.token() },
                        url: '../scores/rank/' + $('#rank_exam_id').val(),
                        success: function (result) {
                            $('.overlay').hide();
                            page.inform(result.title, result.message, page.success);
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            /** 批量导入 */
            onImportClick: function () {
                $('#import').on('click', function() {
                    $('#modal-import').modal({backdrop: true});
                });
            },
            onImportExamIdChange: function () {
                $('#import_exam_id').on('change', function () {
                    score.classList('import');
                });
            },
            onImportClassIdChange: function () {
                $(document).on('change', '#import_class_id', function () {
                    $.ajax({
                        type: 'GET',
                        dataType: 'json',
                        url: '../scores/import/' + $('#import_exam_id').val(),
                        data: {
                            _token: score.token(),
                            classId: $('#import_class_id').val()
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                })
            },
            onImportScoresClick: function () {
                $('#import-scores').on('click', function () {
                    var examId = $('#import_exam_id').val(),
                        classId = $('#import_class_id').val(),
                        data = new FormData();

                    data.append('file', $('#fileupload')[0].files[0]);
                    data.append('_token', score.token());
                    data.append('examId', examId);
                    data.append('classId', classId);
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: "../scores/import",
                        data: data,
                        contentType: false,
                        processData: false,
                        success: function (result) {
                            page.inform(result.title, result.message, page.success);
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            /** 批量导出 */
            onExportClick: function () {
                $('#export').on('click', function() {
                    $('#modal-export').modal({backdrop: true});
                });
            },
            onExportExamIdChange: function () {
                $('#export_exam_id').on('change', function () {
                    score.classList('export');
                });
            },
            onExportScoresClick: function () {
                $('#export-scores').on('click', function () {
                    var examId = $('#export_exam_id').val(),
                        classId = $('#export_class_id').val();

                    window.location = page.siteRoot() + 'scores/export?examId=' + examId + '&classId=' + classId;
                });
            },
            /** 统计分析 */
            onStatClick: function () {
                $('#stat').on('click', function () {
                    page.getTabContent($('#tab_' + page.getActiveTabId()), 'scores/stat');
                });
            },
            onExamIdChange: function () {
                var $examId = $('#exam_id');
                $examId.on('change', function () {
                    score.list('class', $('#exam_id').val());
                });
            },
            onClassIdChange: function () {
                $(document).on('change', '#class_id', function () {
                    score.list('student', $('#class_id').val());
                });
            },
            onAnalyzeClick: function () {
                $('#analyze').off('click').on('click', function () {
                    var statType = parseInt($('.checked').find('.minimal').val()),
                        data = {
                            _token: score.token(),
                            classId: $('#class_id').val()
                        };
                    $('.overlay').show();
                    $.ajax({
                        type: 'POST',
                        data: $.extend(
                            data,
                            statType === 1 ? {examId: $('#exam_id').val()} : {studentId: $('#student_id').val()}
                        ),
                        url: '../scores/stat',
                        success: function (result) {
                            $('.overlay').hide();
                            $('#params').after(result['html']);
                            statType === 1 ? score.classData() : score.studentData();
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            getSsList: function (result) {
                var $studentId = $('#student_id'),
                    $subjectId = $('#subject_id'),
                    $studentNext = $studentId.next(),
                    $studentPrev = $studentId.prev(),
                    $subjectNext = $subjectId.next(),
                    $subjectPrev = $subjectId.prev();

                $studentNext.remove();
                $studentId.remove();
                $studentPrev.after(result['students']);
                $subjectNext.remove();
                $subjectId.remove();
                $subjectPrev.after(result['subjects']);

                page.initSelect2();
            }
        };

        return {
            index: score.index,
            create: score.create,
            edit: score.edit,
            stat: score.stat
        };
    }
})(jQuery);