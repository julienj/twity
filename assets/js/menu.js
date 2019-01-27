export default  [
    {
        title: 'Home',
        group: 'repository',
        icon: 'home',
        name: 'dashboard',
        role: 'ROLE_USER'
    },
    {
        title: 'Packages',
        group: 'repository',
        icon: 'package',
        name: 'packages',
        role: 'ROLE_USER'
    },
    {
        title: 'Applications',
        group: 'repository',
        name: 'applications',
        icon: 'monitor',
        role: 'ROLE_USER'
    },
    { header: 'Administration', role: 'ROLE_ADMIN' },
    {
        title: 'Users',
        group: 'administration',
        icon: 'users',
        name: 'admin_users',
        role: 'ROLE_ADMIN'
    },
    {
        title: 'Backends',
        group: 'administration',
        icon: 'server',
        name: 'admin_backends',
        role: 'ROLE_ADMIN'
    },

];

