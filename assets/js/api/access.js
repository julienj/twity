import axios from 'axios'

export default {

    findAll: (applicationId) => {
        return axios.get('/api/applications/' + applicationId + '/access').catch(function (error) {
            console.log(error);
        });
    },

    create: (applicationId, userId, access) => {
        return axios.post('/api/applications/' + applicationId + '/access/' + userId, { access: access });
    },

    delete: (applicationId, userId) => {
        return axios.delete('/api/applications/' + applicationId + '/access/' + userId).catch(function (error) {
            console.log(error);
        });
    }
}