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
                    <b-dropdown-form>
                        <b-form-group label=" " label-for="dropdown-form-search">
                            <b-form-input
                                v-model="search"
                                id="dropdown-form-search"
                                type="text"
                                size="sm"
                                placeholder="Search a version"
                            ></b-form-input>
                        </b-form-group>
                    </b-dropdown-form>
                    <package-versions type="branches" :versions="branches" :search="search" :update-version="setCurrentVersion">
                    </package-versions>
                    <package-versions type="tags" :versions="tags" :search="search" :update-version="setCurrentVersion">
                    </package-versions>
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
    import PackageVersions from '../../components/package/PackageVersions'

    export default {
        name: "package",
        props: ['name'],
        components: {
            Loader,
            ConfirmButton,
            PackageVersions
        },
        data: () => ({
            provider: {},
            loading: false,
            currentVersion: null,
            modalLogsShow: false,
            branches: [],
            tags: [],
            search: ''
        }),
        methods: {
            load: function (showLoader = true) {

                if (showLoader) {
                    this.loading = true;
                }

                ProviderApi.find(this.name)
                    .then((response) => {
                        this.provider = response.data;
                        if (this.provider.packages.length > 0) {
                            let versions = this.provider.packages.map(pack => pack.version);
                            const versionDev = versions.filter(version => version.endsWith('-dev')).sort(this.sortDesc);
                            const devVersion = versions.filter(version => version.startsWith('dev-')).sort(this.sortAsc);

                            this.currentVersion = this.provider.packages[this.provider.packages.length - 1].version;
                            this.branches = versionDev.concat(devVersion);
                            this.tags = versions.filter(version => !version.startsWith('dev-') && !version.endsWith('-dev')).sort(this.sortDesc);
                        }
                    })
                    .finally(() => {
                        this.loading = false;
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
                });
                this.loading = true;
                this.provider = {};
            },
            hasRole (role) {
                return this.$store.getters['user/hasRole'](role);
            },
            sortAsc (a, b) {
                return a.toLowerCase().localeCompare(b.toLowerCase());
            },
            sortDesc (a, b) {
                return this.sortAsc(b, a);
            },
            setCurrentVersion (version) {
                this.currentVersion = version;
                this.search = '';
            }
        },
        computed: {
            currentPackage: function () {
                if(this.provider.packages) {
                    return this.provider.packages.find( p => p.version === this.currentVersion );
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
