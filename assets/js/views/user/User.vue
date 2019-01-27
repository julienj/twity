<template>

    <div>

        <loader v-show="loading"></loader>

        <b-alert :show="error !== false" variant="danger">
            {{ error }}
        </b-alert>


        <div v-if="!loading" class="card bg-grey">
            <div class="card-header">
                <router-link :to="{ 'name': 'admin_users'}" class="back float-left">
                    <feather type="chevron-left"></feather>
                </router-link>
                <h2 class="float-left">{{ title }}</h2>
            </div>
            <div class="card-body">
                <b-form-group id="username"
                              label="Username"
                              label-for="username">
                    <b-input-group>
                        <b-form-input id="username" v-model="user.username"></b-form-input>
                    </b-input-group>
                </b-form-group>

                <b-form-group id="email"
                              label="Email"
                              label-for="email">
                    <b-input-group>
                        <b-form-input id="email" v-model="user.email"></b-form-input>
                    </b-input-group>
                </b-form-group>

                <b-form-group id="fullName"
                              label="Full name"
                              label-for="fullName">
                    <b-input-group>
                        <b-form-input id="fullName" v-model="user.fullName"></b-form-input>
                    </b-input-group>
                </b-form-group>

                <b-form-group id="role"
                              label="Role"
                              label-for="role">
                    <b-input-group>
                        <b-form-select id="role" v-model="user.role" :options="roles" />
                    </b-input-group>
                </b-form-group>

                <b-btn variant="primary" @click="update()">Update</b-btn>
                <confirm-button text="Delete" variant="danger" @confirm="remove()" confirm-message="Remove user ?" ></confirm-button>
            </div>
        </div>

    </div>
</template>

<script>

    import UserApi from '../../api/user'
    import Loader from '../../components/Loader'
    import ConfirmButton from '../../components/ConfirmButton'

    export default {
        name: "user",
        props: ['id'],
        components: {
            Loader,
            ConfirmButton
        },
        data: () => ({
            user: {},
            loading: false,
            error: false,
            title: null,
            roles: UserApi.roles()
        }),
        methods: {
            load: function () {

                let data = this;
                data.loading = true;

                UserApi.find(this.id)
                    .then(function (response) {
                        data.user = response.data;
                        data.title = response.data.fullName;
                    })
                    .then(function () {
                        data.loading = false;
                    })
                ;
            },
            update: function() {
                let data = this;
                data.loading = true;
                data.error = false;
                UserApi.update(this.user)
                    .then(function (response) {
                        data.title = response.data.fullName;
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
                UserApi.remove(this.id).then(() => {
                  this.$router.push({ name: 'admin_users' });
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