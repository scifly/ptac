(function ($) {
    $.atsettings = function (options) {
        var atsettings = {
            options: $.extend({}, options),
            init: function (action, table, form) {
                if (action === 'create') {
                    page.create(form, table);
                } else {
                    page.edit(form, table);
                }
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

        return { init: atsettings.init };
    }
})(jQuery);