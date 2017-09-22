var page = {
    success: 'img/confirm.png',
    failure: 'img/error.png',
    inform: function(title, text, image) {
        $.gritter.add({title: title, text: text, image: page.siteRoot() + image});
    },
    siteRoot: function() {
        var path = window.location.pathname;
        var paths = path.split('/');
        return '/' + paths[1] + '/' + paths[2] + '/';
    },
    ajaxLoader: function() {
        return "<img alt='' src='" + page.siteRoot() + "/img/throbber.gif' " +
        "style='vertical-align: middle;'/>"
    },
    getActiveTabId: function() {
        var tabId = $('.nav-tabs .active a').attr('href').split('_');
        return tabId[tabId.length - 1];
    },
    getTabContent: function($tabPane, url) {
        var tabId = page.getActiveTabId();
        $('a[href="#tab_' + tabId +'"]').attr('data-url', url);
        $tabPane.html(page.ajaxLoader);
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: page.siteRoot() + url,
            data: { tabId: tabId },
            success: function(result) {
                $tabPane.html(result.html);
                $.getScript(page.siteRoot() + result.js);
            },
            error: function(e) {
                var obj = JSON.parse(e.responseText);
                page.inform('出现异常', obj['message'], page.failure);
            }
        });
    }
};
$(function() {
    // 获取状态为active的卡片
    var $activeTabPane = $('#tab_' + page.getActiveTabId());

    $(document).on('click', '.tab', function() {
        // 获取被点击卡片的url
        var url = $(this).attr('data-url');
        // 获取所有卡片
        var $tabPanes = $('.card');
        // 获取状态为active的卡片
        var $activeTabPane = $('#tab_' + page.getActiveTabId());
        // 如果状态为active的卡片的内容为空, 清空其他卡片的内容
        if ($activeTabPane.html() === '') {
            // 清空所有卡片的内容
            $.each($tabPanes, function() { $(this).html(''); });
            // 获取状态为active的卡片内容
            page.getTabContent($activeTabPane, url);
        }
    });
    // 获取状态为active的卡片的url
    url = page.siteRoot() + $('.nav-tabs .active a').attr('data-url');
    // 获取状态为active的卡片内容
    page.getTabContent($activeTabPane, url);
});