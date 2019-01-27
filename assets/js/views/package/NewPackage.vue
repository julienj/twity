<template>

    <div>

        <loader v-show="loading"></loader>

        <b-alert :show="error !== false" variant="danger">
            {{ error }}
        </b-alert>

        <div v-show="!loading" class="card bg-grey">
            <div class="card-header">
                Create new package
            </div>
            <div class="card-body">
                <b-form-group id="name"
                              label="Name"
                              label-for="name">
                    <b-input-group>
                        <b-form-input id="name" v-model="provider.name"></b-form-input>
                    </b-input-group>
                </b-form-group>

                <b-form-group id="type"
                              label="Type"
                              label-for="type">
                    <b-input-group>
                        <b-form-select id="type" v-model="provider.type" :options="types" />
                    </b-input-group>
                </b-form-group>

                <b-form-group id="vcsUri"
                              label="Uri"
                              label-for="vcsUri"
                              v-if="provider.type == 'vcs'">
                    <b-input-group>
                        <b-form-input id="vcsUri" v-model="provider.vcsUri"></b-form-input>
                    </b-input-group>
                </b-form-group>

                <b-btn variant="success" :disabled="!provider.name || provider.name.lenght > 0" @click="create()">Create</b-btn>

            </div>
        </div>

    </div>
</template>

<script>

    import ProviderApi from '../../api/provider'
    import Loader from '../../components/Loader'

    export default {
        name: "new-package",
        components: {
            Loader
        },
        data: () => ({
            provider: {},
            loading: false,
            error: false,
            types: [
                { value: 'vcs', text: 'VCS Auto-detect (GitHub/Bitbucket/GitLab/...)' },
                { value: 'composer', text: 'Add package from packagist.org' }
            ]
        }),
        methods: {
            create: function () {
                let data = this;
                data.loading = true;
                data.error = false;
                ProviderApi.create(data.provider)
                    .then(function (response) {
                        data.$router.push({name: 'package', params: { name: data.provider.name}});
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