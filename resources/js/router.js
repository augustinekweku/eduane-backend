import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router)

import home from './components/pages/home'
import adminusers from './admin/pages/adminusers'
import roles from './admin/pages/roles'
import login from './admin/pages/login'
import assignrole from './admin/pages/assignrole'
import mealplans from './admin/pages/mealplans'
import mealplandurations from './admin/pages/mealplandurations'
import durationpackages from './admin/pages/durationpackages'




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
        path:  '/assignrole',
        component: assignrole,
        name: 'assignRole'
    },
    {
        path:  '/mealplans',
        component: mealplans,
        name: 'mealplans'
    },
    {
        path:  '/mealplandurations',
        component: mealplandurations,
        name: 'mealplandurations'
    },
    {
        path:  '/durationpackages',
        component: durationpackages,
        name: 'durationpackages'
    },
    
    

]

export default new Router ({
    mode: 'history',
    routes,
})