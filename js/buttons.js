const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => document.querySelectorAll(selector);

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
  const baseImage = $(".input-file").files[0];

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

/**
 * sends a request to the specified url from a form. this will change the window location.
 * @param {string} path the path to send the post request to
 * @param {object} params the paramiters to add to the url
 * @param {string} [method=post] the method to use on the form
 */

function post(path, params, method = "post") {
  const form = document.createElement("form");
  form.method = method;
  form.action = path;

  for (const key in params) {
    if (params.hasOwnProperty(key)) {
      const hiddenField = document.createElement("input");
      hiddenField.type = "hidden";
      hiddenField.name = key;
      hiddenField.value = params[key];

      form.appendChild(hiddenField);
    }
  }
  document.body.appendChild(form);
  form.submit();
}

const addSnippetClickListener = () => {
  const snippets = $$(".snippet");
  const viewer = $("#imgViewer");
  const addClickListener = (event) => {
    console.log(viewer.children);
    console.log(event);
    if (viewer) {
      if (event.target.localName === "div") {
        viewer.appendChild(event.target.children[0]);
      } else if (event.target.localName === "img") {
        viewer.appendChild(event.target);
      }
    }
  };
  if (snippets) {
    console.log(snippets);
    snippets.forEach((el) => {
      el.addEventListener("click", addClickListener);
    });
  }
};

const addUploadListener = () => {
  const input = $(".input-file");
  const viewer = $("#imgViewer .base");
  const colorGreen = "#5aac7b";
  const colorWhite = "#fff";
  const tick = "M27 4l-15 15-7-7-5 5 12 12 20-20z";
  const upload =
    "M15 22h-15v8h30v-8h-15zM28 26h-4v-2h4v2zM7 10l8-8 8 8h-5v10h-6v-10z";
  const iconPath = $(".icon path");
  if (input) {
    input.addEventListener("change", (element) => {
      const $label = $(".file_label");
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

// add email and submit form
const sendRestoreEmail = () => {
  const formRestore = $("#restorePassword");
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

/**
 * add event listeners
 */
window.onload = () => {
  sendRestoreEmail();
  addUploadListener();
  addSnippetClickListener();
};
