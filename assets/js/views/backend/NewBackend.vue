<template>

    <div>

        <loader v-show="loading"></loader>

        <b-alert :show="error !== false" variant="danger">
            {{ error }}
        </b-alert>

        <div v-show="!loading" class="card bg-grey">
            <div class="card-header">
                Create new backend
            </div>
            <div class="card-body">
                <b-form-group id="domain"
                              label="Domain"
                              label-for="domain">
                    <b-input-group>
                        <b-form-input id="domain" v-model="backend.domain"></b-form-input>
                    </b-input-group>
                </b-form-group>

                <b-form-group id="type"
                              label="Type"
                              label-for="type">
                    <b-input-group>
                        <b-form-select id="type" v-model="backend.type" :options="types" />
                    </b-input-group>
                </b-form-group>

                <b-form-group id="token"
                              label="Token"
                              label-for="token">
                    <b-input-group>
                        <b-form-input id="token" v-model="backend.token"></b-form-input>
                    </b-input-group>
                </b-form-group>

                <b-btn variant="success" @click="create()">Create</b-btn>

            </div>
        </div>

    </div>
</template>

<script>

    import BackendApi from '../../api/backend'
    import Loader from '../../components/Loader'

    export default {
        name: "new-backend",
        components: {
            Loader
        },
        data: () => ({
            backend: {},
            loading: false,
            error: false,
            types: BackendApi.types()
        }),
        methods: {
            create: function () {
                let data = this;
                data.loading = true;
                data.error = false;
                BackendApi.create(data.backend)
                    .then(function (response) {
                        data.$router.push({name: 'admin_backend', params: { id: response.data.id}});
                    }).catch(function (error) {
                        if(error.response.status === 400) {
                            data.error = error.response.data.detail;
                            data.loading = false;
                        }
                    })
                ;
            }
        }
    }
</script>