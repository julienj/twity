<template>

    <div>

        <loader v-show="loading"></loader>

        <div v-show="!loading" class="card bg-grey">
            <div class="card-header">

                <div class="float-right actions" v-if="access == 'owner'">
                    <a @click="editMode = true"><feather type="edit-3"></feather></a>
                </div>

                <router-link :to="{ 'name': 'applications'}" class="back float-left">
                    <feather type="chevron-left"></feather>
                </router-link>
                <h2 class="float-left">{{ application.name }}</h2>

            </div>
            <div v-if="!editMode" class="card-body">
                {{ application.description }}
            </div>
            <div v-else class="card-body">


                <b-form-group id="name"
                              label="name"
                              label-for="name"
                >
                    <b-input-group>
                        <b-form-input id="name" v-model="editApplication.name"></b-form-input>
                    </b-input-group>
                </b-form-group>


                <b-form-group id="description"
                              label="description"
                              label-for="description"
                >
                    <b-input-group>
                        <b-form-textarea id="description"
                                         v-model="editApplication.description"
                                         :rows="3"
                                         :max-rows="6">
                        </b-form-textarea>
                    </b-input-group>
                </b-form-group>


                <b-btn variant="success" :disabled="!editApplication.name || editApplication.name.lenght > 0" @click="update()">Save</b-btn>
                <confirm-button text="Delete" variant="danger" @confirm="remove()" confirm-message="Environments tokens will stop working immediately when you delete application. Composer operations still using the old token will fail." ></confirm-button>
                <b-btn variant="link" @click="editMode = false">Cancel</b-btn>


            </div>
            <div class="card-footer">

                <b-nav tabs class="card-header-tabs">
                    <b-nav-item :to="{'name': 'application'}">
                        Environments
                    </b-nav-item>
                    <b-nav-item :to="{'name': 'application_members'}" v-if="access == 'owner'">
                        Members
                    </b-nav-item>
                </b-nav>
            </div>
        </div>

        <router-view class="mt-4" v-if="application.id" :access="access" :application="application"></router-view>

    </div>
</template>

<script>

    import ApplicationApi from '../../api/application'
    import Loader from '../../components/Loader'
    import ConfirmButton from '../../components/ConfirmButton'
    import _ from 'lodash'

    export default {
        name: "application",
        props: ['id'],
        components: {
            Loader,
            ConfirmButton
        },
        data: () => ({
            application: {},
            loading: false,
            editMode: false,
            access: null,
            editApplication: {}
        }),
        methods: {
            load: function () {
                let data = this;
                data.loading = true;
                ApplicationApi.find(this.id)
                    .then(function (response) {
                        data.application = response.data.item;
                        data.access = response.data.access;
                        data.editApplication = _.clone(data.application);
                    })
                    .then(function () {
                        data.loading = false;
                    })
                ;
            },

            update: function () {
                let data = this;
                ApplicationApi.update(this.editApplication).then(function () {
                    data.load();
                    data.editMode = false;
                });
            },

            remove: function () {
                ApplicationApi.delete(this.application.id).then(() => {
                    this.$router.push({ name: 'applications' })
                });
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