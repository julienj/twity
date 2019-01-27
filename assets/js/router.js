import Router from 'vue-router'

import Dashboard from './views/Dashboard'
import NewPackage from './views/package/NewPackage'
import Packages from './views/package/Packages'
import Package from './views/package/Package'
import PackageDetails from './views/package/PackageDetails'
import PackageDowloads from './views/package/PackageDowloads'
import NewApplication from './views/application/NewApplication'
import Applications from './views/application/Applications'
import Application from './views/application/Application'
import ApplicationEnvironments from './views/application/ApplicationEnvironments'
import ApplicationMembers from './views/application/ApplicationMembers'
import Backends from './views/backend/Backends'
import Backend from './views/backend/Backend'
import NewBackend from './views/backend/NewBackend'
import Users from './views/user/Users'
import User from './views/user/User'
import NewUser from './views/user/NewUser'
import Profile from './views/Profile'
import NotFound from './views/NotFound'

export default new Router({
    routes: [
        {
            path: '/',
            redirect: 'dashboard',
            name: 'home',
        },
        {
            path: '/profile',
            name: 'profile',
            component: Profile
        },
        {
            path: '/dashboard',
            name: 'dashboard',
            component: Dashboard
        },
        {
            path: '/packages',
            name: 'packages',
            component: Packages
        },
        {
            path: '/new-package',
            name: 'new_package',
            component: NewPackage
        },
        {
            path: '/packages/:name',
            component: Package,
            props: true,
            redirect: { name: 'package' },
            children: [
                {
                    path: 'data',
                    name: 'package',
                    component: PackageDetails
                },
                {
                    path: 'dowloads',
                    name: 'package_dowloads',
                    component: PackageDowloads
                }
            ]
        },
        {
            path: '/applications',
            name: 'applications',
            component: Applications,

        },
        {
            path: '/new-application',
            name: 'new_application',
            component: NewApplication
        },
        {
            path: '/applications/:id',
            component: Application,
            props: true,
            children: [
                {
                    path: 'environments',
                    name: 'application',
                    component: ApplicationEnvironments
                },
                {
                    path: 'members',
                    name: 'application_members',
                    component: ApplicationMembers
                }
            ]
        },
        {
            path: '/admin/users',
            name: 'admin_users',
            component: Users
        },
        {
            path: '/admin/users/:id',
            name: 'admin_user',
            props: true,
            component: User
        },
        {
            path: '/new-user',
            name: 'new_user',
            component: NewUser
        },
        {
            path: '/admin/backends',
            name: 'admin_backends',
            component: Backends
        },
        {
            path: '/admin/backends/:id',
            component: Backend,
            name: 'admin_backend',
            props: true,
        },
        {
            path: '/new-backend',
            name: 'new_backend',
            component: NewBackend
        },
        {
            path: '/404',
            component: NotFound
        },
        {
            path: '*',
            redirect: '/404'
        },
    ]
})
