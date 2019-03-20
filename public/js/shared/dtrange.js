//# sourceURL=dtrange.js
(function ($) {
    $.dtrange = function (options) {
        var dtrange = {
            options: $.extend({}, options),
            dRange: function (selector) {
                $.getScript(
                    page.siteRoot() + plugins.daterangepicker.moment,
                    function () {
                        $.getScript(
                            page.siteRoot() + plugins.daterangepicker.js,
                            function () {
                                var format = (selector === '.dtrange' ? "YYYY-MM-DD hh:mm:ss" : "YYYY-MM-DD"),
                                    $picker = $(selector),
                                    options = {
                                        autoUpdateInput: false,
                                        timePicker: selector === '.dtrange',
                                        timePicker24Hour: true,
                                        timePickerSeconds: true,
                                        opens: 'left',
                                        locale: {
                                            format: format,
                                            separator: ' ~ ',
                                            applyLabel: "确定",
                                            cancelLabel: "清除",
                                            fromLabel: "从",
                                            toLabel: "到",
                                            weekLabel: "W",
                                            daysOfWeek: ["日", "一", "二", "三", "四", "五", "六"],
                                            monthNames: [
                                                "一月", "二月", "三月", "四月", "五月", "六月",
                                                "七月", "八月", "九月", "十月", "十一月", "十二月"
                                            ],
                                            firstDay: 1
                                        }
                                    };

                                page.loadCss(plugins.daterangepicker.css);
                                $picker.daterangepicker(options);
                                $picker.on('apply.daterangepicker', function(ev, picker) {
                                    var value = picker.startDate.format(format) + ' ~ ' + picker.endDate.format(format);
                                    $(this).val(value).trigger('change');
                                });
                                $picker.on('cancel.daterangepicker', function() {
                                    $(this).val('').trigger('change');
                                });
                                // $picker.on('show.daterangepicker', function() {
                                //     options['drops'] = 'up';
                                //     $picker.daterangepicker(options);
                                // });
                            }
                        );
                    }
                );
            },
            tRange: function () {
                page.initParsleyRules();
                page.loadCss(plugins.timepicker.css);
                $.getMultiScripts([plugins.timepicker.js]).done(
                    function () {
                        $('.timepicker').timepicker({
                            showInputs: false,
                            showMeridian: false,
                            minuteStep: 1
                        });
                    }
                );
            }
        };

        return {
            dRange: dtrange.dRange,
            tRange: dtrange.tRange
        };
    }
})(jQuery);