<template>

    <div>

        <div class="card bg-grey">

            <div class="card-header">
                <div class="float-right actions">
                    <b-button variant="primary" :to="{ name: 'new_user'}">
                        Add user
                    </b-button>
                </div>
                <h2>Users</h2>
            </div>

            <div class="card-body">
                <div class="row">
                    <div class="col-6">
                        <loader v-show="loading"></loader>
                    </div>
                    <div class="col-6">
                        <search-input class="light-search" v-model="query" placeholder="Search user"></search-input>
                    </div>
                </div>
            </div>
            
            <ul class="list-group list-group-flush" v-show="!loading">
                <router-link
                        v-for="user in users"
                        :key="user.id"
                        :to="{ name: 'admin_user', params: { id: user.id } }"
                        class="list-group-item list-group-item-action"
                >
                    {{ user.fullName }} ({{user.username}})

                    <div class="float-right">
                        <feather type="gitlab" v-if="user.type==='gitlab'"></feather>
                        {{ getRoleText(user.role) }}
                    </div>

                </router-link>
                <li v-if="hasMore && !loading" class="list-group-item">
                    <b-button @click="load(true)">
                        Load more
                    </b-button>
                </li>
            </ul>
        </div>

        <empty-state
                v-if="users.length === 0 && !loading"
                text="Empty in users"
                :show-reset="query"
                v-on:reset="reset()" />
    </div>

</template>

<script>

    import UserApi from '../../api/user'
    import Loader from '../../components/Loader'
    import EmptyState from '../../components/EmptyState'
    import SearchInput from '../../components/SearchInput'
    import _ from 'lodash'

    export default {
        name: "users",
        components: {
            Loader,
            EmptyState,
            SearchInput
        },
        data: () => ({
            users: [],
            total: null,
            query: null,
            loading: false,
            page: 0,
            hasMore: false,
        }),
        methods: {
            load: function (append) {
                let data = this;
                data.loading = true;
                data.page++;

                UserApi.search(data.page, data.query)
                    .then(function (response) {

                        if (!append) {
                            data.users = [];
                        }

                        data.users.push.apply(data.users, Object.values(response.data.items));
                        data.total = response.data.total;

                        data.hasMore = (Object.values(response.data.items).length === 20);

                    })
                    .then(function () {
                        data.loading = false;
                    })
                ;
            },
            reset: function () {
                this.query = null;
                this.clear();
                this.load();

            },
            clear: function () {
                this.page = 0;
                this.hasMore = false;
                this.backends = [];
                this.total = null;
            },
            getRoleText: function (role) {
                let text = role.substring(5);
                return text.charAt(0) + text.slice(1).toLowerCase();
            }
        },
        mounted: function () {
            this.load(false);
            this.debouncedLoad = _.debounce(this.load, 500)
        },
        watch: {
            'query': function(){
                this.loading = true;
                this.clear();
                this.debouncedLoad(false);
            }
        }
    }
</script>
