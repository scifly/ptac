var table = 'student_attendances';
page.initSelect2();
page.initBackBtn(table);
page.loadCss('css/attendance/stat.css');
page.loadCss(plugins.daterangepicker.css);
/** 加载图表插件 */
$.getMultiScripts([plugins.echarts.js]);

/** 初始化时间范围选择插件 */
page.initDateRangePicker();

/** 加载考勤统计公共插件 */
$.getMultiScripts(['js/common/attendance/common.js']);

/** 初始化年级选择事件监听 */
$.getMultiScripts(['js/shared/contact.js']).done(
    function () {
        $.contact().onGradeChange(table, 'stat');
    }
);
/** 初始化统计按钮点击事件 */
$('#stat').on('click', function () {
    $('#records').find('tbody').html('');
    $.attendance().stat(table)
});

/** 初始化导出按钮点击事件 */
$('#export').on('click', function () {
    window.location = page.siteRoot() + table + '/export';
});

