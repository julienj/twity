<template>

    <div>

        <loader v-show="loading"></loader>

        <b-alert :show="error !== false" variant="danger">
            {{ error }}
        </b-alert>

        <div v-show="!loading" class="card bg-grey">
            <div class="card-header">
                Create new user
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


                <b-btn variant="success" @click="create()">Create</b-btn>

            </div>
        </div>

    </div>
</template>

<script>

    import UserApi from '../../api/user'
    import Loader from '../../components/Loader'

    export default {
        name: "new-user",
        components: {
            Loader
        },
        data: () => ({
            user: {},
            loading: false,
            error: false,
            roles: UserApi.roles()
        }),
        methods: {
            create: function () {
                let data = this;
                data.loading = true;
                data.error = false;
                UserApi.create(data.user)
                    .then(function (response) {
                        data.$router.push({name: 'admin_user', params: { id: response.data.id}});
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