'use strict';

class Recorder {

  constructor(pushDelayMs, playerVideo, viewsCounter) {
    this.userMediaConstraints = {
      audio: true,
      // TODO audio only, but container is totally different
      // Weird Bug: MediaRecorder in state recording but no data get recorded
      // Fix: switching to video:true remove the hangs
      //video: true
      video: { 
        width: { max: 320 },
        height: { max: 240 },
        frameRate: { max: 15 }
      }
    };
    // TODO Find acceptable quality, low bandwidth and low cpu!
    // 9mo ~ 1min
    this.mediaRecorderOptions = {
      audioBitsPerSecond : 64000, // Low quality ~ phone
      videoBitsPerSecond : 500000, // Low quality ~ 360p
    };
    this.playerVideo = playerVideo;
    this.viewsCounter = viewsCounter;
    // Reducing the delay of push:
    // + reduce the latency
    // + small payload, thus less memory bottleneck
    // - increase cpu load (firefox chunk with bad timecode, redo in js ?)
    // - increase HTTP client request over time
    // - several streams at the same time will struggle
    this.pushDelayMs = pushDelayMs;

    this.views = 0;
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
    if (e.data && e.data.size > 0) {
      var dataChunkOrder = this.chunkOrder;
      this.chunkOrder++;
      // Push Data
      axios.post(this.src, e.data,
        { headers: { 'X-Chunk-Order': dataChunkOrder }})
        .then(response => {
          console.log('Pushed Chunk', dataChunkOrder);
          this.views = response.headers['x-views'];
          if (this.viewsCounter) {
            this.viewsCounter.innerHTML = this.views;
          }
        })
        .catch(this.stopRecording.bind(this));
    }  
  }

  startRecording() {
    this.chunkOrder = 1;
    this.mediaRecorderOptions.mimeType = this.mimeType;
    try {
      this.mediaRecorder = new MediaRecorder(this.mediaStream, this.mediaRecorderOptions);
    } catch (e) {
      console.error('Exception while creating MediaRecorder:', e);
      return;
    }
  
    this.mediaRecorder.addEventListener('stop', this.onRecorderStop.bind(this));
    this.mediaRecorder.addEventListener('dataavailable', this.onDataAvailable.bind(this));
    //this.mediaRecorder.ondataavailable = this.onDataAvailable;
    console.log('Created MediaRecorder', this.mediaRecorder, 'with options', this.mediaRecorderOptions);
    
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
    console.log('Stream stopped', this.mediaStream);
  }

  readStream(stream) {
    console.log('getUserMedia() got stream:', stream);
  
    // Apply Constraints
    stream.getVideoTracks()[0]
      .applyConstraints(this.userMediaConstraints.video);

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
      // Using specific constraints on first Media Query may hangs
      navigator.mediaDevices.getUserMedia({ audio: true, video: true})
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