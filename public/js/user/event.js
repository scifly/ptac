$('#data-table').dataTable({
    processing: true,
    serverSide: true,
    ajax: 'events',
    order: [[0, 'desc']],
    stateSave: true,
    autoWidth: true,
    columnDefs: [
        {className: 'text-center', targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9]},
        {className: 'text-right', targets: [10]}
    ],
    scrollX: true,
    language: {url: page.siteRoot() + '/files/ch.json'},
    lengthMenu: [[15, 25, 50, -1], [15, 25, 50, '所有']]
});