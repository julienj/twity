<template>

    <div>

        <loader v-show="loading"></loader>

        <b-alert :show="error !== false" variant="danger">
            {{ error }}
        </b-alert>

        <div v-show="!loading" class="card bg-grey">
            <div class="card-header">
                Create new application
            </div>
            <div class="card-body">
                <b-form-group id="name"
                              label="Name"
                              label-for="name">
                    <b-input-group>
                        <b-form-input id="name" v-model="application.name"></b-form-input>
                    </b-input-group>
                </b-form-group>

                <b-form-group id="description"
                              label="Description"
                              label-for="description">
                    <b-input-group>
                        <b-form-textarea id="description" v-model="application.description"></b-form-textarea>
                    </b-input-group>
                </b-form-group>

                <b-btn variant="success" :disabled="!application.name || application.name.lenght > 0" @click="create()">Create</b-btn>

            </div>
        </div>

    </div>
</template>

<script>

    import ApplicationApi from '../../api/application'
    import Loader from '../../components/Loader'

    export default {
        name: "new-application",
        components: {
            Loader
        },
        data: () => ({
            application: {},
            loading: false,
            error: false
        }),
        methods: {
            create: function () {
                let data = this;
                data.loading = true;
                data.error = false;
                ApplicationApi.create(data.application)
                    .then(function (response) {
                        data.$router.push({name: 'application', params: { id: response.data.id}});
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