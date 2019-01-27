<template>

    <div>

        <div class="card bg-grey">

            <div class="card-header">
                <div class="float-right actions">
                    <b-button variant="primary" :to="{ name: 'new_backend'}">
                        Add backend
                    </b-button>
                </div>
                <h2>Backends</h2>
            </div>

            <div class="card-body">
                <p>Backends let Twity access your private repositories.</p>
                <div class="row">
                    <div class="col-6">
                        <loader v-show="loading"></loader>
                    </div>
                    <div class="col-6">
                        <search-input class="light-search" v-model="query" placeholder="Search backend"></search-input>
                    </div>
                </div>

            </div>

            <ul class="list-group list-group-flush" v-show="!loading">
                <router-link
                        v-for="backend in backends"
                        :key="backend.id"
                        :to="{ name: 'admin_backend', params: { id: backend.id } }"
                        class="list-group-item list-group-item-action"
                >
                    {{ backend.domain }}
                </router-link>
                <li v-if="hasMore && !loading" class="list-group-item">
                    <b-button @click="load(true)">
                        Load more
                    </b-button>
                </li>
            </ul>
        </div>

        <empty-state
                v-if="backends.length === 0 && !loading"
                text="Empty in backends"
                :show-reset="query"
                v-on:reset="reset()" />



    </div>

</template>

<script>

    import BackendApi from '../../api/backend'
    import Loader from '../../components/Loader'
    import EmptyState from '../../components/EmptyState'
    import SearchInput from '../../components/SearchInput'
    import _ from 'lodash'

    export default {
        name: "backends",
        components: {
            Loader,
            EmptyState,
            SearchInput
        },
        data: () => ({
            backends: [],
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

                BackendApi.search(data.page, data.query)
                    .then(function (response) {

                        if (!append) {
                            data.backends = [];
                        }

                        data.backends.push.apply(data.backends, Object.values(response.data.items));
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
