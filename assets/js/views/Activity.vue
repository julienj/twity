<template>
    <div class="card bg-grey">

        <div class="card-header">
            <h2>Activity</h2>
        </div>

        <div class="card-body">
            <p v-if="activity.length > 0">The following packages are waiting for update :</p>
            <p v-else>
                No task in progress
                <feather class="feather-30" type="smile"></feather>
            </p>
        </div>

        <b-list-group v-if="activity.length > 0">
            <b-list-group-item v-for="item in activity" :key="item">
                <b-spinner small variant="light" type="grow"></b-spinner>
                {{ item }}
            </b-list-group-item>
        </b-list-group>
    </div>
</template>


<script>


    import { mapState } from 'vuex'


    export default {
        name: "activity",
        computed: mapState({
            activity: state => state.activity.activity,
        })


    }
</script>
