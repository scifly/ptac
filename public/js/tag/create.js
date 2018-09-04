page.create('formTag', 'tags');
$.getMultiScripts(['js/tree.js']).done(
    function () { $.tree().list('tags/create', 'contact'); }
);