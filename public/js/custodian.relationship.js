var custodian = {
    $studentId: function () {
        return $('#studentId');
    },
    $classId: function () {
        return $('#classId');
    },
    $schoolId: function () {
        return $('#schoolId');
    },
    $addPupil: function () {
        return $('#add-pupil');
    },
    $export: function () {
        return $('#export');
    },
    $tBody: function () {
        return $('#tBody');
    },
    relationship: function () {
        return $('#relationship');
    },
    saveStudent: function(item) {
        $(document).on('click', '#confirm-bind', function () {
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

            item++;

            var htm = '<tr>' +
                '<input type="hidden" value="' + studentId + '" name="student_ids[' + item + ']">' +
                '<td>' + student[0] + '</td>' +
                '<td>' + student[1] + '</td>' +
                '<td><input type="text" name="relationships[' + item +  ']" id="" readonly class="no-border" style="background: none" value="' + relationship + '"></td>' +
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
    schoolChange: function() {
        $(document).on('change', '#schoolId', function () {
            var schoolId = $('#schoolId').val();

            var $gradeId = $('#gradeId');
            var $next = $gradeId.next();
            var $prev = $gradeId.prev();

            var $classId = $('#classId');
            var $classNext = $classId.next();
            var $classPrev = $classId.prev();

            var $studentId = $('#studentId');
            var $studentNext = $studentId.next();
            var $studentPrev = $studentId.prev();
            var token = $('#csrf_token').attr('content');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: page.siteRoot() + 'custodians/create?field=school' + '&id=' + schoolId + '&_token=' + token,
                success: function (result) {
                    $next.remove();
                    $gradeId.remove();
                    $prev.after(result['html']['grades']);

                    $classNext.remove();
                    $classId.remove();
                    $classPrev.after(result['html']['classes']);

                    $studentNext.remove();
                    $studentId.remove();
                    $studentPrev.after(result['html']['students']);

                    page.initSelect2();
                }
            });
        });
    },

    gradeChange: function () {
        $(document).on('change', '#gradeId', function () {
            var gradeId = $('#gradeId').val();

            var $classId = $('#classId');
            var $next = $classId.next();
            var $prev = $classId.prev();

            var $studentId = $('#studentId');
            var $studentNext = $studentId.next();
            var $studentPrev = $studentId.prev();

            var token = $('#csrf_token').attr('content');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: page.siteRoot() + 'custodians/create?field=grade' + '&id=' + gradeId + '&_token=' + token,
                success: function (result) {
                    $next.remove();
                    $classId.remove();
                    $prev.after(result['html']['classes']);

                    $studentNext.remove();
                    $studentId.remove();
                    $studentPrev.after(result['html']['students']);
                    page.initSelect2();
                }
            });
        });
    },
    classChange: function () {
        $(document).on('change', '#classId', function () {
            var classId = $('#classId').val();
            var $studentId = $('#studentId');
            var $next = $studentId.next();
            var $prev = $studentId.prev();
            var token = $('#csrf_token').attr('content');
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: page.siteRoot() + 'custodians/create?field=class' + '&id=' + classId + '&_token=' + token,
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
    init: function (item) {
        custodian.schoolChange();
        custodian.gradeChange();
        custodian.classChange();

        if (item === 'student') {
            page.initSelect2();
            custodian.exportStudent();
            custodian.$export().on('click', function () { $('#export-pupils').modal({backdrop: true}) });

        }else if (item === 'educator') {
            page.initSelect2();
            custodian.exportEducator();
            custodian.$export().on('click', function () { $('#export-pupils').modal({backdrop: true}) });

        }else{
            custodian.relationship().val("");
            custodian.deleteItem();
            custodian.saveStudent(item);
            custodian.$addPupil().on('click', function () { $('#pupils').modal({backdrop: true}) });
        }
    },
    exportStudent: function () {
        $(document).off('click', '#confirm-bind');
        $(document).on('click', '#confirm-bind', function () {
            var classId = custodian.$classId().val();
            //无法用ajax请求
            window.location.href='../ptac/public/students/export/?id=' + classId;
            $("#export-pupils").modal('hide');
        });
    },
    exportEducator: function () {
        $(document).off('click', '#confirm-bind');
        $(document).on('click', '#confirm-bind', function () {
            var schoolId = custodian.$schoolId().val();
            //无法用ajax请求
            window.location.href='../ptac/public/educators/export/?id=' + schoolId;
            $("#export-pupils").modal('hide');
        });
    }
};