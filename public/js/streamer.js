'use strict';

// https://w3c.github.io/media-source/#widl-MediaSource-addSourceBuffer-SourceBuffer-DOMString-type
const playerVideo = document.querySelector('video#player');
var videoUrl = playerVideo.getAttribute("data-src");
var streamedBlobs = [];
var nextChunk = 0;
var sequenceChunk = 0;
var waitCounter = 0;
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
    headers: { 'X-Block-Chunk-Id': nextChunk, 'X-Sequence-Chunk-Id': sequenceChunk }
  })
    .then(function (response) {
      if (response.status == "200") {
        waitCounter = 0;
        // TODO Nothing if stuck on block 0
        console.log('Pulled Chunk ' + response.headers['x-block-chunk-id']);
        nextChunk = Number(response.headers['x-block-next-chunk-id']);
        streamedBlobs.push(response.data);
        sourceBuffer.appendBuffer(response.data);
        console.log('Chunk Sequence ' + sequenceChunk);
        sequenceChunk += 1;
        sourceBuffer.addEventListener('updateend', readStream);
        // TODO Check AUTOplay
      } else if (response.status == "204") {
        if (waitCounter > 20) {
          closeStream();
        }
        console.log('Wait for next Chunk ' + nextChunk);
        // Need to wait until we get more chunk
        waitCounter++;
        setTimeout(readStream, response.headers['retry-after'] * 1000);
      }
    })
    .catch(function (error) {
      console.log(error);
      closeStream();
    });
}

function sourceOpen(e) {
  var mime = 'video/webm;codecs="opus,vp9"';
//  var mime = 'video/mp4';
  mediaSource = e.target;
  sourceBuffer = mediaSource.addSourceBuffer(mime);
  sourceBuffer.mode = "sequence";
  sourceBuffer.addEventListener('update', function(e) {
    console.log("Buffer updated");
    console.log(e);
  });

  sourceBuffer.addEventListener('error', function(e) {
    console.log("Buffer error");
    // chrome://media-internals/
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
  // waitCounter = 0;
  // nextChunk = 0;
  // sequenceChunk = 0;
  // playerVideo.src = URL.createObjectURL(mediaSource);
}
