<template>

    <b-navbar :toggleable="true"  type="dark" variant="dark" :sticky="true">



        <b-navbar-brand :to="{name: 'dashboard'}">
            <img src="../../../images/logo.svg" alt="">
            <h1>Twity</h1>
        </b-navbar-brand>

        <b-collapse is-nav id="nav-collapse">

        <div class="divider-vertical ml-auto"></div>

        <b-navbar-nav v-if="hasRole('ROLE_MANAGER')">
            <b-nav-item-dropdown  no-caret>
                <template slot="button-content">
                    <feather class="feather-30" type="plus"></feather>
                </template>
                <b-dropdown-item :to="{ name: 'new_package' }">New package</b-dropdown-item>
                <b-dropdown-item :to="{ name: 'new_application' }">New application</b-dropdown-item>
            </b-nav-item-dropdown>
        </b-navbar-nav>

        <div class="divider-vertical" v-if="hasRole('ROLE_MANAGER')"></div>

        <b-navbar-nav>
            <b-nav-item-dropdown right>
                <template slot="button-content">
                    <img  class="avatar rounded-circle" :src="user.avatarUrl" alt="" >
                    <span>{{ user.fullName }}</span>
                </template>
                <b-dropdown-item :to="{'name': 'profile'}">Profile</b-dropdown-item>
                <b-dropdown-item href="/logout">Logout</b-dropdown-item>
            </b-nav-item-dropdown>
        </b-navbar-nav>

        </b-collapse>

    </b-navbar>
</template>

<script>

    import { mapState } from 'vuex'

    export default {
        name: "nav-bar",
        computed: mapState({
            user: state => state.user.user
        }),
        methods: {
            hasRole (role) {
                return this.$store.getters['user/hasRole'](role);
            }
        }
    }
</script>
