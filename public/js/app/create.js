//# sourceURL=create.js
page.create('formApp', 'apps');
$('#category').on('change', function () {
    var category = parseInt($(this).val());

    $('.token').toggle(category !== 1);
    $('.eak').toggle(category !== 1);
    $('.name').toggle(category === 2);
    $('.type').toggle(category === 2);
    $('.url').toggle(category === 3);
    $('.appid').toggle(category !== 3);
});