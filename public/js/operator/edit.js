//# sourceURL=edit.js
$.getMultiScripts(['js/operator/operator.js']).done(function () {
    var operator = $.operator();
    operator.init('edit');
});