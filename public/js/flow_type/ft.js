//# sourceURL=ft.js
(function ($) {
    $.ft = function (options) {
        var ft = {
            options: $.extend({}, options),
            init: function (action) {
                $(document).off('click', '.add-step');
                $(document).off('click', '.remove-step');
                ft.add(action);
                ft.remove();
            },
            add: function (action) {
                $(document).on('click', '.add-step', function () {
                    var uri = action === 'edit' ? '../edit/' + $('input[name=id]').val() : 'create';
                    $('.overlay').show();
                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        url: uri,
                        data: { _token: page.token() },
                        success: function (result) {
                            $('tbody').append(result);
                            $('tbody tr:last select').select2();
                            $('.overlay').hide();
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
        remove: function() {
                $(document).on('click', '.remove-step', function () {
                    $(this).closest('tr').remove();
                });
            }
        };

        return {init: ft.init}
    }
})(jQuery);