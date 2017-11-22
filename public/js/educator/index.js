page.index('educators');

/** 导出excel 选择学校 */
var item = 'educator';
if (typeof custodian === 'undefined') {
    $.getMultiScripts(['js/custodian.relationship.js'], page.siteRoot())
        .done(function() { custodian.init(item); });
} else { custodian.init(item); }