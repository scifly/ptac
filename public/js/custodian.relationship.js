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
    exportPupils: function () {
        return $("#export-pupils");
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
    schoolChange: function(item, type, id) {
        $(document).off('change',"#schoolId");
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

            var uri =  item + type + id + '?field=school' + '&id=' + schoolId + '&_token=' + token;
            var curWwwPath = window.document.location.href;
            //获取主机地址之后的目录，如： uimcardprj/share/meun.jsp
            var pathName = window.document.location.pathname;
            var pos = curWwwPath.indexOf(pathName);
            //获取带"/"的项目名，如：/uimcardprj
            var projectName = pathName.substring(0, pathName.substr(1).indexOf('/') + 1);
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: page.siteRoot() + uri,
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

    gradeChange: function (item, type, id) {
        $(document).off('change',"#gradeId");
        $(document).on('change', '#gradeId', function () {
            var gradeId = $('#gradeId').val();

            var $classId = $('#classId');
            var $next = $classId.next();
            var $prev = $classId.prev();

            var $studentId = $('#studentId');
            var $studentNext = $studentId.next();
            var $studentPrev = $studentId.prev();
            var token = $('#csrf_token').attr('content');
            var uri =  item + type + id+ '?field=grade' + '&id=' + gradeId + '&_token=' + token;
            //获取主机地址之后的目录，如： uimcardprj/share/meun.jsp
            // var pathName = window.document.location.pathname;
            // //获取带"/"的项目名，如：/uimcardprj
            // var projectName = pathName.substring(0, pathName.substr(1).indexOf('/') + 1).replace('/', '');

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: page.siteRoot() + uri,
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
    classChange: function (item, type, id) {
        $(document).off('change',"#classId");
        $(document).on('change', '#classId', function () {
            var classId = $('#classId').val();
            var $studentId = $('#studentId');
            var $next = $studentId.next();
            var $prev = $studentId.prev();
            var token = $('#csrf_token').attr('content');
            var uri =  item + type + id + '?field=class' + '&id=' + classId + '&_token=' + token;
            var curWwwPath = window.document.location.href;
            //获取主机地址之后的目录，如： uimcardprj/share/meun.jsp
            var pathName = window.document.location.pathname;
            var pos = curWwwPath.indexOf(pathName);
            //获取带"/"的项目名，如：/uimcardprj
            var projectName = pathName.substring(0, pathName.substr(1).indexOf('/') + 1);
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: page.siteRoot() + uri,
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
    init: function (item, type, id) {
        custodian.schoolChange(item, type, id);
        custodian.gradeChange(item, type, id);
        custodian.classChange(item, type, id);
        if (item === 'students/') {
            page.initSelect2();
            custodian.exportStudent();
            custodian.$export().on('click', function () { $('#export-pupils').modal({backdrop: true}) });

        }else if (item === 'educators/') {
            page.initSelect2();
            custodian.exportEducator();
            custodian.$export().on('click', function () { $('#export-pupils').modal({backdrop: true}) });

        }else{
            custodian.relationship().val("");
            custodian.deleteItem();
            custodian.saveStudent(0);
            custodian.$addPupil().on('click', function () { $('#pupils').modal({backdrop: true}) });
        }
    },
    exportStudent: function () {
        $(document).off('click', '#export');
        $(document).off('click', '#confirm-bind');
        $(document).on('click', '#confirm-bind', function () {
            var classId = custodian.$classId().val();
            //无法用ajax请求
            window.location.href = page.siteRoot() +　"students/export?id=" + classId;
            custodian.exportPupils().modal('hide');
        });
    },
    exportEducator: function () {
        $(document).off('click', '#export');
        $(document).off('click', '#confirm-bind');
        $(document).on('click', '#confirm-bind', function () {
            var schoolId = custodian.$schoolId().val();
            //无法用ajax请求
            window.location.href = page.siteRoot() +　'educators/export?id=' + schoolId;
            custodian.exportPupils().modal('hide');
        });
    }
};