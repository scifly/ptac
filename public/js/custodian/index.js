page.index('custodians');

/**批量导出监护人
 * */
$(document).on('click', '#export-custodian', function () {
    //无法用ajax请求
    window.location.href='../custodians/export';
});

