//# sourceURL=operator.js
(function ($) {
    $.operator = function (options) {
        var operator = {
            options: $.extend({
                groupId: 'group_id',
                corp: 'corp',
                school: 'school',
                corpId: 'corp_id',
                schoolId: 'school_id',
                formId: 'formOperator',
                table: 'operators'
            }, options),
            init: function (action) {
                var $groupId = $('#' + operator.options.groupId),
                    $corp = $('#' + operator.options.corp),
                    $school = $('#' + operator.options.school);

                // init form
                switch (action) {
                    case 'create':
                        page.create(operator.options.formId, operator.options.table);
                        break;
                    case 'edit':
                        page.edit(operator.options.formId, operator.options.table);
                        break;
                    default:
                        return false;
                }
                // init mobile view events
                operator.loadJs(action);

                // init group_id change event
                $groupId.on('change', function () {
                    var $corpId = $('#' + operator.options.corpId),
                        $schoolId = $('#' + operator.options.schoolId);

                    switch (parseInt($groupId.val())) {
                        case 1:
                            $corp.slideUp();
                            $school.slideUp();
                            break;
                        case 2:
                            if ($corpId.length === 0) {
                                operator.lists(action);
                            }
                            $corp.slideDown();
                            $school.slideUp();
                            break;
                        case 3:
                            if ($schoolId.length === 0) {
                                operator.lists(action);
                            } else {
                                operator.lists(action, operator.options.corpId);
                            }
                            $corp.slideDown();
                            $school.slideDown();
                            break;
                        default:
                            break;
                    }
                });

                // init corp_id change event
                $(document).on('change', '#' + operator.options.corpId, function () {
                    if ($('#' + operator.options.school).is(':visible')) {
                        operator.lists(action, operator.options.corpId);
                    }
                });
            },
            loadJs: function (action) {
                $.getMultiScripts(['js/shared/contact.js']).done(
                    function () {
                        var cr = $.contact();
                        switch (action) {
                            case 'create':
                                cr.create(operator.options.table);
                                break;
                            case 'edit':
                                cr.edit(operator.options.table);
                                break;
                            default:
                                return false;
                        }
                    }
                );
            },
            lists: function (action, field) {
                var value = 0,
                    $corp = $('#' + operator.options.corp),
                    $school = $('#' + operator.options.school);
                if (typeof field === 'undefined') {
                    field = operator.options.groupId;
                    value = $('#' + field).val();
                } else {
                    value = $('#' + field).val();
                }
                $('.overlay').show();
                return $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: page.token(),
                        field: field,
                        value: value
                    },
                    url: page.siteRoot() + operator.options.table + '/' + action + (action === 'edit' ? '/' + $('#id').val() : ''),
                    success: function (result) {
                        if (field === operator.options.groupId) {
                            var $corpId = $('#' + operator.options.corpId);
                            if ($corpId.length === 0) {
                                $corp.find('.input-group').append(result['corpList']);
                                if ($corpId.find('option').length <= 1) {
                                    $corpId.prop('disabled', true);
                                }
                                $corpId.select2();
                            }
                            $school.find('.input-group').append(result['schoolList']);
                            $('#' + operator.options.schoolId).select2();
                        } else {
                            var $schoolId = $('#' + operator.options.schoolId),
                                $prev = $schoolId.prev(),
                                $next = $schoolId.next();
                            $next.remove();
                            $schoolId.remove();
                            $prev.after(result['schoolList']);
                            if ($schoolId.find('option').length <= 1) {
                                $schoolId.prop('disabled', true);
                            }
                            page.initSelect2();
                        }
                        $('.overlay').hide();
                    },
                    error: function (e) {
                        page.errorHandler(e);
                    }
                });
            }
        };

        return {
            init: operator.init
        }
    }
})(jQuery);