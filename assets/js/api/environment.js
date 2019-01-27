import axios from 'axios'

export default {

    findAll: (application) => {
        return axios.get('/api/applications/' + application.id + '/environments').catch(function (error) {
            console.log(error);
        });
    },

    create: (application, data) => {
        return axios.post('/api/applications/' + application.id + '/environments', data).catch(function (error) {
            console.log(error);
        });
    },

    update: (application, data) => {
        return axios.put('/api/applications/' + application.id + '/environments/' + data.id, data).catch(function (error) {
            console.log(error);
        });
    },

    delete: (application, id) => {
        return axios.delete('/api/applications/' + application.id + '/environments/' + id).catch(function (error) {
            console.log(error);
        });
    }
}