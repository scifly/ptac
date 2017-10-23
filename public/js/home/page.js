var oPage = {
    title: '',
    url: location.href
};
var updateHistory = true;
var replaceState = true;
var docTitle = '家校通';
var rootDir = 'ptac/public/';
var page = {
    success: 'img/confirm.png',
    failure: 'img/error.png',
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
                        setTimeout(function () {
                            $('#ajaxLoader').remove();
                        }, 1000)
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