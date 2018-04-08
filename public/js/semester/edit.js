page.edit('formSemester', 'semesters');
page.loadCss(plugins.timepicker.css);

function datetime($initTime) {
    $initTime.datetimepicker({
        dateFormat: "yy-mm-dd",
        showSecond: true,
        timeFormat: 'hh:mm:ss'
    })
}

if (!($.fn.timepicker)) {
    $.getMultiScripts([plugins.timepicker.js, plugins.timepicker.jscn])
        .done(function () {
            datetime($(".start_date"));
            datetime($(".end_date"));
        });
} else {
    datetime($(".start_date"));
    datetime($(".end_date"));
}

