import Vue from 'vue'
import VueRouter from 'vue-router'
import ElementUI from 'element-ui'
import './assets/index.css'
import App from './App.vue'
import pageSeatMap from './page/pageSeatMap.vue'
import pageTicketList from './page/pageTicketList.vue'
import pageTicketView from './page/pageTicketView.vue'
import pageLogin from './page/pageLogin.vue'
import VueQrcode from '@xkeshi/vue-qrcode'
import finger from './vue-finger.js'

Vue.use(VueRouter) 
Vue.use(ElementUI)
Vue.use(finger)
Vue.component('qrcode', VueQrcode);
const routes=[
    { path: '/ticket/:id/seat', component: pageSeatMap },
    { path: '/ticket/:id', component: pageTicketView },
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
