<template>
    <div class="input-group">
        <span class="input-group-prepend">
            <div class="input-group-text bg-transparent">
                <feather type="search"></feather>
            </div>
        </span>
        <b-form-input
                class="search-input"
                v-bind:value="value"
                v-on:input="$emit('input', $event)"
                type="text"
                :placeholder="placeholder"></b-form-input>
    </div>
</template>

<script>
    export default {
        name: "search-input",
        props: ['value', 'placeholder']
    }
</script>