<template>
<div :class="noLabelPadding">
    <b-button-group :class="data.groupClass">
        <b-button v-for="(item, key) in options" @click="setValue(item.code)" :pressed="item.code == toggleValue" variant="outline-primary" :key="key">
            {{ item.label }}
        </b-button>
    </b-button-group>
</div>
</template>

<script>
import field from '@/mixins/field';

export default {
    mixins: [field],
    data() {
        return {
            toggleValue: this.value
        }
    },
    watch: {
        'data.options': function(val) {
            this.options = this.obj2Options(val);
        },
        value(val) {
            this.toggleValue = val;
        }
    },
    computed: {
        noLabelPadding() {
           return this.data.label ? '' : 'pt-25'
        }
    },
    methods: {
        setValue(val) {
            this.toggleValue = this.toggleValue != val ? val : ''
            this.emitChild(this.toggleValue)
        }
    },
    mounted() {
        this.options = this.obj2Options(this.data.options);
        this.toggleValue = this.value
    }
}

</script>
