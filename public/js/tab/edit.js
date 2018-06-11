var options = {
    templateResult: page.formatState,
    templateSelection: page.formatState
};
page.edit('formTab', 'tabs', options);
page.initSelect2();