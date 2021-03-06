//# sourceURL=contact.js
(function ($) {
    $.contact = function (options) {
        var contact = {
            options: $.extend({
                ranges: 'ranges',
                studentId: 'student_id',
                classId: 'class_id',
                gradeId: 'grade_id',
                departmentId: 'department_id',
                range: 'input[name=range]',
                singular: 'input[name=singular]',
                output: 'export',
                confirm: 'confirm',
                relationship: 'relationship',
                mobiles: 'mobiles'
            }, options),
            mobiles: function (formId, requestType, ajaxUrl) {
                var $tbody = $('#' + contact.options.mobiles).find('tbody');
                page.initICheck($tbody);
                $tbody.find('tr:not(:last) .btn-mobile-add')
                    .removeClass('btn-mobile-add').addClass('btn-mobile-remove')
                    .html('<i class="fa fa-minus text-blue"></i>');
                var $mobile = $tbody.find('tr:last input[class="form-control"]'),
                    $form = $('#' + formId);
                $form.parsley().destroy();
                $mobile.attr('pattern', '/^1[0-9]{10}$/');
                $mobile.attr('required', 'true');
                page.initParsley($form, requestType, ajaxUrl);
            },
            mobile: function (formId, size, requestType, ajaxUrl) {
                $(document).off('click', '.btn-mobile-add');
                $(document).off('click', '.btn-remove');
                $(document).off('click', '#relationship');
                $(document).off('click', '#confirm-bind');
                var $tbody = $('#' + contact.options.mobiles).find('tbody');
                $(document).off('click', '.btn-mobile-remove');
                $(document).off('click', '.btn-mobile-add').on('click', '.btn-mobile-add', function (e) {
                    e.preventDefault();
                    // add html
                    size++;
                    $tbody.append(
                        '<tr>' +
                            '<td>' +
                                '<div class="input-group">' +
                                    '<div class="input-group-addon">' +
                                        '<i class="fa fa-mobile" style="width: 20px;"></i>' +
                                    '</div>' +
                                    '<input class="form-control" ' +
                                            'placeholder="(请输入手机号码)" ' +
                                            'name="mobile[' + size + '][mobile]" ' +
                                            'value="" style="width: 100%"' +
                                    '>' +
                                '</div>' +
                            '</td>' +
                            '<td style="text-align: center">' +
                                '<input type="radio" ' +
                                    'class="minimal" ' +
                                    'id="mobile[isdefault]" ' +
                                    'name="mobile[isdefault]" ' +
                                    'value="' + size + '"' +
                                '>' +
                            '</td>' +
                            '<td style="text-align: center">' +
                                '<input type="checkbox" class="minimal" name="mobile[' + size + '][enabled]">' +
                            '</td>' +
                            '<td style="text-align: center">' +
                                '<button class="btn btn-box-tool btn-add btn-mobile-add" type="button">' +
                                    '<i class="fa fa-plus text-blue"></i>' +
                                '</button>' +
                            '</td>' +
                        '</tr>'
                    );
                    contact.mobiles(formId, requestType, ajaxUrl);
                }).on('click', '.btn-mobile-remove', function (e) {
                    $(this).parents('tr:first').remove();
                    e.preventDefault();
                    var $defaults = $('input[name="mobile[isdefault]"]'),
                        defaultChecked = false;
                    $.each($defaults, function () {
                        if (typeof $(this).attr('checked') !== 'undefined') {
                            defaultChecked = true;
                            return false;
                        }
                    });
                    if (!defaultChecked) $($defaults[0]).iCheck('check');
                    return false;
                });
            },
            onRangeChange: function (table) {
                page.initICheck();
                var $range = $(contact.options.range);
                if (table === 'educators') {
                    $range.on('ifClicked', function () {
                        var $departmentId = $('#' + contact.options.departmentId);
                        $departmentId.select2('destroy');
                        $departmentId.prop('disabled', parseInt(this.value) !== 0);
                        page.initSelect2();
                        return false;
                    });
                } else {
                    $range.on('ifClicked', function () {
                        var $gradeId = $('#' + contact.options.gradeId),
                            $classId = $('#' + contact.options.classId),
                            value = parseInt(this.value);
                        $gradeId.select2('destroy');
                        $classId.select2('destroy');
                        $gradeId.prop('disabled', value === 2);
                        $classId.prop('disabled', value !== 0);
                        page.initSelect2();
                        return false;
                    });
                }
            },
            // onSingularChange: function (table) {
            //     page.initICheck();
            //     $(contact.options.singular).on('ifClicked', function () {
            //         $(table === 'educators' ? '#relationships' : '#class-subjects')
            //             .toggle(parseInt(this.value) === 0);
            //     });
            // },
            onGradeChange: function (table, action, relationship, id) {
                $(document).off('change', '#' + contact.options.gradeId);
                $(document).on('change', '#' + contact.options.gradeId, function () {
                    var gradeId = $('#' + contact.options.gradeId).val(),
                        $classId = $('#' + contact.options.classId),
                        uri = table + '/' + action + (typeof id !== 'undefined' ? '/' + id : ''),
                        $next = $classId.next(),
                        $prev = $classId.prev();
                    $next.remove();
                    $classId.remove();
                    $prev.after(page.ajaxLoader());
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: page.siteRoot() + uri,
                        data: {
                            _token: page.token(),
                            field: 'grade',
                            id: gradeId
                        },
                        success: function (result) {
                            $('#ajaxLoader').remove();
                            $prev.after(result['html']['classes']);
                            if (typeof relationship !== 'undefined') {
                                var $studentId = $('#' + contact.options.studentId),
                                    $studentNext = $studentId.next(),
                                    $studentPrev = $studentId.prev();
                                $studentNext.remove();
                                $studentId.remove();
                                $studentPrev.after(result['html']['students']);
                            }
                            if ($('#range .checked').find('input').val() === "1") {
                                $('#' + contact.options.classId).prop('disabled', true);
                            }
                            page.initSelect2();
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            onSectionChange: function (table, action, id) {
                $(document).off('change', '#' + contact.options.classId);
                $(document).on('change', '#' + contact.options.classId, function () {
                    var classId = $('#' + contact.options.classId).val(),
                        $studentId = $('#' + contact.options.studentId),
                        $next = $studentId.next(),
                        $prev = $studentId.prev(),
                        uri = table + '/' + action + (typeof id !== 'undefined' ? '/' + id : '');
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: page.siteRoot() + uri,
                        data: {
                            _token: page.token(),
                            field: 'class',
                            id: classId
                        },
                        success: function (result) {
                            $next.remove();
                            $studentId.remove();
                            $prev.after(result['html']['students']);
                            page.initSelect2();
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            onRelationshipDelete: function () {
                $(document).on('click', '.delete', function () {
                    $(this).parents('tr').remove();
                });
            },
            onExportClick: function () {
                $('#' + contact.options.output).off('click').on('click', function () {
                    $('#' + contact.options.ranges).modal({backdrop: true});
                });
            },
            saveRelationship: function() {
                var relationship = $('#' + contact.options.relationship).val(),
                    $studentId = $('#' + contact.options.studentId),
                    student = $studentId.find("option:selected").text().split('-'),
                    studentId = $studentId.val(),
                    $studentIds = [],
                    $tbody = $('#tBody');

                if (!$.trim(relationship) || !$.trim(student) || !$.trim(studentId)) {
                    page.inform('保存监护关系', '监护关系不能为空', page.failure);
                    return false
                }
                $tbody.find(":input[type='hidden']").each(function () {
                    $studentIds.push(this.value);
                });
                if ($studentIds.length !== 0) {
                    if ($.inArray(studentId, $studentIds) >= 0) {
                        page.inform('保存监护关系', '已有该学生的监护关系！', page.failure);
                        return false;
                    }
                }
                var html = '<tr>' +
                    '<td class="text-center">' +
                        student[0] + '<input type="hidden" value="' + studentId + '" name="student_ids[]">' +
                    '</td>' +
                    '<td class="text-center">' + student[1] + '</td>' +
                    '<td class="text-center">' +
                        '<input type="text" name="relationships[]" ' +
                                'readonly class="no-border text-center" ' +
                                'style="background: none;" ' +
                                'value="' + relationship + '"' +
                        '>' +
                    '</td>' +
                    '<td class="text-center">' +
                        '<a href="javascript:" class="delete">' +
                            '<i class="fa fa-trash-o text-blue"></i>' +
                        '</a>' +
                    '</td>' +
                '</tr>';
                $tbody.append(html);
            },
            onImportClick: function (table) {
                $('#import').on('click', function () {
                    $('#upload').modal({backdrop: true});
                    contact.onConfirmImportClick(table);
                });
            },
            onConfirmClick: function (table, relationship) {
                // var $ranges = $('#' + contact.options.ranges);
                var confirm = '#' + contact.options.confirm;

                $(document).off('click', '#' + contact.options.output);
                $(document).off('click', confirm);
                $(document).on('click', confirm, function () {
                    if (typeof relationship === 'undefined') {
                        var range = parseInt($($('.checked').children()[0]).val()),
                            url = page.siteRoot() + table + '/export',
                            $gradeId = $('#' + contact.options.gradeId),
                            $classId = $('#' + contact.options.classId),
                            $departmentId = $('#' + contact.options.departmentId),
                            departmentId = (table !== 'educators'
                                ? (range === 0 ? $classId.val() : (range === 1 ? $gradeId.val() : ''))
                                : (range === 0 ? $departmentId.val() : ''));
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: url,
                            data: {
                                _token: page.token(),
                                range: range,
                                id: departmentId
                            },
                            success: function (result) {
                                page.inform(result['title'], result['message'], page.success);
                            },
                            error: function (e) {
                                page.errorHandler(e);
                            }
                        });
                    } else {
                        contact.saveRelationship(0)
                    }
                    // $ranges.modal('hide');
                });
            },
            onConfirmImportClick: function (table) {
                $('#confirm-import').off('click').on('click', function () {
                    var formData = new FormData();

                    formData.append('_token', page.token());
                    formData.append('file', $('#fileupload')[0].files[0]);
                    page.inform("导入通讯录", '正在导入中...', page.info);

                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: '../' + table + '/import',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (result) {
                            page.inform('导入通讯录', result.message,  page.success);
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                })
            },
            onAddClassClick: function () {
                $(document).off('click', '.btn-class-add');
                $(document).off('click', '.btn-class-remove');

                var $tbody = $("#classes").find("tbody");
                $(document).on('click', '.btn-class-add', function (e) {
                    e.preventDefault();
                    var html = $tbody.find('tr').last().clone();
                    html.find('span.select2').remove();
                    // 删除插件初始化增加的html
                    $tbody.append(html);
                    // select2 init
                    page.initSelect2();
                    // 加减切换
                    $tbody.find('tr:not(:last) .btn-class-add')
                        .removeClass('btn-class-add').addClass('btn-class-remove')
                        .html('<i class="fa fa-minus text-blue"></i>');
                    $('#user[group_id]').on('click',function(){
                        alert(123);
                    });
                }).on('click', '.btn-class-remove', function (e) {
                    // 删除元素
                    $(this).parents('tr:first').remove();
                    e.preventDefault();
                    return false;
                });
            },
            onAddClick: function () {
                $('#add').on('click', function () {
                    $('#' + contact.options.ranges).modal({backdrop: true});
                });
            },
            index: function (table) {
                contact.onRangeChange(table);
                page.initSelect2();
                contact.onExportClick();
                contact.onConfirmClick(table);
                switch (table) {
                    case 'students':
                        contact.onGradeChange(table, 'export');
                        contact.onImportClick(table);
                        break;
                    case 'custodians':
                        contact.onGradeChange(table, 'export');
                        break;
                    case 'educators':
                        contact.onImportClick(table);
                        break;
                    default:
                        break;
                }
            },
            action: function (table, type) {
                var formId = '',
                    id = $('#id').val();
                switch (table) {
                    case 'students':
                        formId = 'formStudent';
                        contact.onGradeChange(table, type, null, id);
                        break;
                    case 'custodians':
                    case 'educators':
                        $('#' + contact.options.relationship).val('');
                        contact.onAddClick();
                        contact.onAddClassClick();
                        // contact.onSingularChange(table);
                        contact.onRelationshipDelete();
                        contact.onGradeChange(table, type, true, id);
                        contact.onSectionChange(table, type, id);
                        contact.onConfirmClick(table, true);
                        formId = table === 'custodians' ? 'formCustodian' : 'formEducator';
                        if (table === 'educators') {
                            $.getMultiScripts(['js/shared/tree.js']).done(
                                function() { $.tree().list(
                                    'educators/' + type + (typeof id !== 'undefined' ? '/' + id : ''), 'department');
                                }
                            );
                        }
                        break;
                    case 'operators':
                        formId = 'formOperator';
                        break;
                    default:
                        break;
                }
                contact.mobile(
                    formId,
                    type === 'create' ? 0 : $('#count').val(),
                    type === 'create' ? 'POST' : 'PUT',
                    table + (type === 'create' ? '/store' : '/update/' + id)
                );
            }
        };

        return {
            index: contact.index,
            action: contact.action,
            onGradeChange: contact.onGradeChange
        }
    }
})(jQuery);