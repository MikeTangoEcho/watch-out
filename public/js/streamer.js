'use strict';

// https://w3c.github.io/media-source/#widl-MediaSource-addSourceBuffer-SourceBuffer-DOMString-type
const playerVideo = document.querySelector('video#player');
var videoUrl = playerVideo.getAttribute("data-src");
var streamedBlobs = [];
var currentChunk = 1;
var mediaSource;
var sourceBuffer;

if (window.MediaSource) {
  mediaSource = new MediaSource();
  playerVideo.src = URL.createObjectURL(mediaSource);
  mediaSource.addEventListener('sourceopen', sourceOpen);
  mediaSource.addEventListener("sourceclose", function (e) {
    console.log("FUCK close");
    console.log(e);
  });
  mediaSource.addEventListener("sourceended", function (e) {
    console.log("FUCK end");
    console.log(e);
  });
} else {
  console.log("The Media Source Extensions API is not supported.")
}

function readStream(e) {
  //var sourceBuffer = e.target;
  sourceBuffer.removeEventListener('updateend', readStream);
  if (mediaSource.readyState !== "open") {
    // Check error status
    return;
  }
  // Stream
  // Get next Block
  // TODO retry with 404
  axios.get(videoUrl, {
    responseType: 'arraybuffer',
    headers: { 'X-Block-Chunk-Id': currentChunk }
  })
    .then(function (response) {
      console.log('Pulled Chunk ' + response.headers['x-block-chunk-id']);
      currentChunk = Number(response.headers['x-block-chunk-id']) + 1;
      streamedBlobs.push(response.data);
      sourceBuffer.appendBuffer(response.data);
      sourceBuffer.addEventListener('updateend', readStream);
      if (playerVideo.paused) {
        //console.log('Play');
        //playerVideo.play();
      }
    })
    .catch(function (error) {
      // TODO wait if 404
      console.log(error);
      //closeStream();
    });
}

function sourceOpen(e) {
  var mime = 'video/webm; codecs="vorbis,vp8"';
  mediaSource = e.target;
  sourceBuffer = mediaSource.addSourceBuffer(mime);
  sourceBuffer.mode = "sequence";
  sourceBuffer.addEventListener('update', function(e) {
    console.log("Update");
    console.log(e);
  });

  sourceBuffer.addEventListener('error', function(e) {
    console.log("Error");
    console.log(e);
  });
  sourceBuffer.addEventListener('abort', function(e) {
    console.log("Abort"); 
    console.log(e); 
  });
  readStream();
}

function closeStream() {
  if (mediaSource.readyState === "open") {
    mediaSource.endOfStream("network");
  }  
  URL.revokeObjectURL(playerVideo.src);
}
