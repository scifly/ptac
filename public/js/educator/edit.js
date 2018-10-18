//# sourceURL=edit.js
page.edit('formEducator', 'educators');

/** 初始化教职员工编辑页面功能 */
$.getMultiScripts(['js/shared/contact.js']).done(
    function () {
        $.contact().edit('educators');
    }
);