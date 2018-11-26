/* 
 * Vue application
 */

let App = Vue.extend({});

let postList = Vue.extend({
    template: '#mv-books-list'
});

/* создаем екземпляр класса роутер */
const router = new VueRouter({
    routes: [
        {
            path: '/',
            component: postList
        }
    ]
});

//router.map({
//    '/': {
//        component: postList
//    }
//});

//router.start(App, '#app');
const app = new Vue({
  router
}).$mount('#app');