'use strict';

class Recorder {

  constructor(playerVideo) {
    this.userMediaConstraints = {
      audio: true,
      video: { 
        width: { min: 640, ideal: 640 },
        height: { min: 480, ideal: 480 }
      }
    };
// TODO Find somewhat good quality
    this.mediaRecorderOptions = {
//      audioBitsPerSecond : 128000,
//      videoBitsPerSecond : 2500000,
    };
    this.playerVideo = playerVideo;
    this.pushDelayMs = 2000;
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

  static getSupportedMimeType() {
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

  onRecorderStop(e) {
    console.log('Recorder stopped: ', e);
  }

  onDataAvailable(e) {
    if (event.data && event.data.size > 0) {
      var dataChunkOrder = this.chunkOrder;
      console.log('Pushed Blob', dataChunkOrder);
      this.chunkOrder++;
      // Push Data
      axios.post(this.src, event.data,
        { headers: { 'X-Chunk-Order': dataChunkOrder }})
        .then(response => console.log(response))
        .catch(this.stopRecording.bind(this));
    }  
  }

  startRecording() {
    this.chunkOrder = 1;
    this.mediaRecorderOptions.mimeType = this.mimeType;
    try {
      this.mediaRecorder = new MediaRecorder(this.mediaStream, this.mediaRecorderOptions);
      console.log('Created MediaRecorder', this.mediaRecorder, 'with options', this.mediaRecorderOptions);
    } catch (e) {
      console.error('Exception while creating MediaRecorder:', e);
      return;
    }
  
    this.mediaRecorder.addEventListener('stop', this.onRecorderStop.bind(this));
    this.mediaRecorder.addEventListener('dataavailable', this.onDataAvailable.bind(this));

    // Chrome give us avg 1 Cluster every 12s
    // Firefox make one Cluster per trigger
    this.mediaRecorder.start(this.pushDelayMs);
    console.log('MediaRecorder started', this.mediaRecorder);
  }
  
  stopRecording(e) {
    console.log('Stop Recording', e);
    if (this.mediaRecorder && this.mediaRecorder.state !== "inactive") {
      this.mediaRecorder.stop();
    }
    this.mediaStream.stop();
    console.log('Recorded Blobs: ', this.queuedBlobs);
  }

  readStream(stream) {
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
  
    this.mediaStream = stream;
    this.playerVideo.srcObject = stream;
    this.startRecording();
  }

  openStream() {
    if (navigator.mediaDevices) {
      navigator.mediaDevices.getUserMedia(this.userMediaConstraints)
        .then(this.readStream.bind(this))
        .catch(e => console.error('navigator.getUserMedia error:', e));    
    } else {
      console.log("The Media Source Extensions API is not supported.")
    }
  }

  closeStream() {
    this.mediaStream.stop();
    console.log("Media Stream Closed");
  }
}