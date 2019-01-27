<template>
    <div>
        <nav-bar></nav-bar>

        <b-container fluid>
            <b-row>
                <b-col cols="2">
                    <main-nav></main-nav>
                </b-col>
                <b-col>
                    <b-container>
                        <router-view></router-view>
                    </b-container>
                </b-col>
            </b-row>

            <b-row>
                <div class="col footer">
                    @2019 | Julien Jacottet
                </div>
            </b-row>
        </b-container>

    </div>
</template>

<script>

    import NavBar from './components/layout/NavBar'
    import MainNav from './components/layout/MainNav'

    export default {
        components: {
            NavBar,
            MainNav
        },
        mounted: function () {
            this.$store.dispatch('user/load');
        },
    }
</script>

<style scoped>

</style>