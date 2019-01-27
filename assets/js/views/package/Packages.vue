<template>

    <div>

        <div class="card bg-grey">
            <div class="card-body">
                <search-input v-model="query" placeholder="Search package"></search-input>
            </div>
            <div class="card-footer">

                <b-nav tabs class="card-header-tabs">
                    <b-nav-item @click="filter = null" :active="filter === null">
                        All <span v-if="!loading">({{ total || 0 }})</span>
                    </b-nav-item>
                    <b-nav-item @click="filter = 'vcs'" :active="filter === 'vcs'">
                        Private <span v-if="!loading">({{ facets.vcs || 0}})</span>
                    </b-nav-item>
                    <b-nav-item @click="filter = 'composer'" :active="filter === 'composer'">
                        Mirrored <span v-if="!loading">({{ facets.composer || 0 }})</span>
                    </b-nav-item>
                </b-nav>
            </div>
        </div>

        <router-link v-for="provider in providers" :key="provider.id"  :to="{ name: 'package', params: { name: provider.name } }">
            <div class="card bg-grey list-item" >
                <div class="card-body">
                    <h4>{{ provider.name }}</h4>
                    <p>{{ provider.description }}</p>
                </div>
            </div>
        </router-link>

        <loader v-show="loading"></loader>

        <empty-state
                v-if="providers.length === 0 && !loading"
                text="Empty in packages"
                :show-reset="filter || query"
                v-on:reset="reset()" />


        <b-button
                v-if="hasMore && !loading"
                @click="load(true)"
        >
            Load more
        </b-button>

    </div>

</template>

<script>

    import ProviderApi from '../../api/provider'
    import Loader from '../../components/Loader'
    import EmptyState from '../../components/EmptyState'
    import SearchInput from '../../components/SearchInput'
    import _ from 'lodash'

    export default {
        name: "packages",
        components: {
            Loader,
            EmptyState,
            SearchInput
        },
        data: () => ({
            providers: [],
            facets: [],
            total: null,
            filter: null,
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

                ProviderApi.search(data.page, data.query, data.filter)
                    .then(function (response) {

                        if (!append) {
                            data.providers = [];
                        }

                        data.providers.push.apply(data.providers, Object.values(response.data.items));
                        data.facets = response.data.facets;
                        data.total = response.data.total;

                        data.hasMore = (Object.values(response.data.items).length === 20);

                    })
                    .then(function () {
                        data.loading = false;
                    })
                ;
            },
            reset: function () {
                this.filter = null;
                this.query = null;
                this.clear();
                this.load();

            },
            clear: function () {
                this.page = 0;
                this.hasMore = false;
                this.providers = [];
                this.facets = [];
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

            },
            'filter': function(){
                this.clear();
                this.load(false);
            }
        }
    }
</script>
