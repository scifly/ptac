var table = 'schools';
var id = $('#id').val();
var url = 'edit_info/' + id;
$('.btn-bianji').on('click', function () {
        var $wrapper = $('.content-wrapper');
        $('.overlay').show();
        $.ajax({
            type: 'GET',
            dataType: 'json',
            url: url,
            success: function(result) {
                switch (result.statusCode) {
                    case 200:
                        $wrapper.html(result.html);
                        $('.overlay').hide();
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
            },
            error: function(e) { page.errorHandler(e); }
        });
});
