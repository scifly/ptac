// $('#data-table').dataTable({
//     processing: true,
//     serverSide: true,
//     ajax: {
//         url: page.siteRoot() + 'users/message' + page.getQueryString(),
//         error: function (e) {
//             page.errorHandler(e);
//         }
//     },
//     order: [[0, 'desc']],
//     stateSave: true,
//     autoWidth: true,
//     columnDefs: [
//         { className: 'text-center', targets: [0, 1, 2, 3, 4, 5, 6, 7, 8] },
//     ],
//     scrollX: true,
//     language: {url: page.siteRoot() + '/files/ch.json'},
//     lengthMenu: [[15, 25, 50, -1], [15, 25, 50, '所有']]
// });
//# sourceURL=message.js
var options = [
    { className: 'text-center', targets: [0, 1, 2, 3, 4, 5, 6, 7] },
];
page.initDatatable('users', options, 'message');