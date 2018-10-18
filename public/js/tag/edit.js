page.edit('formTag', 'tags');
$.getMultiScripts(['js/shared/tree.js']).done(
    function () { $.tree().list('tags/edit', 'contact'); }
);