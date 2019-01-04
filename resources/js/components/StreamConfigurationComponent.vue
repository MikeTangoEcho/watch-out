<template>
    
</template>

<script>
    export default {
        mounted() {
            this.title = this.defaultTitle;
            this.newTitle = this.defaultTitle;
        },
        props: {
            url: String,
            defaultTitle: String
        },
        data() {
            return {
                title: null,
                newTitle: null,
                validation: {},
                editing: false
            }
        },
        methods: {
            updateStream: function(e) {
                axios.put(this.url, {
                    'title': this.newTitle
                })
                .then(response => {
                    this.validation = {};
                    this.editing = false;
                    this.title = this.newTitle;
                })
                .catch(error => {
                    // Check if 422
                    this.validation = error.response.data;
                });
            },
            isInvalid: function (field) {
                return this.validation.errors
                    && this.validation.errors[field];
            },
            getErrors: function (field) {
                if (this.isInvalid(field)) {
                    return this.validation.errors[field];
                }
                return [];
            },
            cancelEdit: function (e) {
                this.editing = false;
                this.newTitle = this.title;
            }
        }
    }
</script>
