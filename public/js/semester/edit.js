page.edit('formSemester', 'semesters');
page.loadCss(page.plugins.timepicker.css);

function datetime($initTime) {
    $initTime.datetimepicker({
        dateFormat: "yy-mm-dd",
        showSecond: true,
        timeFormat: 'hh:mm:ss'
    })
}

$.getMultiScripts([page.plugins.timepicker.js, page.plugins.timepicker.jscn], page.siteRoot())
    .done(function () {
        datetime($(".start_date"));
        datetime($(".end_date"));
    });
