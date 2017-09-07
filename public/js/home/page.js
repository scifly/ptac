var page = {
    success: 'img/confirm.png',
    failure: 'img/failure.jpg',
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
        $tabPane.html(page.ajaxLoader);
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: url,
            data: { tabId: page.getActiveTabId() },
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
    var $activeTabPane = $('#tab_' + page.getActiveTabId());

    $(document).on('click', '.tab', function() {
        var url = $(this).attr('data-url');
        var $tabPanes = $('.card');
        var $activeTabPane = $('#tab_' + page.getActiveTabId());

        if ($activeTabPane.html() === '') {
            $.each($tabPanes, function() {
                $(this).html('');
            });
            page.getTabContent($activeTabPane, page.siteRoot() + url);
        }
    });
    url = page.siteRoot() + $('.nav-tabs .active a').attr('data-url');
    page.getTabContent($activeTabPane, url);
});