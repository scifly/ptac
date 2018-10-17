var options = [
    {className: 'text-center', targets: [5, 6, 7, 8]},
    {className: 'searching_disabled', targets: [5]},
    {searchable: false, targets: [5]},
    {orderable: false, targets: [5]}
];
page.index('modules', options);
page.initSelect2();