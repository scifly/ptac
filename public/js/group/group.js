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
            init: function () {
                page.unbindEvents();
                // load menu tree
                group.loadTree();
                page.initICheck();
                page.initSelect2();
                page.initBackBtn('groups');
                group.initForm();
                // on school changed
                $('#' + group.options.schoolId).on('change', function() {
                    group.loadTree();
                });
                $('.collapsed-box').boxWidget('collapse');
                group.ifTabChecked();
                group.ifTabUnChecked();
                group.ifActionChecked();
                group.ifActionUnChecked();
                $.tree().initTree(group.loadTree);
            },
            loadTree: function () {
                var $menuTree = $('#' + group.options.menuTree),
                    $schoolId = $('#' + group.options.schoolId),
                    url = page.siteRoot() + group.options.table,
                    $id = $('#id');

                $.jstree.destroy();
                $('.overlay').show();
                $('a[href="#tab02"]').html(page.ajaxLoader());

                if ($id.length === 0) {
                    url += '/create?schoolId=' + $schoolId.val();
                } else {
                    url += '/edit/' + $id.val() + '?schoolId=' + $schoolId.val();
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
                                    _token: page.token()
                                }
                            }
                        }
                    },
                    checkbox: {
                        // keep_selected_style : false,
                        three_state: false
                    },
                    plugins: ['types', 'search', 'checkbox', 'wholerow'],
                    types: $.tree().options.departmentTypes
                }).on('select_node.jstree', function(node, selected) {
                }).on('deselect_node.jstree', function (node, selected) {
                }).on('loaded.jstree', function () {
                    if ($id.length === 1) {
                        var menuIds = $('#menu_ids').val().split(',');
                        $menuTree.jstree().select_node(menuIds);
                    }
                    $menuTree.jstree('open_all');
                    $('a[href="#tab02"]').html('菜单权限');
                    $('.overlay').hide();
                });
            },
            initForm: function () {
                var $menuTree = $('#' + group.options.menuTree),
                    $form = $('#' + group.options.formGroup);

                $form.parsley().on('form:validated', function () {
                    if ($('.parsley-error').length === 0) {
                        var $id = $('#id'),
                            url = page.siteRoot() + group.options.table +
                                ($id.length === 0 ? '/store' : ('/update/' + $id.val())),
                            menuIds = $menuTree.jstree().get_selected(),    // 选定的菜单id
                            tabIds = [],    // 选定的卡片id
                            actionIds = []; // 选定的功能id

                        $menuTree.find(".jstree-undetermined").each(function (i, element) {
                            menuIds.push($(element).parents().eq(1).attr('id'));
                        });
                        $('.tabsgroup').find('.checked').find('.minimal').each(function () {
                            tabIds.push($(this).val());
                        });
                        $('.actionsgroup').find('.checked').find('.minimal').each(function () {
                            actionIds.push($(this).val());
                        });
                        $('#menu_ids').val(menuIds.join());
                        $('#tab_ids').val(tabIds.join());
                        $('#action_ids').val(actionIds.join());
                        $.ajax({
                            type: 'POST',
                            dataType: 'json',
                            url: url,
                            data: page.formData($form), // $form.serialize(),
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
                    var $actionContainer = $(this).parentsUntil($('.box .box-default'), '.box-header').next(),
                        checkAll = true;

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

        return { init: group.init }
    }
})(jQuery);