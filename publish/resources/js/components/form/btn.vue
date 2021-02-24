<template>
    <button :type="c_type" :class="c_css" class="btn rounded-sm" :disabled="disabled" @click="clicked">
        <div v-show="disabled" :class="c_spinner" class="spinner-grow spinner-grow-sm" role="status" style="margin-bottom: 3px; marging-right: 3px;">
            <span class="sr-only">Loading...</span>
        </div>
        <slot></slot>
    </button>
</template>

<script>
export default {
    props: ['type','css','loading','variant','emt','spinner'],
    computed: {
        c_type() {
            return  typeof this.type !== 'undefined' ? this.type : 'button';
        },
        c_variant() {
            return  typeof this.variant !== 'undefined' ? ' btn-' + this.variant : ' btn-primary';
        },
        c_css() {
            var css = typeof this.css !== 'undefined' ? this.css : '';
            return css + this.c_variant;
        },
        c_spinner() {
            return typeof this.variant !== 'undefined' ? this.variant : 'text-light'
        },
        disabled() {
            return typeof this.loading !== 'undefined' ? this.loading : this.$parent.loading
        }
    },
    methods: {
        clicked() {
            if (typeof this.emt === 'string') {
                this.$emit(this.emt)
            } else if (typeof this.emt === 'object') {
                this.$emit(this.emt.evt,this.emt.data)
            } else {
                this.$emit('click');
            }
        }
    }
}
</script>