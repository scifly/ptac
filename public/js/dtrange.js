//# sourceURL=dtrange.js
(function ($) {
    $.dtrange = function (options) {
        var dtrange = {
            options: $.extend({}, options),
            init: function (selector, tp) {
                var tpicker = tp !== 'undefined';
                $.getScript(
                    page.siteRoot() + plugins.daterangepicker.moment,
                    function () {
                        $.getScript(
                            page.siteRoot() + plugins.daterangepicker.js,
                            function () {
                                var format = tpicker ? "YYYY-MM-DD hh:mm:ss" : "YYYY-MM-DD",
                                    $picker = $(typeof selector === 'undefined' ? '#daterange' : selector);

                                page.loadCss(plugins.daterangepicker.css);

                                $picker.daterangepicker({
                                    autoUpdateInput: false,
                                    timePicker: tpicker,
                                    timePicker24Hour: true,
                                    timePickerSeconds: true,
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
                                });
                                $picker.on('apply.daterangepicker', function(ev, picker) {
                                    var format = tpicker ? 'YYYY-MM-DD hh:mm:ss' : 'YYYY-MM-DD';
                                    $(this).val(picker.startDate.format(format) + ' ~ ' + picker.endDate.format(format));
                                });

                                $picker.on('cancel.daterangepicker', function() {
                                    $(this).val('');
                                });
                            }
                        );
                    }
                );
            },
        };

        return { init: dtrange.init };
    }
})(jQuery);