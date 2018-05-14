/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));

// Vue.component(
//     'passport-clients',
//     require('./components/passport/Clients.vue')
// );
//
// Vue.component(
//     'passport-authorized-clients',
//     require('./components/passport/AuthorizedClients.vue')
// );
//
// Vue.component(
//     'passport-personal-access-tokens',
//     require('./components/passport/PersonalAccessTokens.vue')
// );

var notify = function (e) {
    var image = page.success;
    switch (e['response']['statusCode']) {
        case 200: break;
        case 202: image = page.info; break;
        default: image = page.failure; break;
    }
    page.inform(
        e['response']['title'],
        e['response']['message'],
        image
    );
};
// noinspection JSUnusedLocalSymbols
const app = new Vue({
    // el: '#app',
    created() {
        Echo.private('user.' + document.getElementById('userId').value)
            .listen('JobResponse', (e) => { notify(e); })
    }
});
