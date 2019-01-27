import axios from 'axios'

export default {

    search: (page, query = null) => {
        return axios.get('/api/applications', {params: {'page': page, 'query': query}}).catch(function (error) {
            console.log(error);
        });
    },

    find: (id) => {
        return axios.get('/api/applications/' + id).catch(function (error) {
            console.log(error);
        });
    },

    update: (application) => {
        return axios.put('/api/applications/' + application.id, application).catch(function (error) {
            console.log(error);
        });
    },

    delete: (id) => {
        return axios.delete('/api/applications/' + id).catch(function (error) {
            console.log(error);
        });
    },

    create: (application) => {
        return axios.post('/api/applications', application);
    }

}