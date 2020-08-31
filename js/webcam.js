let cameraWidth = 480;
let cameraHeight = 320;
let video;
let canvas;
let ctx;
let isCameraWorking = false;
let isCameraAvalible = false;
let viewer;

const initWebcam = () => {
  video = $("#video");
  canvas = $("canvas");
  viewer = $("#imgViewer");
  ctx = canvas.getContext("2d");
  canvas.setAttribute("width", cameraWidth);
  canvas.setAttribute("height", cameraHeight);
  if (navigator.mediaDevices.getUserMedia) {
    runWebcam();
  } else {
    video.src = "./assets/bg.jpg";
  }
};

const runWebcam = () => {
  navigator.mediaDevices
    .getUserMedia({ video: true })
    .then((stream) => {
      video.srcObject = stream;
      video.addEventListener(
        "canplay",
        function (e) {
          if (!isCameraWorking) {
            cameraHeight = video.videoHeight / (video.videoWidth / cameraWidth);

            // guess video height if heigth is not found (Firefox bug)
            if (isNaN(cameraHeight)) {
              cameraHeight = cameraWidth / (4 / 3);
            }

            video.setAttribute("width", "100%");
            video.setAttribute("height", "auto");
            canvas.setAttribute("width", cameraWidth);
            canvas.setAttribute("height", cameraHeight);
            isCameraWorking = true;
            isCameraAvalible = true;
          }
        },
        false
      );
    })
    .catch((err) => {
      // showMessage({ text: "camera is not avalible", error: false });
      replaceVideoWithImage();
    });
};

const replaceVideoWithImage = (img = null) => {
  if (!video || !viewer) return;
  if (video.parentNode === viewer) {
    viewer.removeChild(video);
  }
  // if there is already one image appended
  if (viewer.querySelector("img")) return;
  if (!img) {
    img = document.createElement("img");
    img.classList.add("base");
    img.src = "./assets/bg.jpg";
  }
  viewer.appendChild(img);
};

const replaceImageWithVideo = () => {
  const img = $(".base");
  if (!video || !viewer || !img) return;
  viewer.removeChild(img);
  viewer.appendChild(video);
};

const changeButton = () => {
  const captureButton = $("#capture");
  captureButton.value = "Take another one";
  captureButton.onclick = () => {
    clearEdit();
    captureButton.value = "Capture";
    runWebcam();
    captureButton.onclick = captureImage;
  };
};

const captureImage = () => {
  if (!isCameraAvalible) {
    showMessage({ text: "camera is not avalible" });
    return;
  } else if (!isCameraWorking) initWebcam();
  if (video && video.parentNode !== viewer) replaceImageWithVideo();
  const input = $(".input-file");
  // if user tried to upload file first and then capture
  if (input.files.length > 0) input.value = "";
  ctx.drawImage(video, 0, 0, cameraWidth, cameraHeight);
  const data = canvas.toDataURL("image/png");
  const img = document.createElement("img");
  img.classList.add("base");
  img.src = data;
  replaceVideoWithImage(img);
  stopWebcam();
  changeButton();
};

const stopWebcam = () => {
  navigator.getUserMedia(
    { audio: false, video: true },
    (stream) => {
      var tracks = stream.getTracks();
      tracks.forEach((track) => track.stop());
    },
    (error) => {
      log("getUserMedia() error", error);
    }
  );
  isCameraWorking = false;
};
