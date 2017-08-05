var formUrl = '', currentJsTree;
$(function() {
    var $treeview = $('#jstree-menu');

    /*$('select').select2();
    Switcher.init();*/
    // 表单提交
    $('#save').click(function(){
        var $jstree = $('#jstree-menu');
        $('#formMenu').parsley().on('form:validated', function() {
            $.ajax({
                type: 'PUT',
                dataType: 'json',
                url: formUrl,
                data: $('#formMenu').serialize(),
                success: function(result) {
                    $.gritter.add({
                        title: '操作结果',
                        text: result.message
                    });
                    $('#menuFormData').hide();
                    $jstree.removeClass('col-md-6').addClass('col-md-12');
                    $jstree.jstree().refresh();
                }
            });
        });
    });

    $('#cancel').on('click', function() {
        $('#menuFormData').hide();
        $('#jstree-menu').removeClass('col-md-6').addClass('col-md-12');
    });

    // 文件上传
    /*
    $('#buttonUpload').click(function() {
        var image_id = $('#nodepic').val();
        $.ajaxFileUpload({
            url: '/categories/upload' + (image_id.length ? '/' + image_id : ''),
            secureuri: false,
            fileElementId: 'nodepicupload',
            dataType: 'json',
            success: function(data) {
                if (data['image_url'] != '') {
                    $('#picDiv').show();
                    $('#showPicture').html('<img src="' + data['image_url'] + '" style="max-width:300px"/>');
                    $('#nodepic').val(data['image_id']);
                } else {
                    $.gritter.add({
                        title: "上传失败",
                        text: '文件上传失败，请稍后再试'
                    });
                }
            },
            error: function() {
                $.gritter.add({
                    title: "网络异常",
                    text: '文件上传失败，不能连接到上传服务器，请检查网络'
                });
            }
        });
    });
    */

    // 初始化SELECT选项
    /*$.post('/providers/index?source=select', {}, function(data){
        $.each(data.items, function(index, value) {
            $('#nodeprovider').append(
                '<option value="' +
                data.items[index].id + '">' +
                data.items[index].text + '</option>'
            );
        });
        $('#nodeprovider').select2({
            placeholder: "请选择分类所属商家",
            minimumInputLength: 0,
            allowClear: true,
            language: "zh-CN",
            width: '100%',
            ajax: {
                url: '/providers/index?source=select',
                dataType: 'json',
                data: function(params) {
                    return {
                        q: params.term,
                        page: params.page
                    };
                },
                processResults: function(data) {
                    return { results: data.items };
                },
                cache: false
            }
        });
    }, 'json');*/

    currentJsTree = $treeview.jstree({
        core: {
            themes: {
                variant: 'large',
                dots: true,      // this setting is conflict with 'wholerow' plugin
                icons: false,
                stripes: true
            },
            expand_selected_onload: true,
            check_callback: true,
            multiple: false,
            animation: 0,
            data: {
                url: '/urlshortener/public/menus/index',
                type: 'POST',
                dataType: 'json',
                // contentType: 'application/json;',
                data: function(node) {
                    return { id: node.id, _token: $('#csrf_token').attr('content') };
                }
            }
        },
        plugins: ['contextmenu', 'dnd', 'wholerow'],
        contextmenu: { items: customMenu }
    }).on('loaded.jstree', function() {
        $treeview.jstree('open_all')
    }).on('move_node.jstree', function(e, data){
        console.info(e);
        console.info(data);
        $.post('/urlshortener/public/menus/update?type=move', {
            Menu: {
                id: data.node.id,
                parent_id: data.node.parent
            }
        }, function(data){}, 'json');
    });
});

function customMenu() {
    return {
        createItem: {
            label: '创建',
            action: function(node) {
                var selector = $.jstree.reference(node.reference).get_node(node.reference);
                var $treeview = $('#jstree-menu');
                var $nodeid = $('#nodeid');
                formUrl = '/urlshortener/public/menus/store';
                $nodeid.val('');
                $('#nodename').val('');
                $('#nodemaker').val('');
                $('#nodestatus').find('option').removeAttr('selected');
                $('#nodepic').val('');
                $('#showPicture').html('');
                $('#picDiv').hide();
                $treeview.removeClass('col-md-12').addClass('col-md-6');
                $nodeid.after('<input type="hidden" name="Menu[parent_id]" id="nodeparentid" />');
                $('#nodeparentid').val(selector.id);
                $('#menuFormData').show();
            }
        },
        renameItem: {
            label: '修改',
            action: function(node){
                var selector = $.jstree.reference(node.reference).get_node(node.reference);
                // 获取选择节点的所有详细信息
                var $nodeid = $('#nodeid');
                var $nodestatus = $('#nodestatus').find('option');
                var $treeview = $('#jstree-menu');

                formUrl = '/urlshortener/public/menus/edit';
                $treeview.removeClass('col-md-12').addClass('col-md-6');
                $.post('/urlshortener/public/menus/update?type=view', {id: selector.id}, function(data){
                    $('#menuFormData').show();
                    $('#nodename').val(data.Menu.name);
                    $nodeid.val(data.Menu.id);
                    $('#nodepic').val(data.Menu['media_id']);
                    $('#nodemaker').val(data.Menu['remark']);
                    // $('#nodeprovider').select2('val', data.Category['provider_id']);
                    $nodeid.val(data.Menu.id);
                    $('#nodeparentid').remove();
                    if (data.Menu.enabled === false) {
                        $nodestatus.eq(0).attr('selected', 'selected');
                    } else {
                        $nodestatus.eq(1).attr('selected', 'selected');
                    }

                    if (data.Menu['media_id'] !== null) {
                        $('#showPicture').html(
                            '<img src="' +
                            data.Image['image_url'] +
                            '" style="max-width:300px;" />'
                        );
                        $('#picDiv').show();
                    } else {
                        $('#picDiv').hide();
                        $('#showPicture').html('');
                    }
                }, 'json');

            }
        },
        deleteItem: {
            label: '删除',
            action: function(node){
                var selector = $.jstree.reference(node.reference).get_node(node.reference);
                var $dialog = $('#modal-dialog');

                $dialog.modal({ backdrop: true });
                $dialog.find('#confirm-delete').on('click', function() {
                    $.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: '/urlshortener/public/menus/delete/' + selector.id,
                        success: function(result) {
                            $.gritter.add({
                                title: "删除结果",
                                text: result.message
                            });
                            return false;
                        }
                    });
                });
            }
        }
    };

}