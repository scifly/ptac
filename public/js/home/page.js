var oPage = {
    title: '',
    url: location.href
};
var updateHistory = true;
var replaceState = true;
var docTitle = '家校通';
// var rootDir = 'ptac/public/';
var rootDir = '';
var page = {
    success: 'img/confirm.png',
    failure: 'img/error.png',
    plugins: {
        datatable: {
            css: 'js/plugins/datatables/datatables.min.css',
            js: 'js/plugins/datatables/datatables.min.js'
        },
        select2: {
            css: 'js/plugins/select2/css/select2.min.css',
            js: 'js/plugins/select2/js/select2.full.min.js'
        },
        icheck: {
            css: 'js/plugins/icheck/all.css',
            js: 'js/plugins/icheck/icheck.min.js',
            selector: 'input[type="checkbox"].minimal, input[type="radio"].minimal',
            params: {
                checkboxClass: 'icheckbox_square-blue',
                radioClass: 'iradio_minimal-blue'
            }
        },
        jstree: {
            css: 'js/plugins/jstree/dist/themes/default/style.min.css',
            js: 'js/plugins/jstree/dist/jstree.min.js'
        },
        fullcalendar: {
            css: 'js/plugins/fullcalendar/fullcalendar.min.css',
            js: 'js/plugins/fullcalendar/fullcalendar.min.js'
        },
        fullcalendar_moment: {
            js: 'js/plugins/fullcalendar/lib/moment.min.js'
        },
        fullcalendar_locale: {
            js: 'js/plugins/fullcalendar/locale/zh-cn.js'
        },
        fileinput: {
            css: 'js/plugins/fileinput/css/fileinput.min.css',
            js: 'js/plugins/fileinput/js/fileinput.min.js'
        },
        fileinput_locale: {
            js: 'js/plugins/fileinput/js/locales/zh.js'
        },
        fileinput_theme: {
            css: 'js/plugins/fileinput/themes/explorer/theme.css',
            js: 'js/plugins/fileinput/themes/explorer/theme.js'
        },
        jqueryui: {
            css: 'js/plugins/jqueryui/jquery-ui.min.css',
            js: 'js/plugins/jqueryui/jquery-ui.min.js'
        },
        ueditor_config: {
            js: 'js/plugins/UEditor/ueditor.config.js'
        },
        ueditor_all: {
            js: 'js/plugins/UEditor/ueditor.all.js'
        }
    },
    backToList: function (table) {
        var $activeTabPane = $('#tab_' + page.getActiveTabId());
        page.getTabContent($activeTabPane, table + '/index');
    },
    inform: function (title, text, image) {
        $.gritter.add({title: title, text: text, image: page.siteRoot() + image});
    },
    siteRoot: function () {
        var siteRoot =  window.location.origin
            ? window.location.origin + '/'
            : window.location.protocol + '/' + window.location.host + '/';
        return siteRoot + rootDir;
    },
    ajaxLoader: function () {
        return "<img id='ajaxLoader' alt='' src='" + page.siteRoot() + "/img/throbber.gif' " +
            "style='vertical-align: middle;'/>"
    },
    getActiveTabId: function () {
        var tabId = $('.nav-tabs .active a').attr('href').split('_');
        return tabId[tabId.length - 1];
    },
    getTabContent: function ($tabPane, url) {
        if (url.indexOf('http://') > -1) {
            url = url.replace(page.siteRoot(), '');
        }
        var tabId = page.getActiveTabId();
        $('a[href="#tab_' + tabId + '"]').attr('data-uri', url);
        $tabPane.html(page.ajaxLoader);
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: page.siteRoot() + url,
            data: {tabId: tabId},
            success: function (result) {
                if (result.statusCode === 200) {
                    // $tabPane.html(result.html);
                    $('#ajaxLoader').after(result.html);
                    $.getScript(page.siteRoot() + result.js, function () {
                        $('#ajaxLoader').remove();
                    });
                    var breadcrumb = $('#breadcrumb').html();
                    document.title = docTitle + ' - ' + breadcrumb;
                    oPage.title = tabId;
                    oPage.url = page.siteRoot() + url;
                    if (updateHistory) {
                        if (replaceState) {
                            history.replaceState(oPage, oPage.title, oPage.url);
                        } else {
                            history.pushState(oPage, oPage.title, oPage.url);
                        }
                    }
                    replaceState = false;
                    updateHistory = true;
                } else {
                    window.location = 'login';
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var obj = JSON.parse(jqXHR.responseText);
                page.inform('出现异常', obj['message'], page.failure);
            }
        });
    },
    ajaxRequest: function (requestType, url, data, obj) {
        $.ajax({
            type: requestType,
            dataType: 'json',
            url: url,
            data: data,
            success: function (result) {
                if (result.statusCode === 200) {
                    switch (requestType) {
                        case 'POST':        // create
                            obj.reset();    // reset create form
                            break;
                        default:
                            break;
                    }
                }
                page.inform(
                    '操作结果', result.message,
                    result.statusCode === 200 ? page.success : page.failure
                );
                return false;
            },
            error: function (e) {
                var obj = JSON.parse(e.responseText);
                page.inform('出现异常', obj['message'], page.failure);
            }
        });
    },
    initDatatable: function (table, options) {

        if (typeof options === 'undefined') {
            options = [];
        }
        var showTable = function() {
            var $cip = $('#cip');
            var params = {
                processing: true,
                serverSide: true,
                ajax: page.siteRoot() + table + '/index',
                order: [[0, 'desc']],
                stateSave: true,
                autoWidth: true,
                columnDefs: options,
                scrollX: true,
                language: {url: '../files/ch.json'},
                lengthMenu: [[15, 25, 50, -1], [15, 25, 50, '所有']],
                dom: '<"row"<"col-md-6"l><"col-sm-4"f><"col-sm-2"B>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
                buttons: ['pdf', 'csv']
            };
            $cip.after($("<link/>", {
                rel: "stylesheet", type: "text/css",
                href: page.siteRoot() + page.plugins.datatable.css
            }));
            $('#data-table').dataTable(params).on('init.dt', function () {
                $('.dt-buttons').addClass('pull-right');
                $('.buttons-pdf').addClass('btn-sm');
                $('.buttons-csv').addClass('btn-sm');
                // $('.paginate_button').each(function() { $(this).addClass('btn-sm'); })
            });
        };
        if (!($.fn.dataTable)) {
            $.getMultiScripts([page.plugins.datatable.js], page.siteRoot()).done(function() { showTable(); });
        } else { showTable(); }
    },
    index: function (table, options) {
        this.unbindEvents();
        var $activeTabPane = $('#tab_' + page.getActiveTabId());

        // 显示记录列表
        this.initDatatable(table, options);
        // $('div.dataTables_length select').addClass('form-control');
        // $('div.dataTables_filter label').addClass('control-label');

        // 新增记录
        $('#add-record').on('click', function () {
            page.getTabContent($activeTabPane, table + '/create');
        });
        // 编辑记录
        $(document).on('click', '.fa-edit', function () {
            var url = $(this).parents().eq(0).attr('id');
            url = url.replace('_', '/');
            page.getTabContent($activeTabPane, table + '/' + url);
        });
        // 充值
        $(document).on('click', '.fa-money', function () {
            var url = $(this).parents().eq(0).attr('id');
            url = url.replace('_', '/');
            page.getTabContent($activeTabPane, table + '/' + url);
        });
        // 查看记录详情
        $(document).on('click', '.fa-eye', function () {
            var url = $(this).parents().eq(0).attr('id');
            url = url.replace('_', '/');
        });
        // 删除记录
        var id;
        $(document).on('click', '.fa-trash', function () {
            id = $(this).parents().eq(0).attr('id');
            $('#modal-dialog').modal({backdrop: true});
        });
        $('#confirm-delete').on('click', function () {
            this.ajaxRequest(
                'DELETE', page.siteRoot() + '/' + table + '/delete/' + id,
                {_token: $('#csrf_token').attr('content')}
            );
        });
    },
    create: function (formId, table) {
        page.initForm(table, formId, table + '/store', 'POST');
    },
    edit: function (formId, table) {
        var id = $('#id').val();
        page.initForm(table, formId, table + '/update/' + id, 'PUT');
    },
    recharge: function (formId, table) {
        var id = $('#id').val();
        page.initForm(table, formId, table + '/rechargeStore/' + id, 'PUT');
    },
    initForm: function (table, formId, url, requestType) {
        this.unbindEvents();
        var $form = $('#' + formId);
        var $cip = $('#cip');
        // Cancel button
        $('#cancel, #record-list').on('click', function() {
            page.backToList(table);
        });
        var initPlugins = function() {
            $cip.after($("<link/>", {
                rel: "stylesheet", type: "text/css",
                href: page.siteRoot() + page.plugins.select2.css
            })).after($("<link/>", {
                rel: "stylesheet", type: "text/css",
                href: page.siteRoot() + page.plugins.icheck.css
            }));
            $('select').select2();
            page.initICheck();
            page.initParsley($form, requestType, url);
            return false;
        };

        if (!($.fn.select2) || !($.fn.iCheck)) {
            var scripts = [
                page.plugins.select2.js,
                page.plugins.icheck.js
            ];
            $.getMultiScripts(scripts, page.siteRoot()).done(function() { initPlugins(); });
        } else { initPlugins(); }
    },
    initICheck: function (object) {
        if (typeof object === 'undefined') {
            $(page.plugins.icheck.selector).iCheck(page.plugins.icheck.params);
        } else {
            object.find(page.plugins.icheck.selector).iCheck(page.plugins.icheck.params);
        }
    },
    initParsley: function ($form, requestType, url) {
        $form.parsley().on('form:validated', function () {
            if ($('.parsley-error').length === 0) {
                this.ajaxRequest(requestType, page.siteRoot() + url, $form.serialize(), $form[0]);
            }
        }).on('form:submit', function () {
            return false;
        });
    },
    unbindEvents: function () {
        $('#add-record').unbind('click');
        $(document).off('click', '.fa-edit');
        $(document).off('click', '.fa-eye');
        $(document).off('click', '.fa-trash');
        $(document).off('click', '.fa-money');
        $('#confirm-delete').unbind('click');
    },
    getUrlVars: function () {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        for (var i = 0; i < hashes.length; i++) {
            hash = hashes[i].split('=');
            vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }
};
$.getMultiScripts = function(arr, path) {
    var _arr = $.map(arr, function(scr) {
        return $.getScript( (path||"") + scr );
    });
    _arr.push($.Deferred(function( deferred ){
        $( deferred.resolve );
    }));
    return $.when.apply($, _arr);
};
$(function () {
    // 激活菜单
    var $active = $('.sidebar-menu li.active');
    var parents = $active.parentsUntil('.sidebar-menu');
    if (typeof parents !== 'undefined') {
        $(parents[parents.length - 1]).addClass('active');
        for (var i = 0; i < parents.length; i++) {
            if ($(parents[i]).is('ul')) {
                $(parents[i]).addClass('menu-open').css('display', 'block');
            } else {
                $(parents[i]).addClass('active')
            }
        }
    }
    // 获取状态为active的卡片
    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    window.onpopstate = function (e) {
        if (!e.state) { return false; }
        oPage.title = e.state.title;
        oPage.url = e.state.url;
        // deactivate current pane
        var activeTabId = page.getActiveTabId();
        $('a[href="#tab_' + activeTabId + '"]').parent().removeClass();
        $activeTabPane = $('#tab_' + activeTabId);
        $activeTabPane.removeClass('active').html('');
        // activate targe pane
        $('a[href="#tab_' + oPage.title + '"]').parent().addClass('active');
        $activeTabPane = $('#tab_' + oPage.title);
        $activeTabPane.addClass('active');
        updateHistory = false;
        page.getTabContent($activeTabPane, oPage.url);
    };
    $(document).on('click', '.tab', function () {
        // 获取被点击卡片的url
        var url = $(this).attr('data-uri');
        // 获取所有卡片
        var $tabPanes = $('.card');
        // 获取状态为active的卡片
        var $activeTabPane = $('#tab_' + page.getActiveTabId());
        // 如果状态为active的卡片的内容为空, 清空其他卡片的内容
        if ($activeTabPane.html() === '') {
            // 清空所有卡片的内容
            $.each($tabPanes, function () {
                $(this).html('');
            });
            // 获取状态为active的卡片内容
            page.getTabContent($activeTabPane, url);
        }
    });
    // 获取状态为active的卡片的url
    url = $('.nav-tabs .active a').attr('data-uri');
    // 获取状态为active的卡片内容
    page.getTabContent($activeTabPane, url);
});