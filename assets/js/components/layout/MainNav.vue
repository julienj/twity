<template>

    <b-nav vertical id="main-menu">

        <template v-for="(item, i) in menu">

            <li v-if="item.header" :key="i"><h4>{{ item.header }}</h4></li>

            <li v-else-if="item.divider" :key="i" class="divider"></li>

            <b-nav-item v-else :to="!item.href ? { name: item.name } : null" :href="item.href" :key="i">

                <feather :type="item.icon"></feather>

                <span>{{ item.title }}</span>

            </b-nav-item>

        </template>

    </b-nav>
</template>

<script>

    import menu from '../../menu';
    import { mapState } from 'vuex'

    export default {
        name: "main-nav",
        computed:  {
            menu: function () {
                return menu.filter(item => this.$store.getters['user/hasRole'](item.role));
            },
            ...mapState({
                user: state => state.user.user
            })
        }
    }
</script>
