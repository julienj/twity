<template>
    <div style="display: inline-block;">
        <b-button :variant="variant" @click="modalShow = !modalShow">
            {{ text }}
        </b-button>
        <b-modal
                v-model="modalShow"
                @ok="$emit('confirm')"
                :ok-variant="variant"
                :ok-title="text"
                title="Are you sure">
            <p>{{ confirmMessage }}</p>
        </b-modal>
    </div>
</template>

<script>
    export default {
        name: "confirm-button",
        props: ['confirmMessage', 'variant', 'text'],
        data () {
            return {
                modalShow: false
            }
        }
    }
</script>