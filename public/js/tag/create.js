page.create('formTag', 'tags');
$.getMultiScripts(['js/shared/tree.js']).done(
    function () { $.tree().list('tags/create', 'contact'); }
);