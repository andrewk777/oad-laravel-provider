<template>
    <div @mouseover="$parent.second_menu_opened = false" class="top-bar">
        <div class="top-right-menu">
            <ul>
                <li>
                    <dropdown-menu  v-model="showDropdown.user" :right="true" :interactive="true">
                        <i class="fal fa-user"></i>
                        <div class="dd-menu" slot="dropdown">
                            <ul>
                                <li class="border-bottom">
                                    <span>Signed in as:<br>
                                     <b>{{ $store.state.user.name }}</b> ({{ $store.state.user.email }})</span>                                    
                                </li>
                                <li>
                                    <a href="" @click.prevent="$parent.confirmLogout"><i class="far fa-power-off"></i> Logout</a>                                 
                                </li>
                            </ul>
                        </div>
                    </dropdown-menu>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
//https://innologica.github.io/vue-dropdown-menu/#installation
export default {
    data() {
        return {
            showDropdown: {
                user: false
            }
        }
    }
}
</script>