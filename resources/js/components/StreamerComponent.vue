<template>
    <div class="stream">
        <span class="stream-title scroll-container">
            <p class="scroll-left">{{ video.user.name }}. {{ video.title }}</p>
        </span>
        <i class="stream-shuffle material-icons" @click="close()">shuffle</i>
        <a class="stream-url material-icons" target="_blank" :href="video.url">launch</a>
        <i ref="mute" @click="toggleSound()" class="stream-mute material-icons">volume_off</i>
        <video
            ref="video"
            :poster="video.poster"
            class="streamer"
            muted
            autoplay
            :data-src="video.src"
            :data-mime-type="video.mime_type"
            @ended="closed()"
            >
        </video>
    </div>
</template>

<script>
    export default {
        mounted: function() {
            this.open();
            // Listen to ClosedStream
            // Close itself
        },
        beforeDestroy: function() {
            this.stream = null;
        },
        props: {
            video: {
                type: Object,
                default: function() {
                    return {
                        id: null,
                        title: null,
                        url: null,
                        src: null,
                        mime_type: null,
                        user: {
                            id: null,
                            name: null
                        }
                    };
                }
            }
        },
        data() {
           return {
               streamer: null
           };
        },
        methods: {
            open: function () {
                this.streamer = new Streamer(this.$refs.video);
                this.streamer.openStream();
            },
            toggleSound: function () {
                this.$refs.video.muted = !this.$refs.video.muted;

                if (this.$refs.video.muted) {
                    this.$refs.mute.innerHTML = "volume_off";
                    console.log("Muted", this.streamer.src);
                } else {
                    this.$refs.mute.innerHTML = "volume_up";
                    console.log("Unmuted", this.streamer.src);
                }
            },
            closed: function (e) {
                this.$emit('ended', this.video);
            },
            close: function() {
                this.streamer.closeStream("User Action");
            }
        }
    }
</script>
