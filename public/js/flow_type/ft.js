//# sourceURL=ft.js
(function ($) {
    $.ft = function (options) {
        var ft = {
            options: $.extend({
                step:  {
                    name: '',
                    option: {
                        ajax: { url: '', dataType: 'json' },
                        minimumInputLength: 1
                    }
                }
            }, options),
            init: function (action) {
                var url = action, options = ft.options.step;
                if (action === 'edit') {
                    url = url + '/' + $('input[name=id]').val();
                }
                $(document).off('click', '.add-step');
                $(document).off('click', '.remove-step');
                ft.add(action);
                ft.remove();
                options['name'] = 'select[name="ids[]"]';
                options['option']['ajax']['url'] = url;
                page[action]('formFlowType', 'flow_types', [options]);
            },
            add: function (action) {
                $(document).on('click', '.add-step', function () {
                    var uri = action === 'edit' ? '../edit/' + $('input[name=id]').val() : 'create';
                    $('.overlay').show();
                    $.ajax({
                        type: 'POST',
                        dataType: 'html',
                        url: uri,
                        data: {_token: page.token()},
                        success: function (result) {
                            var options = ft.options.step;
                            $('tbody').append(result);
                            options['name'] = 'tbody tr:last select';
                            options['option']['ajax']['url'] = uri;
                            page.initSelect2([options]);
                            // $('tbody tr:last select').select2();
                            $('.overlay').hide();
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                });
            },
            remove: function () {
                $(document).on('click', '.remove-step', function () {
                    $(this).closest('tr').remove();
                });
            }
        };

        return {init: ft.init}
    }
})(jQuery);