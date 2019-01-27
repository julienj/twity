<template>
    <div class="empty-state bg-grey">
        <img class="img mx-auto d-block" src="../../images/empty-state.svg" />
        <p class="text-center">
            {{ text }}
        </p>
        <b-button class="mx-auto d-block"
                  variant="primary"
                  v-if="showReset"
                  @click="$emit('reset')"
        >
            View all
        </b-button>
    </div>
</template>

<script>
    export default {
        name: "empty-state",
        props: ['text', 'showReset']
    }
</script>