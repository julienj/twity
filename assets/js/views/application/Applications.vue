<template>

    <div>

        <div class="card bg-grey">
            <div class="card-body">
                <search-input v-model="query" placeholder="Search application"></search-input>
            </div>
        </div>

        <router-link v-for="application in applications" :key="application.id"  :to="{ name: 'application', params: { id: application.id } }">
            <div class="card bg-grey list-item" >
                <div class="card-body">
                    <h4>{{ application.name }}</h4>
                    <p>{{ application.description }}</p>
                </div>
            </div>
        </router-link>

        <loader v-show="loading"></loader>

        <empty-state
                v-if="applications.length === 0 && !loading"
                text="Empty in applications"
                :show-reset="query"
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

    import ApplicationApi from '../../api/application'
    import Loader from '../../components/Loader'
    import EmptyState from '../../components/EmptyState'
    import SearchInput from '../../components/SearchInput'
    import _ from 'lodash'

    export default {
        name: "applications",
        components: {
            Loader,
            EmptyState,
            SearchInput
        },
        data: () => ({
            applications: [],
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

                ApplicationApi.search(data.page, data.query)
                    .then(function (response) {

                        if (!append) {
                            data.applications = [];
                        }

                        data.applications.push.apply(data.applications, Object.values(response.data.items));
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
                this.applications = [];
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
