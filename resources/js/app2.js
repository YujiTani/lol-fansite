/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

import Vue from 'vue'


require('./bootstrap');

window.Vue = require('vue');


Vue.component('master-component', require('./components/MasterComponent.vue').default);


const app = new Vue({
    el: '#app2',
});



