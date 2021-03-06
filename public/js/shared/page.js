var oPage = {title: '', url: location.href},
    updateHistory = true, replaceState = true,
    $cip = $('#cip');
// noinspection JSUnusedGlobalSymbols
var page = {
    success: 'img/confirm.png',
    failure: 'img/error.png',
    info: 'img/info.png',
    drLocale: function () {
        return {
            format: "YYYY年MM月DD日",
            separator: " 至 ",
            applyLabel: "确定",
            cancelLabel: "取消",
            fromLabel: "从",
            toLabel: "到",
            todayRangeLabel: '今天',
            customRangeLabel: "自定义",
            weekLabel: "W",
            daysOfWeek: ["日", "一", "二", "三", "四", "五", "六"],
            monthNames: [
                "一月", "二月", "三月", "四月", "五月", "六月",
                "七月", "八月", "九月", "十月", "十一月", "十二月"
            ],
            firstDay: 1
        };
    },
    dRanges: function () {
        return {
            '今天': [
                moment(),
                moment()
            ],
            '昨天': [
                moment().subtract(1, 'days'),
                moment().subtract(1, 'days')
            ],
            '过去 7 天': [
                moment().subtract(6, 'days'),
                moment()
            ],
            '过去 30 天': [
                moment().subtract(29, 'days'),
                moment()
            ],
            '这个月': [
                moment().startOf('month'),
                moment().endOf('month')
            ],
            '上个月': [
                moment().subtract(1, 'month').startOf('month'),
                moment().subtract(1, 'month').endOf('month')
            ]
        };
    },
    token: function () {
        return $('meta[name="csrf-token"]').attr('content');
    },
    backToList: function (table) {
        var $activeTabPane = $('#tab_' + page.getActiveTabId());
        if ($activeTabPane.length !== 0) {
            page.getTabContent($activeTabPane, table + '/index');
        } else {
            page.getWrapperContent(page.getActiveMenuId(), page.siteRoot() + table + '/index');
        }
    },
    inform: function (title, text, image) {
        title = title || '提示';
        // $.extend($.gritter.options, {position: 'bottom-right'});
        $.gritter.add({title: title, text: text, image: page.siteRoot() + image});
    },
    siteRoot: function () {
        var siteRoot = window.location.origin
            ? window.location.origin + '/'
            : window.location.protocol + '/' + window.location.host + '/';
        if (window.location.href.indexOf('public') > -1) {
            return siteRoot + 'ptac/public/';
        }
        return siteRoot;
    },
    ajaxLoader: function () {
        return "<img id='ajaxLoader' alt='' src='" + page.siteRoot() + "/img/throbber.gif' " +
            "style='vertical-align: middle;'/>"
    },
    formatState: function (state) {
        return !state.id
            ? state.text
            : $('<span><i class="' + state.text + '" style="width: 20px;"></i>' + state.text + '</span>');
    },
    formatStateImg: function (state) {
        var paths = state.text.split('|'),
            style = "height: 18px; vertical-align: text-bottom; margin-right: 5px;";

        return !state.id
            ? paths[0]
            : $('<span><img src="' + paths[1] + '" style="' + style + '" alt="">&nbsp;' + paths[0] + '</span>');
    },
    refreshMenus: function () {
        var $active = $('.sidebar-menu li.active'),
            parents = $active.parentsUntil('.sidebar-menu');

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
    refreshTabs: function () {
        var $tabs = $('a.tab');
        $.each($tabs, function () {
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
    getActiveMenuId: function () {
        return $('.sidebar-menu li.active').last().find('a').attr('id');
    },
    getTabUrl: function () {
        var url = window.location.href;
        return encodeURIComponent(url + this.getQueryString());
    },
    getMenuUrl: function () {
        return page.siteRoot() + $('.sidebar-menu li.active').last().find('a').attr('href');
    },
    getQueryString: function (extra) {
        return '?extra=' + (typeof extra === 'undefined' ? '' : extra) +
            '&menuId=' + this.getActiveMenuId() + '&tabId=' + this.getActiveTabId();
    },
    errorHandler: function (e) {
        var obj = JSON.parse(e.responseText),
            status = e.status,
            title = [obj['exception'], status].join(' : '),
            message = obj['message'] + '\n' + [obj['file'], obj['line']].join(' : ');

        $('.overlay').hide();
        if (status === 406 && typeof obj['errors'] !== 'undefined') {
            $.each(obj['errors'], function () {
                page.inform(title, this, page.failure);
            });
        } else if (status === 498) {
            window.location.reload();
        } else {
            page.inform(title, message, page.failure);
            if (status === 401) {
                window.location = page.siteRoot() + 'login?returnUrl=' + page.getMenuUrl()
            }
        }
    },
    getWrapperContent: function (menuId, menuUrl, tabId, tabUrl) {
        var $wrapper = $('.content.clearfix');

        $('.overlay').show();
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: menuUrl,
            data: {menuId: menuId},
            success: function (result) {
                var d = result['department'] || '';

                $('.d_icon').removeClass().addClass('fa ' + (d['icon'] || 'fa fa-send-o') + ' d_icon');
                $('.d_name').html(d['name'] || '运营');
                // $wrapper.html(result.html);
                $wrapper.html(result.html);
                $('#head-breadcrumb').html(result['title']);
                $('.overlay').hide();
                // 获取状态为active的卡片内容
                var $tab = null;
                var tabUri = $('.nav-tabs .active a').attr('data-uri');
                if (typeof tabUri !== 'undefined') {
                    // Wrapper中的Html包含卡片，获取卡片中的Html
                    if (typeof tabId !== 'undefined') {
                        var $tabPanes = $('.card');
                        $.each($tabPanes, function () {
                            $(this).html('');
                        });
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
                        function () {
                            $(this).removeClass('text-gray').addClass('text-blue');
                        },
                        function () {
                            if (!($(this).parent().hasClass('active'))) {
                                $(this).removeClass('text-blue').addClass('text-gray');
                            }
                        }
                    );
                    // 获取当前卡片中的HTML
                    page.getTabContent($tab, tabUri);
                } else {
                    if (typeof result.js !== 'undefined') {
                        $.getScript(page.siteRoot() + result.js, function () {
                            $('#ajaxLoader').remove();
                            $('.overlay').hide();
                        });
                    }
                    // Wrapper中的Html不含卡片，更新浏览器History
                    document.title = result['title'];
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
            },
            error: function (e) {
                page.errorHandler(e);
            }
        });
    },
    getTabContent: function ($tabPane, url, ids) {
        if ($tabPane.length === 0) {
            page.getWrapperContent(page.getActiveMenuId(), page.siteRoot() + url);
            return false;
        }
        if (url.indexOf('http://') > -1) {
            url = url.replace(page.siteRoot(), '');
        }
        var tabId = page.getActiveTabId(),
            menuId = page.getActiveMenuId(),
            data = {tabId: tabId, menuId: menuId};

        if (typeof ids !== 'undefined') {
            data = $.extend(data, {ids: ids});
        }
        $('a[href="#tab_' + tabId + '"]').attr('data-uri', url);
        $tabPane.html(page.ajaxLoader);
        $('.overlay').show();
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: page.siteRoot() + url,
            data: data,
            success: function (result) {
                // 在当前已激活卡片中加载服务器返回的HTML
                $tabPane.html(result.html);
                $('#head-breadcrumb').html(result['breadcrumb']);
                $('.overlay').show();
                // $('#ajaxLoader').after(result.html);
                // 动态加载服务器返回的链接地址指向的js脚本
                $.getScript(page.siteRoot() + result.js, function () {
                    $('#ajaxLoader').remove();
                    $('.overlay').hide();
                    // 移除当前页面的datatable.css
                    // if (!$('#data-table').length) {
                    //     $('link[href="' + page.siteRoot() + plugins.datatable.css +'"]').remove();
                    // }
                });
                // 更新浏览器抬头
                document.title = $('#breadcrumb').html();
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
            },
            error: function (e) {
                page.errorHandler(e);
            }
        });
    },
    ajaxRequest: function (requestType, url, data, succeed) {
        $('.overlay').show();
        $.ajax({
            type: requestType,
            dataType: 'json',
            url: page.siteRoot() + url,
            data: data,
            success: function (result) {
                $('.overlay').hide();
                if (typeof succeed !== 'undefined') succeed();
                page.inform(result.title, result.message, page.success);
            },
            error: function (e) {
                page.errorHandler(e);
            }
        });
    },
    initDatatable: function (table, options, method, dtId, extra) {
        var datatable = (typeof dtId === 'undefined' ? '#data-table' : '#' + dtId),
            actions = ['enable', 'disable', 'delete'],
            rowIds = [],
            selected = [],
            $tbody = $(datatable + ' tbody'),
            $selectAll = $('#select-all'),
            $deselectAll = $('#deselect-all'),
            showTable = function () {
                var $datatable = $(datatable),
                    columns = $datatable.find('thead tr th').length,
                    statusCol = {className: 'text-right', targets: [columns - 1]},
                    uri = typeof method === 'undefined' ? '/index' : '/' + method,
                    url = page.siteRoot() + table + uri + page.getQueryString(extra);

                if (typeof options === 'undefined') {
                    options = [statusCol];
                } else {
                    options.push(statusCol);
                }
                var params = {
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: url,
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    },
                    rowCallback: function (row, data) {
                        if ($.inArray(data[0], selected) !== -1) {
                            $(row).addClass('selected');
                        }
                    },
                    order: [[0, 'desc']],
                    stateSave: true,
                    autoWidth: true,
                    columnDefs: options,
                    scrollX: true,
                    language: {url: '/files/ch.json'},
                    lengthMenu: [[15, 25, 50, -1], [15, 25, 50, '所有']],
                    // dom: '<"row"<"col-md-6"l><"col-sm-4"f><"col-sm-2"B>>rt<"row"<"col-sm-6"i><"col-sm-6"p>>',
                    // buttons: ['pdf', 'csv']
                };
                page.loadCss(plugins.datatable.css);
                page.loadCss(plugins.datatable.multiCss);
                $('.overlay').show();
                $.fn.dataTable.ext.errMode = 'none';
                $datatable.find('tfoot input').css('width', '100%');
                var dt = $datatable.DataTable(params).on('init.dt', function () {
                    // $('.dt-buttons').addClass('pull-right');
                    // $('.buttons-pdf').addClass('btn-sm');
                    // $('.buttons-csv').addClass('btn-sm');
                    // $('.paginate_button').each(function() { $(this).addClass('btn-sm'); })
                    $('.dataTables_filter input').off().on('keyup', function (e) {
                        if (e.keyCode === 13) {
                            dt.search(this.value, true).draw();
                        }
                    }).attr('title', '多关键词请用空格分隔');
                    $('.dataTables_scrollHeadInner').css('width', '100%');
                    $('.dataTables_scrollHeadInner table').css('width', '100%');
                    $('.overlay').hide();
                    $('.dataTables_scrollFoot').find('tfoot th').each(function () {
                        var $this = $(this),
                            $title = $datatable.find('thead th:nth(' + $this.index() + ')'),
                            searchDisabled = $title.hasClass('searching_disabled');
                        if ($this.attr('class') !== 'undefined') {
                            if (searchDisabled) {
                                $this.html('');
                            } else {
                                $this.find('input').addClass($this.attr('class'));
                            }
                        }
                    });
                }).on('error.dt', function (e, settings, techNote, message) {
                    page.inform('加载列表', message, page.failure);
                }).on('xhr.dt', function (e, settings, data) {
                    rowIds = data['ids'];
                    var differences = [];
                    $.grep(selected, function (el) {
                        if ($.inArray(el, rowIds) === -1) {
                            differences.push(el);
                        }
                    });
                    $.each(differences, function () {
                        if ($.inArray(parseInt(this), rowIds) === -1) {
                            selected.splice($.inArray(parseInt(this), selected), 1);
                        }
                    });
                });

                dt.search('').columns().search('').draw();
                $(document).off('keyup', 'tfoot .form-control');
                $(document).on('keyup', 'tfoot .form-control', function (e) {
                    var i = $(this).parent().index(),
                        keyword = $(this).val();
                    if (e.keyCode === 13) {
                        dt.columns(i).search(keyword).draw();
                    }
                });
                $(document).off('change', 'tfoot .select2');
                $(document).on('change', 'tfoot .select2', function () {
                    var i = $(this).parent().index();
                    dt.columns(i).search($(this).val()).draw();
                });
                $(document).off('change', 'tfoot .dtrange');
                $(document).on('change', 'tfoot .dtrange', function () {
                    var i = $(this).parent().index();
                    dt.columns(i).search($(this).val()).draw();
                });
            },
            batch = function (action) {
                var data = {
                        ids: selected,
                        action: action,
                        _token: page.token()
                    },
                    uri = '/' + action,
                    type = $('#batch-' + action).attr('title');

                if (selected.length === 0) {
                    page.inform(type, '请选择需要' + type + '的记录', page.failure);
                    return false;
                }
                if ($.inArray(action, ['enable', 'disable']) !== -1) {
                    data = $.extend(data, {field: 'enabled'});
                    uri = '/update';
                }
                if ($.inArray(action, ['enable', 'disable', 'delete', 'export']) === -1) {
                    page.getTabContent(
                        $('#tab_' + page.getActiveTabId()),
                        table + '/' + action, selected
                    )
                } else {
                    var types = {
                        'export': 'POST',
                        'enable': 'PUT',
                        'disable': 'PUT',
                        'delete': 'DELETE'
                    };
                    $('.overlay').show();
                    $.ajax({
                        type: types[action],
                        dataType: 'json',
                        url: page.siteRoot() + table + uri,
                        data: data,
                        success: function (result) {
                            $('.overlay').hide();
                            switch (action) {
                                case 'enable':
                                case 'disable':
                                    $(datatable + ' tbody tr.selected td:last-child >:first-child').each(function () {
                                        $(this).removeClass().addClass(
                                            'fa fa-circle ' + (action === 'enable' ? 'text-green' : 'text-gray')
                                        );
                                    });
                                    break;
                                case 'delete':
                                    $(datatable + ' tbody tr.selected').each(function () {
                                        $(this).addClass('text-gray');
                                    });
                                    break;
                                case 'export':
                                    window.location = page.siteRoot + result['url'];
                                    break;
                                default:
                                    break;
                            }
                            page.inform('批量' + type, result.message, page.success);
                        },
                        error: function (e) {
                            page.errorHandler(e);
                        }
                    });
                }
            };

        $tbody.off().on('click', 'tr', function () {
            var id = parseInt($(this).find('td').eq(0).text()),
                index = $.inArray(id, selected);

            index === -1 ? selected.push(id) : selected.splice(index, 1);
            $(this).toggleClass('selected');
        });
        $selectAll.off().on('click', function () {
            var $rows = $(datatable + ' tbody tr');
            $.each($rows, function () {
                $(this).addClass('selected');
            });
            selected = rowIds;
        });
        $deselectAll.off().on('click', function () {
            selected = [];
            var $rows = $(datatable + ' tbody tr');
            $.each($rows, function () {
                $(this).removeClass('selected');
            });
        });

        if (typeof options[0] === 'string') {
            actions = actions.concat(options[0].split(','));
        }
        $.map(
            actions,
            function (action) {
                $('#batch-' + action).off().on(
                    'click', function () {
                        batch(action);
                    }
                );
            }
        );
        $.getMultiScripts([plugins.datatable.js]).done(
            function () {
                showTable();
            }
        );
    },
    index: function (table, options) {
        var $activeTabPane = $('#tab_' + page.getActiveTabId());

        this.unbindEvents();
        // 列表
        this.initDatatable(table, options);
        // 新增
        $('#add-record').on('click', function () {
            page.getTabContent($activeTabPane, table + '/create');
        });
        // 编辑、充值、详情
        $.map(
            ['.fa-pencil', '.fa-money', '.fa-bars'],
            function (icon) {
                $(document).on('click', icon, function () {
                    var url = $(this).parents().eq(0).attr('id');
                    url = url.replace('_', '/');
                    page.getTabContent($activeTabPane, table + '/' + url);
                });
            }
        );
        // 删除记录
        this.remove(table, options);
    },
    create: function (formId, table, options) {
        page.initForm(table, formId, table + '/store', 'POST', options);
    },
    edit: function (formId, table, options) {
        var $id = $('input[name=id]'),
            id = $id.length > 0 ? $id.val() : '';

        page.initForm(table, formId, table + '/update/' + id, 'PUT', options);
    },
    show: function (table) {
        var url = 'edit/' + $('input[name=id]').val(),
            $activeTabPane = $('#tab_' + page.getActiveTabId());

        page.initBackBtn(table);
        $('.btn-bianji').on('click', function () {
            page.getTabContent($activeTabPane, table + '/' + url);
        });
    },
    remove: function (table, options) {
        var id, reloadDt = function () {
            $('#data-table').dataTable().fnDestroy();
            page.initDatatable(table, options);
        };

        // 打开删除对话框
        $(document).on('click', '.fa-remove', function () {
            id = $(this).parents().eq(0).attr('id');
            $('#modal-delete').modal({backdrop: true});
        });
        // 删除记录
        $('#confirm-delete').on('click', function () {
            page.ajaxRequest(
                'DELETE',
                table + '/delete/' + id,
                {_token: page.token()},
                reloadDt
            );
        });
    },
    loadCss: function (css) {
        if (!$('link[href="' + page.siteRoot() + css + '"]').length) {
            $cip.after($("<link/>", {
                rel: "stylesheet", type: "text/css",
                href: page.siteRoot() + css
            }));
        }
    },
    initSelect2: function (options) {
        var option = {language: "zh-CN"},
            $select = $('select'),
            init = function (options) {
                if (typeof options === 'undefined') {
                    $select.select2(option);
                } else {
                    $.each(options, function () {
                        $select = $(this['name']);
                        if (typeof this['option'] !== 'undefined') {
                            $.extend(option, this['option']);
                        }
                        $select.select2(option);
                    });
                }
            };

        if (!($.fn.select2)) {
            page.loadCss(plugins.select2.css);
            $.getMultiScripts([plugins.select2.js])
                .done(function () {
                    $.getMultiScripts([plugins.select2.jscn]).done(
                        function () { init(options); }
                    );
                });
        } else {
            init(options);
        }
    },
    initICheck: function (object) {
        var init = function (object) {
            if (typeof object === 'undefined') {
                $(plugins.icheck.selector).iCheck(plugins.icheck.params);
            } else {
                object.find(plugins.icheck.selector).iCheck(plugins.icheck.params);
            }
        };
        if (!($.fn.iCheck)) {
            page.loadCss(plugins.icheck.css);
            $.getMultiScripts([plugins.icheck.js])
                .done(function () {
                    init(object);
                });
        } else {
            init(object);
        }
    },
    initMinimalIcheck: function (object) {
        var init = function (object) {
            if (typeof object === 'undefined') {
                $(plugins.minimal_icheck.selector).iCheck(plugins.minimal_icheck.params);
            } else {
                object.find(plugins.minimal_icheck.selector).iCheck(plugins.minimal_icheck.params);
            }
        };
        if (!($.fn.iCheck)) {
            page.loadCss(plugins.minimal_icheck.css);
            $.getMultiScripts([plugins.minimal_icheck.js])
                .done(function () {
                    init(object);
                });
        } else {
            init(object);
        }
    },
    initParsley: function ($form, requestType, url) {
        $form.parsley().on('form:validated', function () {
            var reset = function () {
                if (requestType === 'POST') {
                    $('input[type="text"], textarea').each(function () {
                        $(this).val('');
                    });
                }
            };
            if ($('.parsley-error').length === 0) {
                page.ajaxRequest(requestType, url, page.formData($form), reset);
            }
        }).on('form:submit', function () {
            return false;
        });
    },
    formData: function ($form) {
        var data = $form.serialize(), id, value,
            $disabledSelects = $('select[disabled]');

        if ($disabledSelects.length > 0) {
            $.each($disabledSelects, function () {
                id = $(this).attr('id');
                value = $(this).val();
                data += '&' + id + '=' + value;
            });
        }
        return data;
    },
    initBackBtn: function (table) {
        $('#cancel, #record-list').off().on(
            'click', function () { page.backToList(table); }
        );
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
        $.map(
            ['.fa-pencil', '.fa-money', '.fa-bars', '.fa-remove'],
            function (target) { $(document).off('click', target); }
        );
        $.map(
            ['#add-record', '#confirm-delete', '#cancel, #record-list'],
            function (target) { $(target).unbind('click'); }
        );
    },
    // 初始化起始时间与结束时间的Parsley验证规则
    initParsleyRules: function () {
        window.Parsley.removeValidator('start');
        window.Parsley.removeValidator('end');
        window.Parsley.addValidator('start', {
            requirementType: 'string',
            validateString: function (value, requirement) {
                var endTime = $(requirement).val();
                return value < endTime;
            },
            messages: {
                cn: '开始时间不得大于等于%s'
            }
        });
        window.Parsley.addValidator('end', {
            requirementType: 'string',
            validateString: function (value, requirement) {
                var startTime = $(requirement).val();
                return value > startTime;
            },
            messages: {
                cn: '结束时间不得小于等于%s'
            }
        });
    },
    dateRange: function (selector) {
        if (typeof selector === 'undefined') {
            selector = 'daterange';
        }
        page.loadCss(plugins.daterangepicker.css);
        $('#' + selector).daterangepicker(
            {
                locale: page.drLocale(),
                ranges: page.dRanges(),
                startDate: moment().subtract(29, 'days'),
                endDate: moment()
            },
            function (start, end) {
                $('#' + selector).find('span').html(
                    start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD')
                );
            }
        );
    },
    initDateRangePicker: function (selector) {
        $.getScript(
            page.siteRoot() + plugins.daterangepicker.moment,
            function () {
                $.getScript(
                    page.siteRoot() + plugins.daterangepicker.js,
                    function () {
                        page.dateRange(selector);
                    }
                )
            }
        )
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
    },
    initBroadcaster: function () {
        Pusher.logToConsole = true;
        var pusher = new Pusher($('meta[name="pusher-key"]').attr('content'), {
                cluster: $('meta[name="pusher-cluster"]').attr('content'),
                encrypted: true
            }),
            channel = pusher.subscribe('channel.' + $('#userId').val());

        channel.bind('broadcast', function (response) {
            var status = response['statusCode'], image;

            if (status === 200) {
                image = page.success;
            } else if (status === 202) {
                image = page.info;
            } else {
                image = page.failure;
            }
            page.inform(response['title'], response['message'], image);
            if (typeof response['url'] !== 'undefined') {
                window.location = page.siteRoot() + response['url'];
            }
        });
    }
    // getScripts: function (scripts, callback) {
    //     var progress = 0;
    //     scripts.forEach(function(script) {
    //         $.getScript(page.siteRoot() + script, function () {
    //             if (++progress === scripts.length) callback();
    //         });
    //     });
    // }
};
$.getMultiScripts = function (arr) {
    var path = page.siteRoot(),
        _arr = $.map(arr, function (scr) {
            return $.getScript((path || "") + scr);
        });
    _arr.push($.Deferred(
        function (deferred) {
            $(deferred.resolve);
        })
    );
    return $.when.apply($, _arr);
};
$(function () {
    // 初始化广播
    page.initBroadcaster();
    // 刷新菜单
    page.refreshMenus();
    // 个人信息弹窗
    $('#profile').on('click', function (e) {
        e.preventDefault();
        page.edit('formUser', 'users');
        $('#user-profile').modal({backdrop: true});
    });
    // 获取状态为active的卡片
    var $activeTabPane = $('#tab_' + page.getActiveTabId());
    // 初始化浏览器历史相关的popstate事件
    window.onpopstate = function (e) {
        if (!e.state) return false;
        // 如果用户点击浏览器“前进”或“后退”按钮，则不需要更新浏览器历史
        updateHistory = false;
        // 如果目标页面链接地址中包含pages关键字，则停止重定向
        if (e.state.url.indexOf('pages') > -1) return false;
        // 获取目标页面的卡片ID、菜单ID和菜单链接地址
        var paths = e.state.title.split(','),
            targetTabId = paths[0],
            targetMenuId = paths[1],
            targetMenuUrl = paths[2],
            uri = e.state.url,
            // 获取当前已激活卡片的ID
            activeTabId = page.getActiveTabId();

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
    $(document).on('click', '.sidebar-menu a.leaf', function (e) {
        e.preventDefault();
        // var uri = $(this).attr('href');
        $('.sidebar-menu li.active').removeClass('active menu-open');
        $(this).parent().addClass('active');
        page.refreshMenus();
        page.getWrapperContent(page.getActiveMenuId(), page.getMenuUrl());
    });
    // 初始化卡片点击事件
    $(document).on('click', '.tab', function () {
        page.refreshTabs();
        // 获取被点击卡片的uri
        var url = $(this).attr('data-uri');
        if (typeof url !== 'undefined') {
            // 选定所有卡片
            var $tabPanes = $('.card');
            // 获取状态为active的卡片
            var $activeTabPane = $('#tab_' + page.getActiveTabId());
            // 如果状态为active的卡片的内容为空, 清空其他卡片的内容
            if ($.trim($activeTabPane.html()) === '') {
                // 清空所有卡片的内容
                $.each($tabPanes, function () {
                    $(this).html('');
                });
                // 获取状态为active的卡片内容
                page.getTabContent($activeTabPane, url);
            }
        }
    });
    // 获取wrapper div中显示的Html
    page.getWrapperContent(page.getActiveMenuId(), page.getMenuUrl(), page.getActiveTabId());
});