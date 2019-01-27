import axios from 'axios'

export default {

    me: () => {
        return axios.get('/api/me').catch(function (error) {
            console.log(error);
        });
    },
    regenerateMyToken: () => {
        return axios.post('/api/me/regenerate-token');
    },

    autocomplete: (query = null) => {
        return axios.get('/api/users/autocomplete', {params: {'query': query}}).catch(function (error) {
            console.log(error);
        });
    },


    search: (page, query = null) => {
        return axios.get('/api/users', {params: {'page': page, 'query': query}}).catch(function (error) {
            console.log(error);
        });
    },

    find: (id) => {
        return axios.get('/api/users/' + id).catch(function (error) {
            console.log(error);
        });
    },

    update: (user) => {
        return axios.put('/api/users/' + user.id, user);
    },

    remove: (id) => {
        return axios.delete('/api/users/' + id).catch(function (error) {
            console.log(error);
        });
    },

    create: (user) => {
        return axios.post('/api/users', user);
    },

    roles: function () {
        return [
            { value: 'ROLE_USER', text: 'User (read packages)' },
            { value: 'ROLE_MANAGER', text: 'Manager (Manage packages and applications)' },
            { value: 'ROLE_ADMIN', text: 'Admin (All access)' }
        ];
    }

}