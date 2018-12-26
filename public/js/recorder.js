'use strict';


function getSupportedMimeType() {
  // Ordered by priority
  var types = [
    'video/webm;codecs="opus,vp9"',
    'video/webm;codecs="opus,vp8"',
    'video/webm;codecs="opus,h264"',
    'video/mpeg'
  ];
  for (var i in types) { 
    if (MediaRecorder.isTypeSupported(types[i])) {
      console.log("MimeType " + types[i] + " is supported");
      return types[i];
    }
    console.log("MimeType " + types[i] + " is NOT supported");
  }
  return false;
}

var mediaRecorder;
var recordedBlobs;
var mediaStream;
var mimeType;

var constraints = {
    audio: true,
//    video: true
    video: { 
      width: { min: 640, ideal: 640 },
      height: { min: 480, ideal: 480 }
    }
};

// Selectors
const previewVideo = document.querySelector('video#preview');
const videoUrl = previewVideo.getAttribute("data-src");
var videoMimeType = previewVideo.getAttribute("data-mime-type");
const recordButton = document.querySelector('button#record');
const stopButton = document.querySelector('button#stop');

function handleDataAvailable(event) {
  console.log('Pushed Blob: ' + event.timecode);
  if (event.data && event.data.size > 0) {
    recordedBlobs.push(event.data);
    // Push Data
    // TODO mark uploaded records
    axios.post(videoUrl, event.data, { headers: {
      'X-Chunk-Order': recordedBlobs.length,
    }})
      .then(function (response) {
        console.log(response);
      })
      .catch(function (error) {
        // TODO Retry
        console.log(error);
        stopRecording();
      });
  }
}

function startRecording() {
  recordedBlobs = [];
  var options = {
//    audioBitsPerSecond : 128000,
//    videoBitsPerSecond : 2500000,
    mimeType: videoMimeType,
  };

  try {
    mediaRecorder = new MediaRecorder(mediaStream, options);
  } catch (e) {
    console.error('Exception while creating MediaRecorder:', e);
    return;
  }

  console.log('Created MediaRecorder', mediaRecorder, 'with options', options);

  mediaRecorder.onstop = (event) => {
    console.log('Recorder stopped: ', event);
  };
  mediaRecorder.ondataavailable = handleDataAvailable;
  // Chrome give us avg 1 Cluster every 12s
  // Firefox make one Cluster per trigger
  mediaRecorder.start(2000);
  console.log('MediaRecorder started', mediaRecorder);
}

function stopRecording() {
  if (mediaRecorder && mediaRecorder.state !== "inactive") {
    mediaRecorder.stop();
  }
  mediaStream.stop();
  console.log('Recorded Blobs: ', recordedBlobs);
}

function handleSuccess(stream) {
  console.log('getUserMedia() got stream:', stream);

  // re-add the stop function
  // Chrome deprecated stop function
  if(!stream.stop && stream.getTracks) {
    stream.stop = function(){         
      this.getTracks().forEach(function (track) {
         track.stop();
      });
    };
  }

  mediaStream = stream;
  previewVideo.srcObject = stream;
  startRecording();
}

// Event Listener
recordButton.addEventListener('click', () => {
  navigator.mediaDevices.getUserMedia(constraints)
      .then(handleSuccess)
      .catch(e => console.error('navigator.getUserMedia error:', e));    
});
stopButton.addEventListener('click', () => {
    stopRecording();
});
