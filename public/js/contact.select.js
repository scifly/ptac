//# sourceURL=contact.select.js
(function ($) {
    $.contactRange = function (options) {
        var cr = {
            options: $.extend({
                ranges: 'ranges',
                studentId: 'student_id',
                classId: 'class_id',
                gradeId: 'grade_id',
                departmentId: 'department_id',
                range: 'input[name="range"]',
                output: 'export',
                confirm: 'confirm',
                relationship: 'relationship',
                mobiles: 'mobiles'
            }, options),
            token: function () {
                return $('#csrf_token').attr('content');
            },
            mobiles: function (formId, requestType, ajaxUrl) {
                var $tbody = $('#' + cr.options.mobiles).find('tbody');
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
                var $tbody = $('#' + cr.options.mobiles).find('tbody');
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
                                            'placeholder="（请输入手机号码）" ' +
                                            'name="mobile[' + size + '][mobile]" ' +
                                            'value="" style="width: 75%"' +
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
                    cr.mobiles(formId, requestType, ajaxUrl);
                }).on('click', '.btn-mobile-remove', function (e) {
                    $(this).parents('tr:first').remove();
                    e.preventDefault();
                    var $defaults = $('input[name="mobile[isdefault]"]');
                    var defaultChecked = false;
                    $.each($defaults, function () {
                        if (typeof $(this).attr('checked') !== 'undefined') {
                            defaultChecked = true;
                            return false;
                        }
                    });
                    if (!defaultChecked) {
                        $($defaults[0]).iCheck('check');
                    }
                    return false;
                });
            },
            onRangeChange: function (table) {
                page.initICheck();
                var $range = $(cr.options.range);
                if (table === 'educators') {
                    $range.on('ifClicked', function () {
                        var $departmentId = $('#' + cr.options.departmentId);
                        $departmentId.select2('destroy');
                        $departmentId.prop('disabled', parseInt(this.value) !== 0);
                        page.initSelect2();
                        return false;
                    });
                } else {
                    $range.on('ifClicked', function () {
                        var $gradeId = $('#' + cr.options.gradeId);
                        var $classId = $('#' + cr.options.classId);
                        var value = parseInt(this.value);
                        $gradeId.select2('destroy');
                        $classId.select2('destroy');
                        $gradeId.prop('disabled', value === 2);
                        $classId.prop('disabled', value !== 0);
                        page.initSelect2();
                        return false;
                    });
                }
            },
            onGradeChange: function (table, action, relationship, id) {
                $(document).off('change', '#' + cr.options.gradeId);
                $(document).on('change', '#' + cr.options.gradeId, function () {
                    var gradeId = $('#' + cr.options.gradeId).val(),
                        $classId = $('#' + cr.options.classId),
                        uri = table + '/' + action + (typeof id !== 'undefined' ? '/' + id : ''),
                        disabled = $classId.prop('disabled'),
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
                            _token: cr.token(),
                            field: 'grade',
                            id: gradeId
                        },
                        success: function (result) {
                            $('#ajaxLoader').remove();
                            $prev.after(result['html']['classes']);
                            if (disabled) {
                                $('#class_id').prop('disabled', true);
                            }
                            if (typeof relationship !== 'undefined') {
                                var $studentId = $('#' + cr.options.studentId),
                                    $studentNext = $studentId.next(),
                                    $studentPrev = $studentId.prev();
                                $studentNext.remove();
                                $studentId.remove();
                                $studentPrev.after(result['html']['students']);
                            }
                            page.initSelect2();
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            onClassChange: function (table, action, id) {
                $(document).off('change', '#' + cr.options.classId);
                $(document).on('change', '#' + cr.options.classId, function () {
                    var classId = $('#' + cr.options.classId).val(),
                        $studentId = $('#' + cr.options.studentId),
                        $next = $studentId.next(),
                        $prev = $studentId.prev(),
                        uri = table + '/' + action + (typeof id !== 'undefined' ? '/' + id : '');
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: page.siteRoot() + uri,
                        data: {
                            _token: cr.token(),
                            field: 'class',
                            id: classId
                        },
                        success: function (result) {
                            $next.remove();
                            $studentId.remove();
                            $prev.after(result['html']);
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
                $('#' + cr.options.output).off('click').on('click', function () {
                    $('#' + cr.options.ranges).modal({backdrop: true});
                });
            },
            saveRelationship: function(items) {
                var relationship = $('#' + cr.options.relationship).val(),
                    $studentId = $('#' + cr.options.studentId),
                    student = $studentId.find("option:selected").text().split('-'),
                    studentId = $studentId.val();
                if (!$.trim(relationship) || !$.trim(student) || !$.trim(studentId)) {
                    page.inform('保存监护关系', '监护关系不能为空', page.failure);
                    return false
                }
                var $studentIds = [];
                var $tbody = $('#tBody');
                $tbody.find(":input[type='hidden']").each(function () {
                    $studentIds.push(this.value);
                });
                if ($studentIds.length !== 0) {
                    var index = $.inArray(studentId, $studentIds);
                    if (index >= 0) {
                        page.inform('保存监护关系', '已有该学生的监护关系！', page.failure);
                        return false;
                    }
                }
                items++;
                var html =
                    '<input type="hidden" value="' + studentId + '" name="student_ids[' + items + ']">' +
                    '<tr>' +
                        '<td>' + student[0] + '</td>' +
                        '<td>' + student[1] + '</td>' +
                        '<td>' +
                            '<input type="text" ' +
                                    'name="relationships[' + items + ']" ' +
                                    'id="" readonly class="no-border" ' +
                                    'style="background: none" ' +
                                    'value="' + relationship + '"' +
                            '>' +
                        '</td>' +
                        '<td>' +
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
                    cr.onConfirmImportClick(table);
                });
            },
            onConfirmClick: function (table, relationship) {
                $(document).off('click', '#' + cr.options.output);
                $(document).off('click', '#' + cr.options.confirm);
                $(document).on('click', '#' + cr.options.confirm, function () {
                    if (typeof relationship === 'undefined') {
                        var range = parseInt($($('.checked').children[0]).val()),
                            url = page.siteRoot() + table + '/export?range=' + range,
                            $ranges = $('#' + cr.options.ranges),
                            $gradeId = $('#' + cr.options.gradeId),
                            $classId = $('#' + cr.options.classId),
                            $departmentId = $('#' + cr.options.departmentId),
                            id = (table !== 'educators'
                                ? (range === 0 ? $classId.val() : (range === 1 ? $gradeId.val() : ''))
                                : (range ? $departmentId.val() : ''));
                        window.location = url + (id !== '' ? '&id=' + id : '');
                    } else {
                        cr.saveRelationship(0)
                    }

                    $ranges.modal('hide');
                });
            },
            onConfirmImportClick: function (table) {
                $('#confirm-import').off('click').on('click', function () {
                    page.inform("导入通讯录", '正在导入中...', page.info);
                    var formData = new FormData();
                    formData.append('file', $('#fileupload')[0].files[0]);
                    formData.append('_token', $('#csrf_token').attr('content'));
                    $.ajax({
                        url: '../' + table + '/import',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (result) {
                            page.inform(
                                '导入通讯录', result.message,
                                result.error === 0 ? page.success : page.failure
                            );
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
                    $('#' + cr.options.ranges).modal({backdrop: true});
                });
            },
            index: function (table) {
                cr.onRangeChange(table);
                page.initSelect2();
                cr.onExportClick();
                cr.onConfirmClick(table);
                switch (table) {
                    case 'students':
                        cr.onGradeChange(table, 'export');
                        cr.onImportClick(table);
                        break;
                    case 'custodians':
                        cr.onGradeChange(table, 'export');
                        break;
                    case 'educators':
                        cr.onImportClick(table);
                        break;
                    default:
                        break;
                }
            },
            create: function (table) {
                var formId = '';
                switch (table) {
                    case 'students':
                        formId = 'formStudent';
                        cr.onGradeChange(table, 'create');
                        break;
                    case 'custodians':
                        formId = 'formCustodian';
                        $('#' + cr.options.relationship).val('');
                        cr.onAddClick();
                        cr.onRelationshipDelete();
                        cr.onGradeChange(table, 'create', true);
                        cr.onClassChange(table, 'create');
                        cr.onConfirmClick(table, true);
                        break;
                    case 'educators':
                        formId = 'formEducator';
                        cr.onAddClassClick();
                        $.getMultiScripts(['js/department.tree.js'])
                            .done(function() { dept.init('educators/create'); });
                        break;
                    case 'operators':
                        formId = 'formOperator';
                        break;
                    default:
                        break;
                }
                cr.mobile(formId, 0, 'POST', table + '/store');
            },
            edit: function (table) {
                var formId = '',
                    id = $('#id').val();
                switch (table) {
                    case 'students':
                        formId = 'formStudent';
                        cr.onGradeChange(table, 'edit', null, id);
                        break;
                    case 'custodians':
                        $('#' + cr.options.relationship).val('');
                        cr.onAddClick();
                        cr.onRelationshipDelete();
                        cr.onGradeChange(table, 'edit', true, id);
                        cr.onClassChange(table, 'edit', id);
                        cr.onConfirmClick(table, true);
                        break;
                    case 'educators':
                        formId = 'formEducator';
                        cr.onAddClassClick();
                        $.getMultiScripts(['js/department.tree.js'])
                            .done(function() { dept.init('educators/edit/' + id); });
                        break;
                    default:
                        break;
                }
                cr.mobile(formId, $('#count').val(), 'PUT', table + '/update/' +id);
            }
        };

        return {
            index: cr.index,
            create: cr.create,
            edit: cr.edit,
            onGradeChange: cr.onGradeChange
        }
    }
})(jQuery);