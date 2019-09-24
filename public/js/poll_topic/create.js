var $category =
    row = '<tr>' +
    '<td>' +
        '<input class="form-control text-blue" name="option[]" type="text">' +
    '</td>' +
    '<td class="text-center">' +
        '<button class="btn btn-box-tool remove-option" title="移除">' +
            '<i class="fa fa-minus text-blue"></i>' +
        '</button>' +
    '</td>' +
'</tr>';
page.create('formPollTopic', 'poll_topics');

$('#options').toggle()