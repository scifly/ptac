
var data;
var $menuTree = $('#menu_tree');
// Switchery
Switcher.init();

// iCheck
crud.initICheck();
var id = $('#id').val();
// Cancel button
$('#cancel, #record-list').on('click', function () {
    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    page.getTabContent($activeTabPane, 'groups/index');
    crud.unbindEvents();
});

// Parsley
var $form = $('#formGroup');
// crud.formParsley($form, requestType, ajaxUrl);
$form.parsley().on('form:validated', function () {
    var data = $form.serialize();

    if ($('.parsley-error').length === 0) {
        var url =  page.siteRoot() + 'groups/update/'+id;
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
                if (result.statusCode === 200){
                    page.inform('操作结果',result.message, page.success)
                }else{
                    page.inform('操作结果',result.message, page.failure)
                }
            },
            error:function () {
                var obj = JSON.parse(e.responseText);
                page.inform('出现异常', obj['message'], page.failure);
            }
        });
        // crud.ajaxRequest(requestType, page.siteRoot() + 'groups/store', data, $form[0]);
    }
}).on('form:submit', function() {return false; });

loadTree($('#school_id').val());

$('.menu').change(function(){
    var school_id = $(this).val();
    loadTree(school_id);
});

function loadTree(schoolId) {
    $.jstree.destroy();
    $('a[href="#tab02"]').html(page.ajaxLoader());
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
                url: page.siteRoot() + 'groups/edit/'+id+'?schoolId=' + schoolId,
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
        $('a[href="#tab02"]').html('菜单权限');
    });
}

/*
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
            url: page.siteRoot() + 'groups/edit/'+id,
            type: 'POST',
            dataType: 'json',
            data: function (node) {
                return {id: node.id, _token: $('#csrf_token').attr('content')}
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
    var menuIds = $('#menu_ids').val().split(',');
    console.log(menuIds);
    $menuTree.jstree().select_node(menuIds);
    $menuTree.jstree('open_all');
});
*/

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
