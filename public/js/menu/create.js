var options = [
    {
        option: {
            templateResult: page.formatState,
            templateSelection: page.formatState
        },
        id: 'icon_id'
    },
    {
        id: 'tab_ids'
    }
];
page.create('formMenu', 'menus', options);