'use strict';

// https://w3c.github.io/media-source/#widl-MediaSource-addSourceBuffer-SourceBuffer-DOMString-type
class Streamer {
  constructor(playerVideo) {
    this.playerVideo = playerVideo;
    this.nextChunk = 0;
    this.waitCounter = 0;
    this.maxWaitCounter = 10;
    this.queuedBlobs = [];
  }

  get src() {
    if (this.playerVideo) {
      return this.playerVideo.getAttribute("data-src");
    }
  } 

  get mimeType() {
    if (this.playerVideo) {
      return this.playerVideo.getAttribute("data-mime-type");
    }
  }

  onSourceBufferUpdate(e) {
    console.log("Buffer Update", e);
  }
  onSourceBufferError(e) {
    console.log("Buffer Error", e);
  }
  onSourceBufferAbort(e) {
    console.log("Buffer Abort", e);
  }

  onMediaSourceOpen(e) {
    this.sourceBuffer = this.mediaSource.addSourceBuffer(this.mimeType);
    // Chunk are ordered on server side
    this.sourceBuffer.mode = "sequence";
    // Events
    this.sourceBuffer.addEventListener('update', this.onSourceBufferUpdate.bind(this));
    this.sourceBuffer.addEventListener('error', this.onSourceBufferError.bind(this));
    this.sourceBuffer.addEventListener('abort', this.onSourceBufferAbort.bind(this));

    this.readStream();
  }

  onMediaSourceClose(e) {
    console.log("Source Close", e);

  }

  onMediaSourceEnded(e) {
    console.log("Source Ended", e);
  }

  openStream() {
    if (window.MediaSource) {
      console.log("Open Stream", this.src, this.mimeType);
      this.mediaSource = new MediaSource();
      this.playerVideo.src = URL.createObjectURL(this.mediaSource);
      this.mediaSource.addEventListener('sourceopen', this.onMediaSourceOpen.bind(this));
      this.mediaSource.addEventListener("sourceclose", this.onMediaSourceClose.bind(this));
      this.mediaSource.addEventListener("sourceended", this.onMediaSourceEnded.bind(this));
    } else {
      console.log("The Media Source Extensions API is not supported.")
    }    
  }
  
  closeStream(e) {
    console.log("Close Stream Reason", e);
    if (this.mediaSource.readyState === "open") {
      this.mediaSource.endOfStream("network");
    }
    URL.revokeObjectURL(this.playerVideo.src);
    console.log("Closed Stream", this.src);
  }

  appendBuffer(response) {
    if (response.status == "200") {
      this.waitCounter = 0;
      // TODO Nothing if stuck on block 0
      console.log("Pulled Chunk", + response.headers['x-chunk-order']);
      this.nextChunk = Number(response.headers['x-next-chunk-order']);
      this.queuedBlobs.push(response.data);
      // sourceBuffer.updating = true before append
      this.sourceBuffer.appendBuffer(response.data);
      this.sourceBuffer.addEventListener('updateend', this.readStream.bind(this), { 'once': true, 'passive': true });
    } else if (response.status == "204") {
      if (this.waitCounter > this.maxWaitCounter) {
        // No Content for x times, close stream or reload stream or loop ?
        this.closeStream("Unavailable Chunk " + this.nextChunk);
      } else {
        console.log("Wait for next Chunk", this.nextChunk);
        // Need to wait until we get more chunk
        this.waitCounter++;
        setTimeout(this.readStream.bind(this), response.headers['retry-after'] * 1000);
      }
    }
  }

  readStream() {

    if (this.mediaSource.readyState !== "open") {
      // Check error status
      return;
    }

    // Get chunks
    axios.get(this.src, {
      responseType: 'arraybuffer',
      headers: { 'X-Chunk-Order': this.nextChunk }
    }).then(this.appendBuffer.bind(this))
      .catch(this.closeStream.bind(this));
  }
}
