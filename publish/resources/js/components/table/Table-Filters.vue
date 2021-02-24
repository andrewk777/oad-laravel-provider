<template>
<b-row>
    <b-col>
        <div class="table-filters">
            <form ref="filters">
                <slot></slot>
                <div class="mt-2 text-right">
                    <!--<b-button type="submit" variant="outline-success">Submit</b-button>-->
                    <b-button variant="outline-info" @click="$emit('clear-filters')">Clear</b-button>
                </div>
            </form>
        </div>
    </b-col>
</b-row>
</template>