import Vue from 'vue'
import VueRouter from 'vue-router'
import ElementUI from 'element-ui'
import 'element-ui/lib/theme-chalk/index.css'
import App from './App.vue'

import pageSeatMap from './page/pageSeatMap.vue'

Vue.use(VueRouter)
Vue.use(ElementUI)

const routes=[
    { path: '/ticket/:id/seat', component: pageSeatMap },
];

const router = new VueRouter({
    routes:routes
})
new Vue({
    el: '#app',
    router ,
    render: h => h(App)
})
