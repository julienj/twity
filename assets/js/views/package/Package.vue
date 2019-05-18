<template>

    <div>

        <loader v-show="loading"></loader>

        <div v-show="!loading" class="card bg-grey">
            <div class="card-header">

                <!-- action -->
                <div v-if="!isInProgress" class="float-right actions">
                    <div v-if="provider.lastUpdate">
                        Last update {{ provider.lastUpdate | moment('from') }} - <a v-if="provider.logs" @click="modalLogsShow = true">view logs</a>
                    </div>
                    <div class="float-right actions-btn" v-if="hasRole('ROLE_MANAGER')">
                        <b-btn variant="primary" @click="refresh">Update</b-btn>
                        <confirm-button text="Delete" variant="danger" @confirm="remove()" confirm-message="Remove package ?" ></confirm-button>
                    </div>
                </div>
                <div v-else class="float-right loader">
                    Background job is running.
                    <loader></loader>
                </div>

                <router-link :to="{ 'name': 'packages'}" class="back float-left">
                    <feather type="chevron-left"></feather>
                </router-link>
                <h2 class="float-left">{{ provider.name }}</h2>

                <!-- versions dropdown -->
                <b-dropdown class="ml-3" :text="currentVersion">
                    <b-dropdown-item  v-for="version in versions" :key="version" @click="currentVersion = version">
                        {{ version }}
                    </b-dropdown-item>
                </b-dropdown>

            </div>
            <div class="card-body">
                <b-alert :show="provider.hasError" variant="danger">
                    This package is not valid.
                </b-alert>
                {{ provider.description }}
            </div>
            <div class="card-footer">

                <b-nav tabs class="card-header-tabs">
                    <b-nav-item :to="{'name': 'package'}">
                        Package
                    </b-nav-item>
                    <b-nav-item :to="{'name': 'package_dowloads'}">
                        Downloads
                    </b-nav-item>
                </b-nav>
            </div>
        </div>


        <router-view class="mt-4" v-if="currentPackage" :currentPackage="currentPackage"></router-view>

        <b-modal v-model="modalLogsShow" size="lg" title="Logs" :hide-footer="true">
           <div class="code" v-html="provider.logs"></div>
        </b-modal>

    </div>
</template>

<script>

    import ProviderApi from '../../api/provider'
    import Loader from '../../components/Loader'
    import ConfirmButton from '../../components/ConfirmButton'

    export default {
        name: "package",
        props: ['name'],
        components: {
            Loader,
            ConfirmButton
        },
        data: () => ({
            provider: {},
            loading: false,
            currentVersion: null,
            modalLogsShow: false
        }),
        methods: {
            load: function (showLoader = true) {

                let data = this;
                if(showLoader) {
                    data.loading = true;
                }

                ProviderApi.find(this.name)
                    .then(function (response) {
                        data.provider = response.data;
                        if(data.provider.packages.length > 0) {
                            data.currentVersion = data.provider.packages[data.provider.packages.length - 1].version;
                        }
                    })
                    .then(function () {
                        data.loading = false;
                    })
                ;
            },
            refresh: function () {
                this.provider.updateInProgress = true;
                this.$store.commit('activity/addProvider', this.name);
                ProviderApi.refresh(this.provider.name);
            },

            remove: function () {
                ProviderApi.remove(this.name).then(() => {
                  this.$router.push({ name: 'packages' });
                })
                this.loading = true;
                this.provider = {};
            },
            hasRole (role) {
                return this.$store.getters['user/hasRole'](role);
            }

        },
        computed: {
            currentPackage: function () {
                if(this.provider.packages) {
                    return this.provider.packages.find( p => p.version === this.currentVersion );
                }

                return null;
            },
            versions: function () {
                if(this.provider.packages) {
                    return this.provider.packages.map(p => p.version);
                }

                return null;
            },
            isInProgress () {
                return this.$store.getters['activity/isInProgress'](this.name);
            }
        },
        mounted: function () {
            this.load();

            this.$store.watch(
                (state, getters) => getters['activity/isInProgress'](this.name), (inProgress) => {
                    if (!inProgress) {
                        this.load(false);
                    }
                },
            );
        },
        watch: {
            'name' : function () {
                this.load();
            }
        }

    }
</script>
