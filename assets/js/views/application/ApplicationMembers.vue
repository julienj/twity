<template>

    <div>
        <loader v-show="loading"></loader>


        <div class="card bg-grey" v-show="!loading">

            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <label>User</label>
                        <v-select v-model="newAccess.user" label="fullName" :filterable="false" :options="users" @search="onSearchUser"></v-select>
                    </div>
                    <div class="col">
                        <label>Access</label>
                        <v-select v-model="newAccess.access" :options="accessType"></v-select>
                    </div>
                    <div class="col">
                        <b-button variant="primary" style="margin-top: 36px;" @click="create()"
                            :disabled="!newAccess.access || !newAccess.user">
                            Add
                        </b-button>
                    </div>
                </div>
            </div>

            <!-- list -->
            <ul class="list-group list-group-flush" v-show="!loading">
                <li
                        v-for="access in accesses"
                        :key="access.user.id"
                        class="list-group-item"
                >
                    {{ access.user.fullName }} ({{ access.access }})

                    <div class="float-right">
                        <feather @click="remove(access.user.id)" type="trash-2" ></feather>
                    </div>

                </li>
            </ul>
            <empty-state
                    v-if="accesses.length === 0 && !loading"
                    text="No members"
                    :show-reset="false" />
        </div>
    </div>

</template>

<script>

    import AccessApi from '../../api/access'
    import UserApi from '../../api/user'
    import Loader from '../../components/Loader'
    import EmptyState from '../../components/EmptyState'
    import vSelect from 'vue-select/src/components/Select'
    import _ from 'lodash'

    export default {
        name: "application-members",
        props: ['application'],
        components: {
            Loader,
            EmptyState,
            vSelect
        },
        data () {
            return {
                accesses: [],
                loading: true,
                newAccess: {},
                users: [],
                accessType: [
                    {label: 'Owner', value: 'owner'},
                    {label: 'Master', value: 'master'},
                    {label: 'User', value: 'user'},
                ]
            }
        },
        methods: {
            load: function () {
                let data = this;
                data.loading = true;
                AccessApi.findAll(this.application.id)
                    .then(function (response) {
                        data.accesses = response.data;
                    })
                    .then(function () {
                        data.loading = false;
                    })
            },
            create: function() {
                let data = this;

                AccessApi.create(this.application.id, this.newAccess.user.id, this.newAccess.access.value).then(function () {
                    data.load();
                    data.newAccess = {};
                });
            },

            remove: function (userId) {
                let data = this;
                AccessApi.delete(this.application.id, userId).then(function () {
                    data.load();
                });
            },
            onSearchUser(search, loading) {
                loading(true);
                this.searchUser(loading, search, this);
            },
            searchUser: _.debounce((loading, search, vm) => {

                UserApi.autocomplete(search).then(response => {
                    vm.users = Object.values(response.data.items);
                    loading(false);
                });
            }, 350)

        },
        mounted: function () {
            this.load();
        }
    }
</script>
