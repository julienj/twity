<template>
    <b-dropdown-group :id="type" :header="type">
        <b-dropdown-item-button v-for="version in filteredVersions" :key="version" @click="updateVersion(version)">
            {{ version }}
        </b-dropdown-item-button>
    </b-dropdown-group>
</template>

<script>
    export default {
        name: "package-versions",
        props: {
            type: {
                type: String,
                required: true
            },
            versions: {
                type: Array,
                required: true
            },
            search: {
                type: String,
                required: true
            },
            updateVersion: {
                type: Function,
                required: true
            }
        },
        computed: {
            filteredVersions() {
                if ('' !== this.search) {
                    return this.versions.filter(version => {
                        return version.toLowerCase().includes(this.search.toLowerCase())
                    })
                }

                return this.versions;
            }
        }
    }
</script>
