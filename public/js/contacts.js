var custodian = {
    $studentId: function () { return $('#studentId'); },
    $classId: function () { return $('#class_id'); },
    $gradeId: function () { return $('#grade_id'); },
    $schoolId: function () { return $('#schoolId'); },
    $addPupil: function () { return $('#add-pupil'); },
    $export: function () { return $('#export'); },
    $tBody: function () { return $('#tBody'); },
    relationship: function () { return $('#relationship'); },
    exportPupils: function () { return $("#export-pupils"); },
    saveStudent: function (item) {
        $(document).on('click', '#confirm', function () {
            var relationship = custodian.relationship().val();
            var student = custodian.$studentId().find("option:selected").text().split('-');
            var studentId = custodian.$studentId().val();
            if (relationship === '') {
                alert('监护关系不能为空');
                return false
            }
            if (student === '' || studentId === '') {
                alert('被监护人不能为空');
                return false
            }
            var $studentIds = [];
            // var warn = $('#warn-info');
            $("#tBody").find(":input[type='hidden']").each(function () {
                $studentIds.push(this.value);
            });
            if ($studentIds.length !== 0) {
                var index = $.inArray(studentId, $studentIds);
                if (index >= 0) {
                    alert('已有该学生的监护关系！');
                    return false;
                    // var html = '<div id="warn-info" class="text-red">tips：已有该学生的监护关系</div>';
                    // if(warn.length > 0){
                    //     warn.remove();
                    // }
                    // $('#relationship').parent().append(html);
                }
            }
            item++;

            var htm = '<tr>' +
                '<input type="hidden" value="' + studentId + '" name="student_ids[' + item + ']">' +
                '<td>' + student[0] + '</td>' +
                '<td>' + student[1] + '</td>' +
                '<td><input type="text" name="relationships[' + item + ']" id="" readonly class="no-border" style="background: none" value="' + relationship + '"></td>' +
                '<td>' +
                '<a href="javascript:" class="delete">' +
                '<i class="fa fa-trash-o text-blue"></i>' +
                '</a>' +
                '</td>' +
                '</tr>';
            custodian.$tBody().append(htm);
            // $("#pupils").hide();
            $("#pupils").modal('hide');
        });
    },
    gradeChange: function (table, action, id) {
        $(document).off('change', '#grade_id');
        $(document).on('change', '#grade_id', function () {
            var gradeId = $('#grade_id').val();
            var $classId = $('#class_id');
            var disabled = $classId.prop('disabled');
            var $next = $classId.next();
            var $prev = $classId.prev();
            var $studentId = $('#studentId');
            var $studentNext = $studentId.next();
            var $studentPrev = $studentId.prev();
            var token = $('#csrf_token').attr('content');
            var uri = table + '/' + action + (typeof id !== 'undefined' ? '/' + id : '');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: page.siteRoot() + uri,
                data: {
                    _token: token,
                    field: 'grade',
                    id: gradeId
                },
                success: function (result) {
                    $next.remove();
                    $classId.remove();
                    $prev.after(result['html']['classes']);
                    if (disabled) { $('#class_id').prop('disabled', true); }
                    $studentNext.remove();
                    $studentId.remove();
                    $studentPrev.after(result['html']['students']);
                    page.initSelect2();
                }
            });
        });
    },
    classChange: function (table, action, id) {
        $(document).off('change', '#class_id');
        $(document).on('change', '#class_id', function () {
            var classId = $('#class_id').val();
            var $studentId = $('#studentId');
            var $next = $studentId.next();
            var $prev = $studentId.prev();
            var token = $('#csrf_token').attr('content');
            var uri = table + '/' + action + (typeof id !== 'undefined' ? '/' + id : '');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: page.siteRoot() + uri,
                data: {
                    _token: token,
                    field: 'class',
                    id: classId
                },
                success: function (result) {
                    $next.remove();
                    $studentId.remove();
                    $prev.after(result['html']['students']);
                    page.initSelect2();
                }
            });
        });
    },
    deleteItem: function () {
        $(document).on('click', '.delete', function () {
            $(this).parents('tr').remove();
        });
    },
    init: function (table, action, relationship, id) {
        custodian.gradeChange(table, action, id);
        custodian.classChange(table, action, id);
        if (table === 'students') {
            page.initSelect2();
            custodian.exportStudent();
            custodian.$export().on('click', function () {
                $('#export-pupils').modal({backdrop: true})
            });
        } else if (table === 'educators') {
            page.initSelect2();
            custodian.exportEducator();
            custodian.$export().on('click', function () {
                $('#export-pupils').modal({backdrop: true})
            });
        } else {
            custodian.relationship().val("");
            custodian.deleteItem();
            custodian.saveStudent(0);
            custodian.$addPupil().on('click', function () {
                $('#ranges').modal({backdrop: true})
            });
        }
    },
    exportStudent: function () {
        $(document).off('click', '#export');
        $(document).off('click', '#confirm');
        $(document).on('click', '#confirm', function () {
            var range = parseInt($($('.checked').children()[0]).val());
            var url = page.siteRoot() + 'students/export?range=' + range;
            switch (range) {
                case 0:
                    url += '&id=' + custodian.$classId().val();
                    break;
                case 1:
                    url += '&id=' + custodian.$gradeId().val();
                    break;
                default:
                    break;
            }
            //无法用ajax请求
            window.location = url;
            custodian.exportPupils().modal('hide');
        });
    },
    exportEducator: function () {
        $(document).off('click', '#export');
        $(document).off('click', '#confirm');
        $(document).on('click', '#confirm', function () {
            var range = parseInt($($('.checked').children()[0]).val());
            window.location.href = page.siteRoot() + 'educators/export?range=' + range +
                (range === 0 ? '&department_id=' + $('#department_id').val() : '');
            custodian.exportPupils().modal('hide');
        });
    },
    output: function (type) {
        $(document).off('click', '#export');
        $(document).off('click', '#confirm');
        $(document).on('click', '#confirm', function () {
            var range = parseInt($($('.checked').children()[0]).val());
            var url = page.siteRoot() + 'students/export?range=' + range;
            var id = (type !== 'educator'
                ? (range === 0 ? $('#class_id').val() : (range === 1 ? $('#grade_id').val() : ''))
                : (range ? $('#department_id').val() : ''));
            window.location = url + (id !== '' ? '&id=' + id : '');
            $('#ranges').modal('hide');
        });
    }
};