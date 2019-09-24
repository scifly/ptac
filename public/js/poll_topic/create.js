page.create('formPollTopic', 'poll_topics');
$.getMultiScripts(['js/poll_topic/pt.js']).done(
    function () { $.pt().init(); }
);
