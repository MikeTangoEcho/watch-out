'use strict';

var mediaRecorder;
var recordedBlobs;
var mediaStream;

var constraints = {
    audio: true,
    video: true
//    video: { width: 1280, height: 720 }
}; 

function handleDataAvailable(event) {
  console.log('Pushed data');
  if (event.data && event.data.size > 0) {
    recordedBlobs.push(event.data);
    // Push Data
    // TODO mark uploaded records
    axios.post('/stream', event.data, { headers: {
      'X-Block-Chunk-Id': recordedBlobs.length,
    }})
      .then(function (response) {
        console.log(response);
      })
      .catch(function (error) {
        console.log(error);
      });
  }
}

function startRecording() {
  recordedBlobs = [];
  var options = {
//    audioBitsPerSecond : 128000,
//    videoBitsPerSecond : 2500000,
    mimeType: 'video/webm'
  };
// Force codec ? for stream reading ?
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
  mediaRecorder.start(2000); // Blob of 1 sec
  console.log('MediaRecorder started', mediaRecorder);
}

function stopRecording() {
  mediaRecorder.stop();
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

function download() {
    var blob = new Blob(recordedBlobs, {
        type: 'video/webm'
    });
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    document.body.appendChild(a);
    a.style = 'display: none';
    a.href = url;
    a.download = 'test.webm';
    a.click();
    window.URL.revokeObjectURL(url);
}
  
// Selectors
const previewVideo = document.querySelector('video#preview');
const recordButton = document.querySelector('button#record');
const stopButton = document.querySelector('button#stop');
const ddlButton = document.querySelector('button#download');

// Event Listener
recordButton.addEventListener('click', () => {
    navigator.mediaDevices.getUserMedia(constraints)
        .then(handleSuccess)
        .catch(e => console.error('navigator.getUserMedia error:', e));    
});
stopButton.addEventListener('click', () => {
    stopRecording();
});
ddlButton.addEventListener('click', () => {
    download();
});
