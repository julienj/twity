import Vue from 'vue'
import Vuex from 'vuex'
import user from './modules/user'
import activity from './modules/activity'


Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production'

export default new Vuex.Store({
    modules: {
        user,
        activity
    },
    strict: debug
})
