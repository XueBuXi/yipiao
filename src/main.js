import Vue from 'vue'
import VueRouter from 'vue-router'
import ElementUI from 'element-ui'
import './assets/index.css'
import App from './App.vue'
import pageSeatMap from './page/pageSeatMap.vue'
import pageTicketList from './page/pageTicketList.vue'
import pageLogin from './page/pageLogin.vue'
 
Vue.use(VueRouter)
Vue.use(ElementUI)
const routes=[
    { path: '/ticket/:id/seat', component: pageSeatMap },
    { path: '/home', component: pageTicketList },
    { path: '/', component: pageLogin },
];

const router = new VueRouter({
    routes:routes
})
window.app=new Vue({
    el: '#app',
    router ,
    render: h => h(App),
    data:{}
})
