$.getMultiScripts(['js/shared/recharge.js']).done(
    function () { $.recharge().charge('users', 'formUser'); }
);