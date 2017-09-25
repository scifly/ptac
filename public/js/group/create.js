$(crud.create('formGroup','groups'));
var $menuTree = $('#menu_tree');
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
            url: page.siteRoot() + 'groups/create',
            type: 'POST',
            dataType: 'json',
            data: function (node) {
                return {id: node.id, _token: $('#csrf_token').attr('content')}
            }
        }
    },
    checkbox: {
        // keep_selected_style : false,
        // three_state: false
    },
    plugins: ['types', 'search', 'checkbox', 'wholerow'],
    types: tree.nodeTypes
}).on('select_node.jstree', function(node, selected) {
}).on('deselect_node.jstree', function (node, selected) {
}).on('loaded.jstree', function () {
    $menuTree.jstree('open_all');
});
$('.collapsed-box').boxWidget('collapse');
$(document).on('ifChecked', '.tabs', function(e) {
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
$(document).on('ifUnchecked', '.tabs', function() {
    var $actionContainer = $(this).parentsUntil($('.box .box-default'), '.box-header').next();
    $actionContainer.find('input').each(function() {
        $(this).iCheck('uncheck');
    });
});
$(document).on('ifUnchecked', '.actions', function() {
    var $tabContainer = $(this).parentsUntil($('.col-md-3'), '.box .box-default').find('.box-header');
    var checks = 0;
    $(this).parents().eq(2).siblings().each(function() {
        checks += $(this).find('div[aria-checked="true"]').length
    });
    if (!checks) {
        $tabContainer.find('input').iCheck('uncheck');
    }
});
$(document).on('ifChecked', '.actions', function(e) {
    var $tabContainer = $(this).parentsUntil($('.col-md-3'), '.box .box-default').find('.box-header');
    $tabContainer.find('input').iCheck('check');
});
