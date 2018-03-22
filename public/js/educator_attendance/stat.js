var table = 'educator_attendances';
page.initBackBtn(table);
page.loadCss('css/attendance/stat.css');
page.loadCss(page.plugins.daterangepicker.css);

/** 加载图表插件 */
$.getMultiScripts([page.plugins.echarts.js], page.siteRoot());

/** 加载考勤管理公共插件 */
$.getMultiScripts(['js/common/attendance/common.js'], page.siteRoot());

/** 初始化时间范围选择插件 */
page.initDateRangePicker();

/** 初始化统计按钮点击事件 */
$('#stat').on('click', function () {
    $('#data-table').find('tbody').html('');
    var attend = $.attendance();
    attend.stat(table);
});

/** 初始化导出按钮点击事件 */
$('#export').on('click', function () {
    window.location = page.siteRoot() + table + '/export';
});