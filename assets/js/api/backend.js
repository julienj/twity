import axios from 'axios'

export default {

    search: (page, query = null) => {
        return axios.get('/api/backends', {params: {'page': page, 'query': query}}).catch(function (error) {
            console.log(error);
        });
    },

    find: (id) => {
        return axios.get('/api/backends/' + id).catch(function (error) {
            console.log(error);
        });
    },

    update: (backend) => {
        return axios.put('/api/backends/' + backend.id, backend).catch(function (error) {
            console.log(error);
        });
    },

    remove: (id) => {
        return axios.delete('/api/backends/' + id).catch(function (error) {
            console.log(error);
        });
    },

    create: (backend) => {
        return axios.post('/api/backends', backend);
    },

    types: () => {
        return [
            { value: 'github', text: 'Github Personal access tokens' },
            { value: 'gitlab', text: 'Gitlab private tokens' }
        ];
    }

}