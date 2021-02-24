<template>
    <VueCtkDateTimePicker
        :no-value-to-custom-elem="true"
        ref="dateTimePicker"
        :id="data.id"
        :value="value"
        @input="onInput"
        :range="data.type == 'daterange'"
        :auto-close="true"
        color="#564AA3"
        :no-label="true"
        :min-date="data.min"
        format="YYYY-MM-DD"
        formatted="D MMM, YYYY"
        :only-date="true"
        minuteInterval="15"
        class="noButtons"
        :first-day-of-week="firstDay"
        :noClearButton="noClearButton"
        :no-button-now="true"
        />
</template>

<script>
    import VueCtkDateTimePicker from 'vue-ctk-date-time-picker';

    import 'vue-ctk-date-time-picker/dist/vue-ctk-date-time-picker.css';
    import field from '@/mixins/field';
    import moment from 'moment'

    export default {
        components: {
            VueCtkDateTimePicker
        },
        mixins: [field],
        data() {
            return {
                compValue: this.value,
                format: "YYYY-MM-DD",
                firstDay: 1
            }
        },
        computed: {
            noClearButton() {
                return this.data.required.length > 0;
            }
        },
        methods: {
            onInput(val) {
                this.$emit('input', val)
            }
        },
    }
</script>
