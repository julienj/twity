import axios from 'axios'

export default {

    load: () => {
        return axios.get('/api/stats').catch(function (error) {
            console.log(error);
        });
    }

}