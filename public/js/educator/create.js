//# sourceURL=create.js
page.create('formEducator', 'educators');
page.loadCss('css/educator/educator.css');
/** 初始化教职员工创建页面功能 */
$.getMultiScripts(['js/shared/contact.js']).done(
    function () {
        $.contact().create('educators');
    }
);