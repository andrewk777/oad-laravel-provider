import Vue from "vue";
import Router from "vue-router";
import { CONF } from './config';
import { frontAuth, backAuth } from './auth';
import beforeEachRoute from './sys/beforeEachRoute'
import afterEachRoute from './sys/afterEachRoute'
import pathThrough from '@/components/parentBlankComponent'

Vue.use(Router);

// create new router
const routes = [
    {
        path: "/",
        component: require('@/views/frontend/layout').default,
        beforeEnter: frontAuth,
        children: [
            {
                path: "/",
                component: require('@/views/frontend/auth/login').default,
                name: 'login',
                meta: { title: 'Login | ' + CONF.APP_NAME },
            },
            {
                path: "/auth/forgot_password",
                name: 'forgot_password',
                component: require('@/views/frontend/auth/forgot_password').default,
                meta: { title: 'Forgot Password | ' + CONF.APP_NAME },
            },
            {
                path: "/auth/reset_password",
                name: 'reset_password',
                component: require('@/views/frontend/auth/reset_password').default,
                meta: { title: 'Reset Password | ' + CONF.APP_NAME },
            },
            {
                path: "/auth/reset_password_link/:email/:token",
                name: 'reset_password_link',
                component: require('@/views/frontend/auth/reset_password').default,
                meta: { title: 'Reset Password | ' + CONF.APP_NAME },
            },
            {
                path: "/auth/reset_password_link/expired",
                name: 'reset_password_link_expired',
                component: require('@/views/frontend/auth/reset_password_expired').default,
                meta: { title: 'Reset Password | ' + CONF.APP_NAME },
            }
        ]
    },
    {
        path: "/app",
        component:  require('@/views/backend/common/layout').default,
        beforeEnter: backAuth,
        children: [
            {
                path: "/dashboard",
                name: 'dashboard',
                component:  require('@/views/backend/dashboard').default,
                meta: { title: 'Dashboard | ' + CONF.APP_NAME },
            },
            {
                path: "/admin",
                name: 'admin',
                component: pathThrough,
                children: [                    
                    {
                        path: "users",
                        name: 'users',
                        component:  require('@/views/backend/users/Users').default,
                        meta: { title: 'Users | ' + CONF.APP_NAME },
                        children: [                    
                            {
                                path: "user-form/:hash",
                                name: 'usersForm',
                                component:  require('@/views/backend/users/UsersForm').default,
                                meta: { title: 'Users | ' + CONF.APP_NAME },
                            },                  
                        ]
                    },
                    {
                        path: "user-roles",
                        name: 'usersRoles',
                        component:  require('@/views/backend/user-roles/UserRoles').default,
                        meta: { title: 'User Permissions | ' + CONF.APP_NAME },
                        children: [                    
                            {
                                path: "user-roles-form/:hash",
                                name: 'usersRolesForm',
                                component:  require('@/views/backend/user-roles/UserRolesForm').default,
                                meta: { title: 'User Permissions | ' + CONF.APP_NAME },
                            },                  
                        ]
                    },                
                ]
            }
        ]
    },
    {
        path: "*",
        component: require('@/views/pages/notFound').default,
        meta: { title: 'Page Not Found | ' + CONF.APP_NAME },
    }
];

const router = new Router({
    mode: "history",
    linkActiveClass: "active",
    routes,
    scrollBehavior(to, from, savedPosition) {
    return {x: 0, y: 0};
}
});

router.beforeEach(beforeEachRoute);
router.afterEach(afterEachRoute);

export default router;
