<template>
    <div class="container">
        <div class="row">
            <button @click="shuffle()" type="button">shuffle</button>
        </div>
        <div class="row">
            <streamer v-for="video in videos" :video="video" :key="video.id"></streamer>
        </div>
    </div>
</template>

<script>
    export default {
        mounted() {
            this.shuffle();
        },
        props: {
            videoApi: String,
            videoCount: String
        },
        data() {
            return {
                videos: []
            };
        },
        methods: {
            shuffle: function () {
                axios.get(this.videoApi, {
                    'limit': this.videoCount
                })
                .then(response => {
                    console.log(response);
                    this.videos = response.data.data;
                })
                .catch(error => {
                    console.log(error)
                });
            }
        }
    }
</script>
