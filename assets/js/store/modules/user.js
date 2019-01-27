import userApi from '../../api/user'

export default {
    namespaced: true,
    state : {
        user: {},
    },
    getters: {
        'hasRole':  (state) => (role) => {
            return state.user.roles && state.user.roles.includes(role);
        }
    },
    actions: {
        'load': ({commit}) => {
            userApi.me().then(function (response) {
                commit('setUser', response.data);
            });
        }
    },

    mutations: {
        'setUser': (state, value) => {
            state.user = value;
        },
    }
}
