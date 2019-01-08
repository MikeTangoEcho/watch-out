<template>
   <span>{{ formattedValue }} {{ formattedUnit }}</span>
</template>

<script>
    export default {
        props: {
            units: {
                type: Array,
                default: function() {
                    return [];
                }
            },
            value: Number
        },
        data() {
            return {
                formattedUnit: null
            };
        },
        computed: {
            formattedValue: function () {
                // Desc order
                var orderedUnit = this.units.sort((b, a) => b.unit > a.unit);
                // Take higher unit with lowest factor
                var bestUnit = orderedUnit.find(u => u.unit <= this.value);
                if (typeof bestUnit == 'undefined') {
                    return this.value;
                }
                this.formattedUnit = bestUnit.format;
                return (this.value / bestUnit.unit).toFixed(2);
            }
        }
    }
</script>
