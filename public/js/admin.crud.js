var crud = {
    $tbody: function () { return $("#mobiles").find("tbody"); },
    mobiles: function (formId, requestType, ajaxUrl) {
        // icheck init
        page.initICheck(crud.$tbody());

        crud.$tbody().find('tr:not(:last) .btn-mobile-add')
            .removeClass('btn-mobile-add').addClass('btn-mobile-remove')
            .html('<i class="fa fa-minus text-blue"></i>');
        var $mobile = crud.$tbody().find('tr:last input[class="form-control"]');
        var $form = $('#' + formId);
        $form.parsley().destroy();
        $mobile.attr('pattern', '/^1[0-9]{10}$/');
        $mobile.attr('required', 'true');
        page.initParsley($form, requestType, ajaxUrl);
    },
    mobile: function (formId, size, requestType, ajaxUrl) {
        $(document).off('click', '.btn-mobile-add');
        $(document).off('click', '.btn-mobile-remove');
        $(document).on('click', '.btn-mobile-add', function (e) {
            e.preventDefault();
            // add html
            size++;
            crud.$tbody().append(
                '<tr>' +
                    '<td>' +
                        '<div class="input-group">' +
                            '<div class="input-group-addon">' +
                                '<i class="fa fa-mobile"></i>' +
                            '</div>' +
                            '<input class="form-control" placeholder="（请输入手机号码）" name="mobile[' + size + '][mobile]" value="">' +
                        '</div>' +
                    '</td>' +
                    '<td style="text-align: center">' +
                        '<input type="radio" class="minimal" id="mobile[isdefault]" name="mobile[isdefault]" value="' + size + '">' +
                    '</td>' +
                    '<td style="text-align: center">' +
                        '<input type="checkbox" class="minimal" name="mobile[' + size + '][enabled]">' +
                    '</td>' +
                    '<td style="text-align: center">' +
                        '<button class="btn btn-box-tool btn-add btn-mobile-add" type="button">' +
                            '<i class="fa fa-plus text-blue"></i>' +
                        '</button>' +
                    '</td>' +
                '</tr>'
            );
            crud.mobiles(formId, requestType, ajaxUrl);
        }).on('click', '.btn-mobile-remove', function (e) {
            $(this).parents('tr:first').remove();
            e.preventDefault();
            var $defaults = $('input[name="mobile[isdefault]"]');
            var defaultChecked = false;
            $.each($defaults, function () {
                if (typeof $(this).attr('checked') !== 'undefined') {
                    defaultChecked = true;
                    return false;
                }
            });
            if (!defaultChecked) {
                $($defaults[0]).iCheck('check');
            }
            return false;
        });
    }
};
