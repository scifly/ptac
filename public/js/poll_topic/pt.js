//# sourceURL=pt.js
(function ($) {
    $.pt = function (options) {
        var pt = {
            options: $.extend({}, options),
            row: '<tr>' +
                '<td>' +
                    '<input class="form-control text-blue" name="option[]" type="text">' +
                '</td>' +
                '<td class="text-center">' +
                    '<button class="btn btn-box-tool remove-option" title="移除">' +
                        '<i class="fa fa-minus text-blue"></i>' +
                    '</button>' +
                '</td>' +
            '</tr>',
            init: function () {
                $(function () {
                    $(".datepicker").datetimepicker({
                        dateFormat: 'yy-mm-dd',
                        showSecond: true,
                        timeFormat: 'hh:mm:ss'
                    });
                });
                $('#category').on('change', function () {
                    $('#options').toggle(parseInt($(this).val()) !== 0);
                });
                $(document).on('click', '.add-option', function () {
                    $('#options tbody').append(pt.row);
                });
                $(document).on('click', '.remove-option', function () {
                    $(this).closest('tr').remove();
                });
            },
        };

        return {init: pt.init}
    }
})(jQuery);