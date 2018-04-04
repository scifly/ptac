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

// noinspection JSUnusedLocalSymbols
const app = new Vue({
    // el: '#app',
    created() {
        Echo.private('user.' + document.getElementById('userId').value)
            .listen('ContactImportTrigger', (e) => {
                console.log(e.data['type']);
                if (e.data['type'] === 'educator') {
                    alert('you nailed it!!!');
                    // page.inform('导入成功', '教职员工队列导入成功', page.success)
                }
                if (e.data['type'] === 'student') {
                    alert('you got it!!!');
                    // page.inform('导入成功', '学生队列导入成功', page.success)
                }
            })
            .listen('ContactSyncTrigger', (e) => {
                page.inform('同步结果', e.data['message'], page.info);
            });
    }
});
