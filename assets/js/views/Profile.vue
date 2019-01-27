<template>

    <div>

        <div class="card bg-grey">
            <div class="card-header">
                Profile
            </div>
            <div class="card-body">
                <b-form-group id="username"
                              label="Username"
                              label-for="username"
                        >
                    <b-input-group>
                        <b-form-input id="username" v-model="user.username" readonly></b-form-input>
                    </b-input-group>
                </b-form-group>

                <b-form-group id="token"
                              label="Token"
                              label-for="token"
                >
                    <b-input-group>
                        <b-form-input id="token" v-model="user.token" readonly></b-form-input>
                        <b-input-group-append>
                            <confirm-button text="Regenerate" variant="danger" @confirm="regenerateToken" confirm-message="Old token will stop working immediately. Composer operations still using the old token will fail." ></confirm-button>
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>

            </div>
        </div>

    </div>

</template>

<script>

    import { mapState } from 'vuex'
    import apiUser from '../api/user'
    import ConfirmButton from '../components/ConfirmButton'

    export default {
        name: "Dashboard",
        components: {
            ConfirmButton
        },
        computed: {
            'composerConfig': function () {
                let host = window.location.hostname;
                return `composer config --global --auth http-basic.${host} ${this.user.username} ${this.user.token}`;
            },
            ...mapState({
                // arrow functions can make the code very succinct!
                user: state => state.user.user
            })
        },
        methods: {
            regenerateToken: function () {
                apiUser.regenerateMyToken()
                    .then(() => {
                        this.$store.dispatch('user/load');
                    })
                ;
            }
        }
    }
</script>
