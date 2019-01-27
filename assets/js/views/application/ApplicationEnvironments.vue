<template>

    <div>
        <loader v-show="loading"></loader>

        <!-- list -->
        <div v-for="environment in environments" :key="environment.id" v-show="!loading"  class="card bg-grey mb-4">
            <div class="card-header">
                <div class="float-right actions" v-if="access == 'owner' || access == 'master'">
                    <a @click="displayEditForm(environment)"><feather type="edit-3"></feather></a>
                </div>
                {{ environment.name }}
            </div>
            <div v-if="editEnvironment.id == environment.id" class="card-body">
                <b-form-group id="edit_name"
                              label="name"
                              label-for="edit_name"
                >
                    <b-input-group>
                        <b-form-input id="edit_name" v-model="editEnvironment.name"></b-form-input>
                    </b-input-group>
                </b-form-group>

                <b-btn variant="success" :disabled="!editEnvironment.name || editEnvironment.name.lenght > 0" @click="update()">Save</b-btn>
                <confirm-button text="Delete" variant="danger" @confirm="remove(environment.id)" confirm-message="Environment token will stop working immediately when you delete environment. Composer operations still using the old token will fail." ></confirm-button>
                <b-btn variant="link" @click="editEnvironment = {}">Cancel</b-btn>
            </div>
            <div v-else class="card-body">
                <h6>Server authentication</h6>
                <pre class="shell">{{ authCmd(environment.id, environment.token) }}</pre>
            </div>
        </div>

        <!-- new env form -->
        <div v-show="displayForm"  class="card bg-grey mb-4">
            <div class="card-header">
               New Environment
            </div>
            <div class="card-body">
                <b-form-group id="name"
                              label="name"
                              label-for="name"
                >
                    <b-input-group>
                        <b-form-input id="name" v-model="newEnvironment.name"></b-form-input>
                    </b-input-group>
                </b-form-group>

                <b-btn variant="success" :disabled="!newEnvironment.name || newEnvironment.name.lenght > 0" @click="save()">Save</b-btn>
                <b-btn variant="link" @click="displayForm = false">Cancel</b-btn>
            </div>
        </div>

        <!-- new env btn -->
        <div v-show="!displayForm && !loading" class="card bg-grey-light" @click="displayForm = true" v-if="access == 'owner' || access == 'master'">
            <div class="card-body text-center">
                <feather class="feather-30" type="plus"></feather>
                <b>Add environment</b>
            </div>
        </div>
    </div>



</template>

<script>

    import EnvironmentApi from '../../api/environment'
    import Loader from '../../components/Loader'
    import Composer from '../../services/composer'
    import ConfirmButton from '../../components/ConfirmButton'
    import _ from 'lodash'

    export default {
        name: "application-environments",
        props: ['application', 'access'],
        components: {
            Loader,
            ConfirmButton
        },
        data () {
            return {
                environments: [],
                loading: true,
                displayForm: false,
                newEnvironment: {},
                editEnvironment: {}
            }
        },
        methods: {
            load: function () {
                let data = this;
                data.loading = true;
                EnvironmentApi.findAll(this.application)
                    .then(function (response) {
                        data.environments = response.data;
                    })
                    .then(function () {
                        data.loading = false;
                    })
            },
            save: function() {
                let data = this;
                EnvironmentApi.create(this.application, this.newEnvironment).then(function () {
                    data.load();
                    data.newEnvironment = {};
                    data.displayForm = false;
                });
            },

            displayEditForm: function (environment) {
                this.editEnvironment = _.clone(environment);
            },

            update: function () {
                let data = this;
                EnvironmentApi.update(this.application, this.editEnvironment).then(function () {
                    data.load();
                    data.editEnvironment = {};
                });
            },
            remove: function (id) {
                let data = this;
                EnvironmentApi.delete(this.application, id).then(function () {
                    data.load();
                    data.editEnvironment = {};
                });
            },
            authCmd: function (username, token) {
                return Composer.authCmd(username, token);
            }
        },
        mounted: function () {
            this.load();
        }
    }
</script>
