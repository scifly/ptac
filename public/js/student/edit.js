//# sourceURL=edit.js
page.edit('formStudent', 'students');

/** 初始化学籍编辑页面功能 */
$.getMultiScripts(['js/contact.js']).done(
    function () {
        $.contact().edit('students');
    }
);