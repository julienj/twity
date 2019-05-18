import axios from 'axios'

export default {

    search: (page, query = null, type = null) => {
        return axios.get('/api/providers', {params: {'page': page, 'query': query, 'type': type}}).catch(function (error) {
            console.log(error);
        });
    },

    find: (name) => {
        return axios.get('/api/providers/' + name);
    },

    getStats: (name, version) => {
        return axios.get('/api/downloads/' + name + '/' + version).catch(function (error) {
            console.log(error);
        });
    },

    refresh: (name) => {
        return axios.put('/api/providers/' + name).catch(function (error) {
            console.log(error);
        });
    },

    remove: (name) => {
        return axios.delete('/api/providers/' + name).catch(function (error) {
            console.log(error);
        });
    },

    create: (provider) => {
        return axios.post('/api/providers', provider);
    }
}
