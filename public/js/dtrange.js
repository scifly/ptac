//# sourceURL=dtrange.js
(function ($) {
    $.dtrange = function (options) {
        var dtrange = {
            options: $.extend({}, options),
            init: function (selector, tp) {
                $.getScript(
                    page.siteRoot() + plugins.daterangepicker.moment,
                    function () {
                        $.getScript(
                            page.siteRoot() + plugins.daterangepicker.js,
                            function () {
                                if (typeof tp !== 'undefined') {
                                    page.loadCss(plugins.timepicker.css);
                                    $.getMultiScripts([plugins.timepicker.js], function () {
                                        dtrange.load(selector, tp);
                                    })
                                } else {
                                    dtrange.load(selector, tp);
                                }
                            }
                        );
                    }
                );
            },
            load: function (selector, tp) {
                var format = typeof tp === 'undefined'
                    ? "YYYY-MM-DD"
                    : "YYYY-MM-DD hh:mm:ss";
                page.loadCss(plugins.daterangepicker.css);
                $(typeof selector === 'undefined' ? '#daterange' : selector).daterangepicker({
                    locale: {
                        format: format,
                        timepicker: tp !== 'undefined',
                        separator: ' ~ ',
                        applyLabel: "确定",
                        cancelLabel: "取消",
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
            }
        };

        return { init: dtrange.init };
    }
})(jQuery);