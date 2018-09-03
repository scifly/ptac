//# sourceURL=index.js
page.index('students', [
    {className: 'text-center', targets: [1, 2, 3, 5, 6, 7, 8, 9, 10]},
    {className: 'searching_disabled', targets: [2]},
    {searchable: false, targets: [2]},
    {orderable: false, targets: [2]}
]);

/** 初始化学籍首页功能 */
$.getMultiScripts(['js/contact.js']).done(function () {
    $.contact().index('students');
});