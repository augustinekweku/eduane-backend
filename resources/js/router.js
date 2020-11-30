import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router)

import home from './components/pages/home'
import adminusers from './admin/pages/adminusers'
import roles from './admin/pages/roles'
import login from './admin/pages/login'
import assignRole from './admin/pages/assignRole'
import createPlan from './admin/pages/createPlan'




const routes = [
    {
        path:  '/',
        component: home,
        name: 'home'
    },
    {
        path:  '/adminusers',
        component: adminusers,
        name: 'adminusers'
    },
    {
        path:  '/roles',
        component: roles,
        name: 'roles'
    },
    {
        path:  '/login',
        component: login,
        name: 'login'
    },
    {
        path:  '/assignRole',
        component: assignRole,
        name: 'assignRole'
    },
    {
        path:  '/createPlan',
        component: createPlan,
        name: 'createPlan'
    },
    
    

]

export default new Router ({
    mode: 'history',
    routes,
})