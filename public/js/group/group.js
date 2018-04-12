//# sourceURL=group.js
(function ($) {
    $.group = function (options) {
        var group = {
            options: $.extend({
                menuTree: 'menu_tree',
                formGroup: 'formGroup',
                schoolId: 'school_id',
                table: 'groups',
            }, options),
            create: function () {
                group.init('create');
            },
            edit: function () {
                group.init('edit');
            },
            init: function (action) {
                page.unbindEvents();
                // load menu tree
                group.loadTree(action);
                page.initICheck();
                page.initSelect2();
                page.initBackBtn('groups');
                group.initForm(action);
                // on school changed
                $('#' + group.options.schoolId).on('change', function() {
                    group.loadTree(action);
                });
                $('.collapsed-box').boxWidget('collapse');
                group.ifTabChecked();
                group.ifTabUnChecked();
                group.ifActionChecked();
                group.ifActionUnChecked();
                tree.initJsTree(group.loadTree(action));
            },
            token: function () {
                return $('#csrf_token').attr('content');
            },
            loadTree: function (action) {
                var $menuTree = $('#' + group.options.menuTree),
                    $schoolId = $('#' + group.options.schoolId),
                    url = page.siteRoot() + group.options.table;

                $.jstree.destroy();
                $('.overlay').show();
                $('a[href="#tab02"]').html(page.ajaxLoader());

                if (action === 'create') {
                    url += '/create?schoolId=' + $schoolId.val();
                } else {
                    url += '/edit/' + $('#id').val() + '?schoolId=' + $schoolId.val();
                }
                $menuTree.jstree({
                    core: {
                        themes: {
                            variant: 'large',
                            dots: true,
                            icons: false,
                            stripes: true
                        },
                        multiple: true,
                        animation: 0,
                        data: {
                            url: url,
                            type: 'POST',
                            dataType: 'json',
                            data: function (node) {
                                return {
                                    id: node.id,
                                    _token: group.token()
                                }
                            }
                        }
                    },
                    checkbox: {
                        // keep_selected_style : false,
                        three_state: false
                    },
                    plugins: ['types', 'search', 'checkbox', 'wholerow'],
                    types: tree.nodeTypes
                }).on('select_node.jstree', function(node, selected) {
                }).on('deselect_node.jstree', function (node, selected) {
                }).on('loaded.jstree', function () {
                    if (action === 'edit') {
                        var menuIds = $('#menu_ids').val().split(',');
                        $menuTree.jstree().select_node(menuIds);
                    }
                    $menuTree.jstree('open_all');
                    $('a[href="#tab02"]').html('菜单权限');
                    $('.overlay').hide();
                });
            },
            initForm: function (action) {
                var $menuTree = $('#' + group.options.menuTree),
                    $form = $('#' + group.options.formGroup);

                $form.parsley().on('form:validated', function () {
                    if ($('.parsley-error').length === 0) {
                        var url = page.siteRoot() + group.options.table +
                            (action === 'create' ? '/store' : ('/update/' + $('#id').val())),
                            menuIds = $menuTree.jstree().get_selected();

                        $menuTree.find(".jstree-undetermined").each(function (i, element) {
                            menuIds.push($(element).parents().eq(1).attr('id'));
                        });
                        $('#menu_ids').val(menuIds.join());
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: url,
                            data: $form.serialize(),
                            success: function(result) {
                                page.inform(result.title, result.message, page.success);
                            },
                            error:function (e) {
                                page.errorHandler(e);
                            }
                        });
                    }
                }).on('form:submit', function() { return false; });
            },
            ifTabChecked: function () {
                $(document).on('ifChecked', '.tabs', function() {
                    var $actionContainer = $(this).parentsUntil($('.box .box-default'), '.box-header').next();
                    var checkAll = true;
                    $actionContainer.find('input').each(function() {
                        if ($(this).iCheck('update')[0].checked) {
                            checkAll = false;
                            return false;
                        }
                    });
                    if (checkAll) {
                        $actionContainer.find('input').each(function() {
                            $(this).iCheck('check');
                        });
                    }
                });
            },
            ifTabUnChecked: function () {
                $(document).on('ifUnchecked', '.tabs', function() {
                    var $actionContainer = $(this).parentsUntil($('.box .box-default'), '.box-header').next();
                    $actionContainer.find('input').each(function() {
                        $(this).iCheck('uncheck');
                    });
                });
            },
            ifActionChecked: function () {
                $(document).on('ifChecked', '.actions', function() {
                    var $tabContainer = $(this).parentsUntil($('.col-md-3'), '.box .box-default').find('.box-header');
                    $tabContainer.find('input').iCheck('check');
                    var method = $(this).attr('data-method');
                    if (method !== 'index') {
                        $tabContainer.next().find('input[data-method="index"]').iCheck('check');
                    }
                    switch (method) {
                        case 'create':
                            $tabContainer.next().find('input[data-method="store"]').iCheck('check');
                            break;
                        case 'store':
                            $tabContainer.next().find('input[data-method="create"]').iCheck('check');
                            break;
                        case 'edit':
                            $tabContainer.next().find('input[data-method="update"]').iCheck('check');
                            break;
                        case 'update':
                            $tabContainer.next().find('input[data-method="edit"]').iCheck('check');
                            break;
                        default: break;
                    }
                });
            },
            ifActionUnChecked: function () {
                $(document).on('ifUnchecked', '.actions', function() {
                    var $tabContainer = $(this).parentsUntil($('.col-md-3'), '.box .box-default').find('.box-header'),
                        checks = 0;

                    $(this).parents().eq(2).siblings().each(function() {
                        checks += $(this).find('div[aria-checked="true"]').length
                    });
                    if (!checks) {
                        $tabContainer.find('input').iCheck('uncheck');
                    } else {
                        var method = $(this).attr('data-method');
                        switch (method) {
                            case 'index':
                                $tabContainer.next().find('input').iCheck('uncheck');
                                break;
                            case 'create':
                                $tabContainer.next().find('input[data-method="store"]').iCheck('uncheck');
                                break;
                            case 'store':
                                $tabContainer.next().find('input[data-method="create"]').iCheck('uncheck');
                                break;
                            case 'edit':
                                $tabContainer.next().find('input[data-method="update"]').iCheck('uncheck');
                                break;
                            case 'update':
                                $tabContainer.next().find('input[data-method="edit"]').iCheck('uncheck');
                                break;
                            default: break;
                        }
                    }
                });
            }
        };

        return {
            create: group.create,
            edit: group.edit
        }
    }
})(jQuery);