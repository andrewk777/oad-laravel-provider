import Vue from 'vue';
import VueToast from 'vue-toast-notification';
import 'vue-toast-notification/dist/theme-default.css';
// import 'vue-toast-notification/dist/theme-sugar.css';
import Swal from 'sweetalert2' 

Vue.use(VueToast);

export const utils = {
    c_notify(text,type,position) {
        var pos = typeof position !== 'undefined' ? position : 'top-right'
        Vue.$toast.open({
            position: pos,
            message: text,
            type: type,
            duration: 3000
            // all other options may go here
        });
    },
    swalConfigm: Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-outline-primary mr-3',
            cancelButton: 'btn btn-outline-danger'
        },
        buttonsStyling: false
    }),
    Swal: Swal
}