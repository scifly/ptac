//# sourceURL=create.js
page.create('formStudent', 'students');

/** 初始化学籍创建页面功能 */
$.getMultiScripts(['js/contact.js']).done(
    function () { $.contact().create('students'); }
);