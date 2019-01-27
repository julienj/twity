<template>
    <div>

        <loader v-show="loading"></loader>

        <div v-show="!loading" class="container stats">
            <div class="row">
                <div class="col-sm">
                    <feather class="feather-60" type="package"></feather>
                    {{ stats.packages }} packages
                </div>
                <div class="col-sm">
                    <feather class="feather-60" type="monitor"></feather>
                    {{ stats.applications }} applications
                </div>
                <div class="col-sm">
                    <feather class="feather-60" type="download-cloud"></feather>
                    {{ stats.weekly_downloads }} dowloads last 7 days
                </div>
            </div>
        </div>


        <div v-show="!loading"  class="card bg-grey">
            <div class="card-header">
                Get started
            </div>
            <div class="card-body">
                <h6><b>Step 1 : </b>configure authentication globally</h6>
                <pre class="shell">{{ authCmd }}</pre>

                <h6><b>Step 2 : </b>Enable custom repository</h6>
                <pre v-html="repositoriesConfig"></pre>
            </div>
        </div>

    </div>

</template>


<script>

    import ApiStats from '../api/stats'
    import { mapState } from 'vuex'
    import Loader from '../components/Loader'
    import Composer from '../services/composer'

    export default {
        name: "Dashboard",
        components: {
            Loader
        },
        mounted: function () {
            ApiStats.load().then( (response) => {
                this.stats = response.data;
                this.loading = false;
            });
        },
        data: () => ({
            stats: {},
            loading: true
        }),
        computed: {
            'repositoriesConfig': function () {
                return Composer.repositoriesConfig();
            },
            'authCmd': function () {
                return Composer.authCmd(this.user.username, this.user.token);
            },
            ...mapState({
                // arrow functions can make the code very succinct!
                user: state => state.user.user
            })
        }
    }
</script>
