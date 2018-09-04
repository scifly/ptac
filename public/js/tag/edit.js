page.edit('formTag', 'tags');
$.getMultiScripts(['js/tree.js']).done(
    function () { $.tree().list('tags/edit', 'contact'); }
);