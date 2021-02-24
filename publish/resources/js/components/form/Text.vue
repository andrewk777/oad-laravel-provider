<template>
    <div>

        <input :type="fieldType"
            :id="fieldId"
            :name="fieldName"
            :value="value"
            :class="fieldClass"
            :placeholder="placeholderText"
            @blur="onBlur"
            @focus="keyUp"
            @keyup="keyUp"
            @change="keyUp"
            :disabled="disabled"
            v-mask="mask"
            ></input>

        <ul v-show="suggestions.length" class="autosuggest-list">
            <li v-for="(item, index) in suggestions" :key="index" @click="suggestionSelected(item)">{{ item }}</li>
        </ul>
    </div>
</template>

<script>


import Vue from 'vue'
import field from '@/mixins/field';
import { CONF } from '@/config';
const VueInputMask = require('vue-inputmask').default
Vue.use(VueInputMask)

export default {
    data() {
        return {
            suggestions: [],
            options: this.data.options,
            delayTimer: {},
            test: ''
        }
    },
    computed: {
        mask() {
            //https://github.com/scleriot/vue-inputmask
            var mask = '';
            switch (this.data.mask) {
                case '#':
                    mask = {
                        regex: '[0-9]*[\.]?[0-9]{0,2}',
                        autoUnmask: true
                    }
                    break;
                case "$":
                    mask = {
                        regex: '\$ [0-9]*[\.]?[0-9]{0,2}',
                        autoUnmask: true
                    }
                    break;
                case "%":
                    mask = {
                        regex: '\% [0-9]*[\.]?[0-9]{0,2}',
                        autoUnmask: true
                    }
                    break;
                case 'tel':
                    mask = {
                        mask: '(999) 999-9999',
                        autoUnmask: true
                    }
                    break;
                case 'none':
                    mask: ''
                    break;
            }

            return mask;

        }
    },
    mixins: [field],
    methods: {
        suggestionSelected(val) {
            this.suggestions = [];
            this.$emit('input', val)
        },
        onBlur() {
            var self = this;
            setTimeout(function () { //delay for suggestionSelected to work
                self.suggestions = [];
            }, 100);
        },
        emitColor(obj) {
            this.$emit('input',obj.hex8)
        },
        keyUp(ev) {
            var val = ev.target.value;
            this.$emit('input', val)
            this.$emit('keyup', this.data.name)
            if (this.data.type == 'autosuggest') {
                var self = this;
                var params = new URLSearchParams();
                params.append('search', val);

                clearTimeout(this.delayTimer);

                this.delayTimer = setTimeout(function () {
                    if (self.data.ajax) {
                        self.$http.get(CONF.API_URL + self.data.ajax, {params: params})
                                .then(function (res) {
                                    self.suggestions = res.data
                                    if (self.value != ev.target.value)
                                        self.$emit('input', ev.target.value)
                                })
                    } else {
                        self.suggestions = self.options.find(function (item) {
                            return item.search(val);
                        });
                    }
                }, 300);
            }
        }
    }
}
</script>
