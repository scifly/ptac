//# sourceURL=index.js
page.index('students', [{
    className: 'text-center',
    targets: [1, 2, 3, 5, 6, 7, 8, 9, 10]
}]);

/** 初始化学籍首页功能 */
$.getMultiScripts(['js/contact.js']).done(function () {
    $.contact().index('students');
});