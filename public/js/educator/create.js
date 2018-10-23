//# sourceURL=create.js
page.create('formEducator', 'educators');
/** 初始化教职员工创建页面功能 */
$.getMultiScripts(['js/shared/contact.js']).done(
    function () { $.contact().action('educators', 'create'); }
);