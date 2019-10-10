var options = [
    {
        option: {
            templateResult: page.formatState,
            templateSelection: page.formatState
        },
        name: 'select[name=icon_id]'
    },
    {
        name: 'select[name="tab_ids[]"]'
    }
];
page.create('formMenu', 'menus', options);