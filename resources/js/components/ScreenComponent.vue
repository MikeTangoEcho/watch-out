<template>
    <div class="container">
        <div class="row my-2">
            <button class="btn btn-success btn-lg" @click="shuffle()" type="button">
                <i class="material-icons">shuffle</i>
            </button>
        </div>
        <div class="row">
            <streamer v-for="(video, index) in videos" :video="video" :key="video.id" @ended="replace"></streamer>
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
        computed: {
            excludedVideoIds: function () {
                return this.videos.map(x => x.id);
            }
        },
        methods: {
            shuffle: function () {
                axios.get(this.videoApi, {
                    params: {
                        per_page: this.videoCount,
                        excluded_ids: this.excludedVideoIds
                    }
                })
                .then(response => {
                    // Remove video  
                    for (var key in response.data.data) {
                        this.$set(this.videos, key, response.data.data[key]);
                    }
                    // Remove others
                    while (key++ < this.videos.length) {
                        this.$delete(this.videos, key);
                    }
                    //this.videos = response.data.data;
                })
                .catch(error => {
                    console.log(error)
                });
            },
            replace: function(video) {
                // Ask as much video as async call can be made to avoid duplicate key
                // Avoid race condition and retring requests
                axios.get(this.videoApi, {
                    params: {
                        per_page: this.videoCount,
                        excluded_ids: this.excludedVideoIds
                    }
                })
                .then(response => {
                    if (response.data.data.length) {
                        // Get first video that is not already shown
                        var newVideo = response.data.data.find( e => !this.excludedVideoIds.includes(e.id));
                        if (typeof newVideo !== "undefined") {
                            //this.videos.pop(video);
                            //this.videos.push(response.data.data[0]);
                            var index = this.videos.indexOf(video);
                            this.videos.splice(index - 1, 1, newVideo);
                            console.log("Replaced", video.id, "with", newVideo.id);
                            return true;
                        }
                        // TODO If new videos are returned and total videos is lesser than videoCount
                        // Add new videos
                    }
                    // No replacement, remove video
                    this.videos.pop(video);
                })
                .catch(error => {
                    this.videos.pop(video);
                    console.log(error);
                });
            },
        }
    }
</script>
