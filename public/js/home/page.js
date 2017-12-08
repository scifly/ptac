var oPage = { title: '', url: location.href};
var updateHistory = true;
var replaceState = true;
var docTitle = '家校通';
var $cip = $('#cip');
var page = {
    success: 'img/confirm.png',
    failure: 'img/error.png',
    info: 'img/info.png',
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
        },
        datepicker: {
            css: 'js/plugins/datepicker/datepicker3.css',
            js: 'js/plugins/datepicker/bootstrap-datepicker.js'
        },
        timepicker: {
            css: 'js/plugins/jqueryui/css/jquery-ui.css',
            js: 'js/plugins/jqueryui/js/jquery-ui-timepicker-addon.js',
            jscn: 'js/plugins/jqueryui/js/datepicker-zh-CN.js'
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
        if (window.location.href.indexOf('public') > -1) {
            return siteRoot + 'pppp/public/';
        }
        return siteRoot;
    },
    ajaxLoader: function () {
        return "<img id='ajaxLoader' alt='' src='" + page.siteRoot() + "/img/throbber.gif' " +
            "style='vertical-align: middle;'/>"
    },
    formatState: function(state) {
        if (!state.id) { return state.text; }
        return $('<span><i class="' + state.text + '"> ' + state.text + '</span>');
    },
    refreshMenus: function() {
        var $active = $('.sidebar-menu li.active');
        var parents = $active.parentsUntil('.sidebar-menu');
        if (typeof parents !== 'undefined') {
            $(parents[parents.length - 1]).addClass('active');
            for (var i = 0; i < parents.length; i++) {
                if ($(parents[i]).is('ul')) {
                    $(parents[i]).addClass('menu-open').css('display', 'block');
                } else {
                    $(parents[i]).addClass('active');
                }
            }
        }
    },
    refreshTabs: function() {
        var $tabs = $('a.tab');
        $.each($tabs, function() {
            $(this).removeClass('text-blue').addClass('text-gray');
        });
        $('li.active a.tab').removeClass('text-gray').addClass('text-blue');
    },
    getActiveTabId: function () {
        var $activeTab = $('.nav-tabs .active a');
        if ($activeTab.length !== 0) {
            var tabId = $activeTab.attr('href').split('_');
            return tabId[tabId.length - 1];
        }
        return false;
    },
    getActiveMenuId: function() {
        return $('.sidebar-menu li.active').last().find('a').attr('id');
    },
    getTabUrl: function() {
        var url = window.location.href;
        return encodeURIComponent(url + this.getQuerySting());
    },
    getMenuUrl: function() {
        return page.siteRoot() + $('.sidebar-menu li.active').last().find('a').attr('href');
    },
    getQuerySting: function() {
        return '?menuId=' + this.getActiveMenuId() + '&tabId=' + this.getActiveTabId();
    },
    errorHandler: function (e) {
        var obj = JSON.parse(e.responseText);
        $('.overlay').hide();
        switch (obj['statusCode']) {
            case 400:
                var response = JSON.parse(e.responseText);
                var errors = response['errors'];
                $.each(errors, function() {
                    page.inform('验证错误', this, page.failure);
                });
                break;
            case 401:
                page.inform('重新登录', '会话已过期，请重新登录', page.info);
                window.location = page.siteRoot() + 'login?returnUrl=' +
                    (typeof obj['returnUrl'] !== 'undefined'
                    ? encodeURIComponent(obj['returnUrl'])
                    : this.getTabUrl());
                break;
            case 498:
                window.location.reload();
                break;
            default:
                page.inform('出现异常', obj['message'], page.failure);
                break;
        }
    },
    getWrapperContent: function(menuId, menuUrl, tabId, tabUrl) {
        var $wrapper = $('.content-wrapper');
        $('.overlay').show();
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: menuUrl,
            data: { menuId: menuId },
            success: function(result) {
                switch (result.statusCode) {
                    case 200:
                        $wrapper.html(result.html);
                        $('.overlay').hide();
                        // 获取状态为active的卡片内容
                        var $tab = null;
                        var tabUri = $('.nav-tabs .active a').attr('data-uri');
                        if (typeof tabUri !== 'undefined') {
                            // Wrapper中的Html包含卡片，获取卡片中的Html
                            if (typeof tabId !== 'undefined') {
                                var $tabPanes = $('.card');
                                $.each($tabPanes, function() { $(this).html(''); });
                                $('.nav-tabs .active').removeClass('active');
                                $('.active .card').removeClass('active');
                                $('a[href="#tab_' + tabId + '"]').parent().addClass('active');
                                $tab = $('#tab_' + tabId);
                                $tab.addClass('active');
                                page.refreshTabs();
                            } else {
                                tabId = page.getActiveTabId();
                                $tab = $('#tab_' + tabId);
                            }
                            if (typeof tabUrl !== 'undefined') {
                                tabUri = tabUrl;
                            }
                            // 初始化鼠标悬停特效
                            $('.tab').hover(
                                function() { $(this).removeClass('text-gray').addClass('text-blue'); },
                                function() {
                                    if (!($(this).parent().hasClass('active'))) {
                                        $(this).removeClass('text-blue').addClass('text-gray');
                                    }
                                }
                            );
                            // 获取当前卡片中的HTML
                            page.getTabContent($tab, tabUri);
                        } else {
                            // Wrapper中的Html不含卡片，更新浏览器History
                            document.title = docTitle + ' - ' + result['title'];
                            // 0 - tabId, 1 - menuId, 2 - menuUrl
                            oPage.title = '0,' + page.getActiveMenuId() + ',' + page.getMenuUrl();
                            oPage.url = page.siteRoot() + result['uri'];
                            if (updateHistory) {
                                if (replaceState) {
                                    history.replaceState(oPage, oPage.title, oPage.url);
                                } else {
                                    history.pushState(oPage, oPage.title, oPage.url);
                                }
                            }
                            replaceState = false;
                            updateHistory = true;
                        }
                        break;
                    case 401:
                        window.location = page.siteRoot() + 'login?returnUrl=' + page.getMenuUrl();
                        break;
                    default:
                        break;
                }
            },
            error: function(e) { page.errorHandler(e); }
        });
    },
    getTabContent: function ($tabPane, url) {
        if (url.indexOf('http://') > -1) {
            url = url.replace(page.siteRoot(), '');
        }
        var tabId = page.getActiveTabId();
        var menuId = page.getActiveMenuId();
        $('a[href="#tab_' + tabId + '"]').attr('data-uri', url);
        $tabPane.html(page.ajaxLoader);
        $('.overlay').show();
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: page.siteRoot() + url,
            data: {tabId: tabId, menuId: menuId},
            success: function (result) {
                if (result.statusCode === 200) {
                    // 在当前已激活卡片中加载服务器返回的HTML
                    $tabPane.html(result.html);
                    $('.overlay').show();
                    // $('#ajaxLoader').after(result.html);
                    // 动态加载服务器返回的链接地址指向的js脚本
                    $.getScript(page.siteRoot() + result.js, function () {
                        $('#ajaxLoader').remove();
                        $('.overlay').hide();
                        // 移除当前页面的datatable.css
                        // if (!$('#data-table').length) {
                        //     $('link[href="' + page.siteRoot() + page.plugins.datatable.css +'"]').remove();
                        // }
                    });
                    // 更新浏览器抬头
                    var breadcrumb = $('#breadcrumb').html();
                    document.title = docTitle + ' - ' + breadcrumb;
                    // 更新浏览器访问历史
                    // 0 - tabId, 1 - menuId, 2 - menuUrl
                    oPage.title = tabId + ',' + page.getActiveMenuId() + ',' + page.getMenuUrl();
                    oPage.url = page.siteRoot() + url;
                    // 如果需要更新浏览器历史
                    if (updateHistory) {
                        if (replaceState) {
                            // 替换当前会话中的第一条访问记录
                            history.replaceState(oPage, oPage.title, oPage.url);
                        } else {
                            // 新增一条浏览器访问记录
                            history.pushState(oPage, oPage.title, oPage.url);
                        }
                    }
                    replaceState = false;
                    updateHistory = true;
                } else {
                    window.location = page.siteRoot() + 'login';
                }
            },
            error: function (e) { page.errorHandler(e); }
        });
    },
    ajaxRequest: function (requestType, url, data, obj) {
        $('.overlay').show();
        $.ajax({
            type: requestType,
            dataType: 'json',
            url: url,
            data: data,
            success: function (result) {
                switch (result.statusCode) {
                    case 200:
                        switch (requestType) {
                            case 'POST':        // create
                                obj.reset();    // reset create form
                                break;
                            case 'DELETE':
                                $('#data-table').dataTable().fnDestroy();
                                page.initDatatable(obj);
                                break;
                            default:
                                break;
                        }
                        $('.overlay').hide();
                        page.inform(
                            '操作结果', result.message,
                            result.statusCode === 200 ? page.success : page.failure
                        );
                        break;
                    case 401:
                        window.location = page.siteRoot() + 'login?returnUrl=' + page.getTabUrl();
                        break;
                    default:
                        break;
                }
            },
            error: function (e) { page.errorHandler(e); }
        });
    },
    initDatatable: function (table, options) {
        var showTable = function() {
            var $datatable = $('#data-table');
            var columns = $datatable.find('thead tr th').length;
            var statusCol = {className: 'text-right', targets: [columns - 1]};
            if (typeof options === 'undefined') {
                options = [statusCol];
            } else {
                options.push(statusCol);
            }
            var params = {
                processing: true,
                serverSide: true,
                ajax: {
                    url: page.siteRoot() + table + '/index' + page.getQuerySting(),
                    error: function (e) { page.errorHandler(e); }
                },
                order: [[0, 'desc']],
                stateSave: true,
                autoWidth: true,
                columnDefs: options,
                scrollX: true,
                language: {url: '../files/ch.json'},
                lengthMenu: [[15, 25, 50, -1], [15, 25, 50, '所有']]
                // dom: '<"row"<"col-md-6"l><"col-sm-4"f><"col-sm-2"B>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
                // buttons: ['pdf', 'csv']
            };
            page.loadCss(page.plugins.datatable.css);
            $('.overlay').show();
            $.fn.dataTable.ext.errMode = 'none';
            var dt = $datatable.DataTable(params).on('init.dt', function () {
                // $('.dt-buttons').addClass('pull-right');
                // $('.buttons-pdf').addClass('btn-sm');
                // $('.buttons-csv').addClass('btn-sm');
                // $('.paginate_button').each(function() { $(this).addClass('btn-sm'); })
                $('input[type="search"]').attr('placeholder', '多关键词请用空格分隔');
                $('.overlay').hide();
            }).on('error.dt', function(e, settings, techNote, message) {
                page.inform('出现异常', message, page.failure);
            });
            $('input[type="search"]').on('keyup', function() {
                dt.search(this.value, true).draw();
            });
        };
        if (!($.fn.dataTable)) {
            $.getMultiScripts([page.plugins.datatable.js], page.siteRoot())
                .done(function() { showTable(); });
        } else { showTable(); }
    },
    index: function (table, options) {
        this.unbindEvents();
        var $activeTabPane = $('#tab_' + page.getActiveTabId());
        // 记录列表
        this.initDatatable(table, options);
        // $('div.dataTables_length select').addClass('form-control');
        // $('div.dataTables_filter label').addClass('control-label');
        // 新增记录
        $('#add-record').on('click', function () {
            page.getTabContent($activeTabPane, table + '/create');
        });
        // 编辑、充值、查看记录
        var selectors = ['.fa-pencil', '.fa-money', '.fa-bars'];
        $(document).on('click', selectors.join(), function () {
            var url = $(this).parents().eq(0).attr('id');
            url = url.replace('_', '/');
            page.getTabContent($activeTabPane, table + '/' + url);
        });
        // 删除记录
        var id;
        $(document).on('click', '.fa-remove', function () {
            id = $(this).parents().eq(0).attr('id');
            $('#modal-dialog').modal({backdrop: true});
        });
        $('#confirm-delete').on('click', function () {
            page.ajaxRequest(
                'DELETE',
                page.siteRoot() + table + '/delete/' + id,
                {_token: $('#csrf_token').attr('content')},
                table

            );
        });
    },
    create: function (formId, table, options) {
        page.initForm(table, formId, table + '/store', 'POST', options);
    },
    edit: function (formId, table, options) {
        var id = $('#id').val();
        page.initForm(table, formId, table + '/update/' + id, 'PUT', options);
    },
    recharge: function (formId, table) {
        var id = $('#id').val();
        page.initForm(table, formId, table + '/rechargeStore/' + id, 'PUT');
    },
    loadCss: function(css) {
        if (!$('link[href="' + page.siteRoot() + css +'"]').length) {
            $cip.after($("<link/>", {
                rel: "stylesheet", type: "text/css",
                href: page.siteRoot() + css
            }));
        }
    },
    initSelect2: function(options) {
        if (!($.fn.select2)) {
            page.loadCss(page.plugins.select2.css);
            $.getMultiScripts([page.plugins.select2.js], page.siteRoot())
                .done(function() {
                    $('select').select2(typeof options !== 'undefined' ? options : {});
                });
        } else {
            $('select').select2(typeof options !== 'undefined' ? options : {});
        }
    },
    initICheck: function (object) {
        var init = function(object) {
            if (typeof object === 'undefined') {
                $(page.plugins.icheck.selector).iCheck(page.plugins.icheck.params);
            } else {
                object.find(page.plugins.icheck.selector).iCheck(page.plugins.icheck.params);
            }
        };
        if (!($.fn.iCheck)) {
            page.loadCss(page.plugins.icheck.css);
            $.getMultiScripts([page.plugins.icheck.js], page.siteRoot())
                .done(function() { init(object); });
        } else { init(object); }
    },
    initParsley: function ($form, requestType, url) {
        $form.parsley().on('form:validated', function () {
            if ($('.parsley-error').length === 0) {
                page.ajaxRequest(requestType, page.siteRoot() + url, $form.serialize(), $form[0]);
            }
        }).on('form:submit', function () {
            return false;
        });
    },
    initBackBtn: function(table) {
        $('#cancel, #record-list').on('click', function() { page.backToList(table); })
    },
    initForm: function (table, formId, url, requestType, options) {
        this.unbindEvents();
        var $form = $('#' + formId);
        page.initBackBtn(table);
        page.initSelect2(options);
        page.initICheck();
        page.initParsley($form, requestType, url);
    },
    unbindEvents: function () {
        var selectors = ['.fa-pencil', '.fa-money', '.fa-bars'];
        $('#add-record').unbind('click');
        $(document).off('click', selectors.join());
        $(document).off('click', '.fa-remove');
        $('#confirm-delete').unbind('click');
        $('#cancel, #record-list').unbind('click');
    },
    // 初始化起始时间与结束时间的Parsley验证规则
    initParsleyRules: function() {
        window.Parsley.removeValidator('start');
        window.Parsley.removeValidator('end');
        window.Parsley.addValidator('start', {
            requirementType: 'string',
            validateString: function(value, requirement) {
                var endTime = $(requirement).val();
                return value < endTime;
            },
            messages: {
                cn: '开始时间不得大于等于%s'
            }
        });
        window.Parsley.addValidator('end', {
            requirementType: 'string',
            validateString: function(value, requirement) {
                var startTime = $(requirement).val();
                return value > startTime;
            },
            messages: {
                cn: '结束时间不得小于等于%s'
            }
        });
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
    // 刷新菜单
    page.refreshMenus();
    // 获取状态为active的卡片
    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    // 初始化浏览器历史相关的popstate事件
    window.onpopstate = function (e) {
        if (!e.state) { return false; }
        // 如果用户点击浏览器“前进”或“后退”按钮，则不需要更新浏览器历史
        updateHistory = false;
        // 如果目标页面链接地址中包含pages关键字，则停止重定向
        if (e.state.url.indexOf('pages') > -1) { return false; }
        // 获取目标页面的卡片ID、菜单ID和菜单链接地址
        var paths = e.state.title.split(',');
        var targetTabId = paths[0];
        var targetMenuId = paths[1];
        var targetMenuUrl = paths[2];
        var uri = e.state.url;
        // 获取当前已激活卡片的ID
        var activeTabId = page.getActiveTabId();
        // 清除当前已激活卡片中的内容
        if (activeTabId) {
            $('a[href="#tab_' + activeTabId + '"]').parent().removeClass();
            $activeTabPane = $('#tab_' + activeTabId);
            $activeTabPane.removeClass('active').html('');
        }
        // 激活目标卡片
        var $targetTabLink = $('a[href="#tab_' + targetTabId + '"]');
        if ($targetTabLink.length !== 0) {
            // 如果目标页面包含的卡片与当前页面包含的卡片属于相同菜单
            $targetTabLink.parent().addClass('active');
            $activeTabPane = $('#tab_' + targetTabId);
            $activeTabPane.addClass('active');
            // 获取目标卡片中的HTML
            page.getTabContent($activeTabPane, uri);
            // 刷新卡片状态
            page.refreshTabs();
        } else {
            // 如果目标页面与当前页面属于不同菜单
            if (targetTabId !== '0') {
                // 如果目标页面包含卡片
                page.getWrapperContent(targetMenuId, targetMenuUrl, targetTabId, uri);
            } else {
                // 如果目标页面不包含卡片
                page.getWrapperContent(targetMenuId, targetMenuUrl);
            }
            // 刷新菜单状态
            $('.sidebar-menu li.active').removeClass('active menu-open');
            $('#' + targetMenuId).parent().addClass('active');
            page.refreshMenus();
        }
    };
    // 初始化菜单点击事件
    $(document).on('click', '.sidebar-menu a.leaf', function(e) {
        e.preventDefault();
        // var uri = $(this).attr('href');
        $('.sidebar-menu li.active').removeClass('active menu-open');
        $(this).parent().addClass('active');
        page.refreshMenus();
        page.getWrapperContent(page.getActiveMenuId(), page.getMenuUrl());
    });
    // 初始化卡片点击事件
    $(document).on('click', '.tab', function() {
        page.refreshTabs();
        // 获取被点击卡片的uri
        var url = $(this).attr('data-uri');
        // 选地所有卡片
        var $tabPanes = $('.card');
        // 获取状态为active的卡片
        var $activeTabPane = $('#tab_' + page.getActiveTabId());
        // 如果状态为active的卡片的内容为空, 清空其他卡片的内容
        if ($activeTabPane.html() === '') {
            // 清空所有卡片的内容
            $.each($tabPanes, function () { $(this).html(''); });
            // 获取状态为active的卡片内容
            page.getTabContent($activeTabPane, url);
        }
    });
    // 获取wrapper div中显示的Html
    page.getWrapperContent(page.getActiveMenuId(), page.getMenuUrl(), page.getActiveTabId());
});