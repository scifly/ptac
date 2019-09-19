//# sourceURL=ft.js
(function ($) {
    $.ft = function (options) {
        var ft = {
            options: $.extend({}, options),
            init: function (action) {
                ft.add(action);
                ft.remove();
            },
            add: function (action) {
                $('.overlay').show();
                $(document).on('click', '.add-step', function () {
                    var uri = action === 'edit' ? 'edit/' + $('#id').val() : 'create';
                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        url: uri,
                        data: { _token: page.token() },
                        success: function (result) {
                            $('tbody').append(result);
                            $('select[name=steps[][ids]]').select2();
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