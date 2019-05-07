import axios from 'axios'

export default {

    get: () => {
        return axios.get('/api/activity').catch(function (error) {
            console.log(error);
        });
    }
}
