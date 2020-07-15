const register = () => {
  window.location.href = "index.php?route=register";
};

const login = () => {
  window.location.href = "index.php?route=login";
};

const forgot = () => {
  window.location.href = "index.php?route=forgot";
};

const sendImage = async () => {
  const baseImage = document.querySelector(".input-file").files[0];

  let formData = new FormData();
  formData.append("baseImage", baseImage);

  const request = {
    method: "POST",
    headers: {
      "Content-Type": "image/jpeg",
      // "multipart/form-data;charset=utf-8; boundary=" +
      // Math.random().toString().substr(2),
    },
    body: formData,
  };
  let response = await fetch("/camagru/api/image.php", request);
  console.log(response);
};

const addUploadListener = () => {
  const input = document.querySelector(".input-file");
  const viewer = document.querySelector("#imgViewer img");
  const colorGreen = "#5aac7b";
  const colorWhite = "#fff";
  const tick = "M27 4l-15 15-7-7-5 5 12 12 20-20z";
  const upload =
    "M15 22h-15v8h30v-8h-15zM28 26h-4v-2h4v2zM7 10l8-8 8 8h-5v10h-6v-10z";
  const iconPath = document.querySelector(".icon path");
  if (input) {
    input.addEventListener("change", (element) => {
      const $label = document.querySelector(".file_label");
      const labelVal = $label.innerHTML;

      var fileName = "";
      if (element.target.value) {
        fileName = element.target.value.split("\\").pop();
      }
      if (fileName) {
        iconPath.setAttribute("d", tick);
        iconPath.setAttribute("fill", colorGreen);
        $label.querySelector(".file_name").innerHTML = fileName;
        viewer.src = URL.createObjectURL(element.target.files[0]);
        viewer.onload = () => URL.revokeObjectURL(viewer.src);
      } else {
        iconPath.setAttribute("d", upload);
        iconPath.setAttribute("fill", colorWhite);
        $label.innerHTML = labelVal;
        viewer.setAttribute("src", "");
      }
    });
  }
};

const sendRestoreEmail = () => {
  const formRestore = document.querySelector("#restorePassword");
  if (formRestore) {
    formRestore.addEventListener("submit", (e) => {
      const urlParams = new URLSearchParams(window.location.search);
      const email = document.createElement("input");
      email.type = "hidden";
      email.name = "email";
      email.value = urlParams.get("email");
      formRestore.appendChild(email);
    });
  }
};

// add email and submit form
window.onload = () => {
  sendRestoreEmail();
  addUploadListener();
};
