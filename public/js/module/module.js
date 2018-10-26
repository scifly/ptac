(function ($) {
    $.module = function (options) {
        var module = {
            options: $.extend({}, options),
            action: function (table, type) {
                var $schoolId = $('#school_id');

                $schoolId.on('change', function () {
                    var $groupId = $('#group_id'),
                        $next = $groupId.next(),
                        $prev = $groupId.prev();

                    $('.overlay').show();
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: page.siteRoot() + table + '/' + type + (type === 'edit' ? '/' + $('#id').val() : ''),
                        data: {
                            _token: page.token(),
                            school_id: $(this).val(),
                        },
                        success: function (result) {
                            $next.remove();
                            $groupId.remove();
                            $prev.after(result);
                            page.initSelect2();
                            $('.overlay').hide();
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            }
        };
        return {
            action: module.action
        }

    };
})(jQuery);