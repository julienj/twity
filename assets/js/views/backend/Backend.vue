<template>

    <div>

        <loader v-show="loading"></loader>

        <b-alert :show="error !== false" variant="danger">
            {{ error }}
        </b-alert>


        <div v-if="!loading" class="card bg-grey">
            <div class="card-header">
                <router-link :to="{ 'name': 'admin_backends'}" class="back float-left">
                    <feather type="chevron-left"></feather>
                </router-link>
                <h2 class="float-left">{{ title }}</h2>
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

                <b-btn variant="primary" @click="update()">Update</b-btn>
                <confirm-button text="Delete" variant="danger" @confirm="remove()" confirm-message="Remove backend ?" ></confirm-button>
            </div>
        </div>

    </div>
</template>

<script>

    import BackendApi from '../../api/backend'
    import Loader from '../../components/Loader'
    import ConfirmButton from '../../components/ConfirmButton'

    export default {
        name: "backend",
        props: ['id'],
        components: {
            Loader,
            ConfirmButton
        },
        data: () => ({
            backend: {},
            loading: false,
            error: false,
            title: null,
            types: BackendApi.types()
        }),
        methods: {
            load: function () {

                let data = this;
                data.loading = true;

                BackendApi.find(this.id)
                    .then(function (response) {
                        data.backend = response.data;
                        data.title = response.data.domain;
                    })
                    .then(function () {
                        data.loading = false;
                    })
                ;
            },
            update: function() {
                let data = this;
                data.loading = true;
                BackendApi.update(this.backend)
                    .then(function (response) {
                        data.title = data.backend.domain;
                    })
                    .catch(function (error) {
                        if(error.response.status === 400) {
                            data.error = error.response.data.detail;
                        }
                    })
                    .then(function () {
                        data.loading = false;
                    })
                ;
            },
            remove: function () {
                BackendApi.remove(this.id).then(() => {
                  this.$router.push({ name: 'admin_backends' });
                })
                this.loading = true;
                this.backend = {};
            }
        },
        mounted: function () {
            this.load();
        },
        watch: {
            'id' : function () {
                this.load();
            }
        }

    }
</script>