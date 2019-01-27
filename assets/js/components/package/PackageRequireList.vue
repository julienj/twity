<template>


    <div class="card bg-grey require-list">
        <div class="card-header">
            {{ title }}
        </div>
        <ul class="list-group list-group-flush">
            <li v-for="(version, item) in list" :key="item" class="list-group-item d-flex  justify-content-between align-items-center">
                {{item}}
                <b-badge>{{version}}</b-badge>
            </li>
            <li v-if="!list" class="list-group-item">
                <i>nothing</i>
            </li>
        </ul>
    </div>

</template>

<script>

    export default {
        name: "package-require-list",
        props: ['list', 'title']
    }
</script>
