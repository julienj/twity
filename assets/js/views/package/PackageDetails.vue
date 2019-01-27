<template>

    <div>

        <div class="card bg-grey list-item" >
            <div class="card-body">
                <div class="row package-info">
                    <div v-if="currentPackage.data.time" class="col">
                        <feather type="clock"></feather>
                        {{ currentPackage.data.time | moment("from") }}
                    </div>
                    <div v-if="currentPackage.data.homepage" class="col">
                        <feather type="feather"></feather>
                        <a :href="currentPackage.data.homepage">{{ currentPackage.data.homepage }}</a>
                    </div>
                    <div v-if="currentPackage.data.source.url" class="col">
                        <feather type="code"></feather>
                        <a :href="currentPackage.data.source.url">{{ currentPackage.data.source.url }}</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col"><package-require-list title="Requires" :list="currentPackage.data.require"></package-require-list></div>
            <div class="col"><package-require-list title="Requires (dev)" :list="currentPackage.data['require-dev']"></package-require-list></div>
            <div class="col"><package-require-list title="Suggests" :list="currentPackage.data.suggests"></package-require-list></div>
        </div>
        <div class="row  mt-3">
            <div class="col"><package-require-list title="Provides" :list="currentPackage.data.provides"></package-require-list></div>
            <div class="col"><package-require-list title="Conflicts" :list="currentPackage.data.conflicts"></package-require-list></div>
            <div class="col"><package-require-list title="Replaces" :list="currentPackage.data.replaces"></package-require-list></div>
        </div>

    </div>

</template>

<script>

    import PackageRequireList from '../../components/package/PackageRequireList'

    export default {
        name: "package-details",
        props: ['currentPackage'],
        components: {
            PackageRequireList
        }
    }
</script>
