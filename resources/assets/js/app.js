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

var pusher = new Pusher('15d0dcb36363ff076262', {authEndpoint: '/pusher_auth.php'});

const app = new Vue({
    el: '#app',
    created() {
        // Echo.channel('channelDemoEvent')
        //     .listen('eventTrigger', (e) => {
        //         alert(e);
        //     });

        Echo.private(`user.2`)
            .listen('eventTrigger', (e) => {
                alert('123');
            });
    }
});

