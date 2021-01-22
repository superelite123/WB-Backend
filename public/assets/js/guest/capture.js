let video = null
let canvasCapture = null
let imgPhoto = $('#photo')
let btnCapture = $('#btnCapture')
let btnStart = $('#btnStart')
let width = 400;    
let height = 0;
let streaming = false;
let captureData = null
function startup() {
  video = document.getElementById('video');
  canvasCapture = document.getElementById('canvas');

  navigator.mediaDevices.getUserMedia({video: true, audio: false})
  .then(function(stream) {
    video.srcObject = stream;
    video.play();
  })
  .catch(function(err) {
    console.log("An error occurred: " + err);
  })

  video.addEventListener('canplay', function(ev){
    if (!streaming) {
      height = video.videoHeight / (video.videoWidth/width);
    
      // Firefox currently has a bug where the height can't be read from
      // the video, so we will make assumptions if this happens.
    
      if (isNaN(height)) {
        height = width / (4/3);
      }
    
      video.setAttribute('width', width);
      video.setAttribute('height', height);
      canvasCapture.setAttribute('width', width);
      canvasCapture.setAttribute('height', height);
      streaming = true;
    }
  }, false)

  btnCapture.click((e) => {
    takepicture();
    e.preventDefault();
  })
  
  clearphoto();
}

// Fill the photo with an indication that none has been
// captured.

function clearphoto() {
  var context = canvasCapture.getContext('2d');
  context.fillStyle = "#AAA";
  context.fillRect(0, 0, canvasCapture.width, canvasCapture.height);

  var data = canvasCapture.toDataURL('image/png');
  captureData = null
  imgPhoto.attr('src',data)
}

function takepicture() {
  let context = canvasCapture.getContext('2d');
  if (width && height) {
    canvasCapture.width = width;
    canvasCapture.height = height;
    context.drawImage(video, 0, 0, width, height);
  
    let data = canvasCapture.toDataURL('image/png');
    captureData = data;
    imgPhoto.attr('src',captureData)
  } else {
    clearphoto();
  }
}

// Set up our event listener to run the startup process
// once loading is complete.
btnStart.click(() => {
  startup()
})

