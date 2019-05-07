import activityApi from '../../api/activity'

export default {
    namespaced: true,
    state : {
        activity: [],
    },
    getters: {
        'isInProgress':  (state) => (provider) => {
            return state.activity.indexOf(provider) > -1;
        },
        'count': (state) => {
            return state.activity.length;
        }
    },
    actions: {
        'load': ({commit}) => {
            activityApi.get().then(function (response) {
                commit('setActivity', response.data);

                // Mercure
                if(response.headers.link) {
                    const hubUrl = response.headers.link.match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1];
                    const h = new URL(hubUrl);
                    h.searchParams.append('topic', 'http://twity.io/p/{provider}/{package}')
                    const es = new EventSource(h);
                    es.onmessage = e => {
                        let data = JSON.parse(e.data);

                        if(data.updateInProgress === true) {
                            commit('addProvider', data.provider);
                        } else {
                            commit('removeProvider', data.provider);
                        }
                    }
                }
            });
        }
    },

    mutations: {
        'setActivity': (state, value) => {
            state.activity = value;
        },
        'addProvider': (state, value) => {
            let index = state.activity.indexOf(value);
            if (index === -1) {
                state.activity.push(value);
            }
        },
        'removeProvider': (state, value) => {
            let index = state.activity.indexOf(value);
            if (index > -1) {
                state.activity.splice(index, 1);
            }
        }
    }
}
