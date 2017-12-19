page.index('custodians');

/**批量导出监护人
 * */
$(document).off('click', '#export');
$(document).on('click', '#export', function () {
    //无法用ajax请求
    window.location.href='../custodians/export';
});

