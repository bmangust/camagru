const register = () => {
  window.location.href = "index.php?route=register";
};

const login = () => {
  window.location.href = "index.php?route=login";
};

const forgot = () => {
  window.location.href = "index.php?route=forgot";
};

const confirmDelete = () => {
  const answer = confirm(
    "Are you sure? All your pictures will be removed from gallery as well as your account. This action cannot be undone."
  );
  if (answer) {
    const url = "api/users.php";
    const params = { action: "Delete account" };
    post(url, params);
  }
};

const updateNotifications = async (el) => {
  const body = { action: "Change notifications", value: el.checked };
  const params = {
    method: "POST",
    headers: {
      "Content-Type": "application/json;charset=utf-8",
    },
    body: JSON.stringify(body),
  };
  const response = await fetch("api/users.php", params);
  const txt = await response.text();
  let json;
  try {
    json = JSON.parse(txt);
    json.success
      ? showMessage({ text: json.data, error: false })
      : showMessage({ text: json.data });
  } catch (e) {
    showMessage({
      text: "Your settings were not updated due to database error. " + txt,
    });
  }
};

const selectNotifications = async () => {
  const response = await fetch("api/users.php?action=selectNotifications");
  const txt = await response.text();
  let json;
  try {
    json = JSON.parse(txt);
    if (!json.success) return;
    $("#notificationsSwitcher").checked = json.value === "1" ? true : false;
  } catch (e) {
    showMessage({
      text: "Your settings were not updated due to database error. " + txt,
    });
  }
};

const addSnippetClickListener = () => {
  const snippets = $$(".snippet");
  const viewer = $("#imgViewer");
  let dragged;
  let currentDroppable = null;
  let onMouseMoveEvent;

  function findClickedSnippet(collection, element) {
    let foundElementIndex;
    let index = 0;
    collection.forEach((el) => {
      if (el == element) {
        foundElementIndex = index;
      }
      index++;
    });
    return foundElementIndex;
  }

  function onClickListener(event) {
    // find if clicked element is viewer's child
    const isInViewer = Array.prototype.slice
      .call(viewer.children)
      .find((el) => el === this);
    if (!isInViewer) {
      // if snippet is in his initail place (in gallery)
      // then find it and place to the viewer
      event.target.setAttribute("draggable", true);
      viewer.appendChild(event.target);
    }
    activeSnippet = this;
  }
  // double click to remove snippet from image
  function onMouseDoubleClick(event) {
    if (this.draggable == false) return;
    // if clicked element is actually in viewer
    // then we should find it's index in snippets array
    // to place it back to the gallery of snippets
    const snippetPlaceholder = $(".snippets").children[this.index];
    const elem = viewer.removeChild(this);
    this.dx = null;
    this.dy = null;
    elem.setAttribute("draggable", false);
    snippetPlaceholder.appendChild(elem);
    activeSnippet = null;
  }
  document.addEventListener("keydown", (e) => {
    if (e.key === "Escape" && dragged) {
      document.removeEventListener("mousemove", onMouseMoveEvent);
      dragged.onmouseup = null;
      dragged = null;
      activeSnippet = null;
    }
  });

  if (snippets) {
    snippets.forEach((el) => {
      el.addEventListener("click", onClickListener);
      el.addEventListener("dblclick", onMouseDoubleClick);
      el.ondragstart = function () {
        return false;
      };
      el.index = findClickedSnippet(Array.prototype.slice.call(snippets), el);
      set_drag_drop(el);

      function set_drag_drop(obj) {
        obj.onmousedown = function (e) {
          if (this.draggable == false) return;
          const parent = viewer.getBoundingClientRect();
          obj.adx = parent.left;
          obj.ady = parent.top;

          var rect = obj.getBoundingClientRect();
          obj.dx = e.clientX - rect.left;
          obj.dy = e.clientY - rect.top;
          dragged = this;
          activeSnippet = this;

          document.onmousemove = onMouseMoveEvent;
        };

        obj.onmouseup = function (e) {
          dragged = null;
        };

        onMouseMoveEvent = function (e) {
          // check what element is under cursor

          if (!dragged) return;
          dragged.hidden = true;
          let elemBelow = document.elementFromPoint(e.clientX, e.clientY);
          dragged.hidden = false;

          if (!elemBelow) return false;

          let droppableBelow = elemBelow.closest(".droppable");
          if (currentDroppable != droppableBelow) {
            if (currentDroppable) {
              // null when we were not over a droppable before target event
              inDroppableZone = false;
            }
            currentDroppable = droppableBelow;
            if (currentDroppable) {
              // null if we're not coming over a droppable now
              inDroppableZone = true;
            }
          }

          if (dragged && inDroppableZone) {
            const left = e.clientX - dragged.adx - dragged.dx;
            const top = e.clientY - dragged.ady - dragged.dy;
            const parent = viewer.getBoundingClientRect();

            dragged.style.left = (left * 100) / parent.width + "%";
            dragged.style.top = (top * 100) / parent.height + "%";
          }
        };
      }
    });
  }
};

const resetSnippets = () => {
  const viewer = $("#imgViewer");
  const snippets = $$`#imgViewer .snippet`;
  snippets.forEach((el) => {
    const snippetPlaceholder = $(".snippets").children[el.index];
    const elem = viewer.removeChild(el);
    el.dx = null;
    el.dy = null;
    elem.setAttribute("draggable", false);
    snippetPlaceholder.appendChild(elem);
  });
};

const clearViewer = () => {
  if (isCameraAvalible) {
    if (video.parentNode === viewer) return;
    // show camera here
    const captureButton = $("#capture");
    replaceImageWithVideo();
    captureButton.value = "Capture";
    runWebcam();
    captureButton.onclick = captureImage;
  }
  const img = $("#imgViewer .base") || $("#video");
  const $label = $(".file_label");
  const iconPath = $(".icon path");
  const colorWhite = "#fff";
  const upload =
    "M15 22h-15v8h30v-8h-15zM28 26h-4v-2h4v2zM7 10l8-8 8 8h-5v10h-6v-10z";
  iconPath.setAttribute("d", upload);
  iconPath.setAttribute("fill", colorWhite);
  $label.querySelector(".file_name").innerHTML = "Upload file";
  img.setAttribute("src", "./assets/bg.jpg");
};

const clearEdit = () => {
  clearViewer();
  resetSnippets();
};

const addUploadListener = () => {
  const input = $(".input-file");
  input.addEventListener("change", (event) => {
    const $label = $(".file_label");
    const video = $("#video");
    if (video) {
      replaceVideoWithImage();
    }
    const colorGreen = "#5aac7b";
    const tick = "M27 4l-15 15-7-7-5 5 12 12 20-20z";
    const iconPath = $(".icon path");
    const img = $("#imgViewer .base");
    var fileName = "";
    if (event.target.value) {
      fileName = event.target.value.split("\\").pop();
    }
    if (fileName) {
      iconPath.setAttribute("d", tick);
      iconPath.setAttribute("fill", colorGreen);
      $label.querySelector(".file_name").innerHTML = fileName;
      img.src = URL.createObjectURL(event.target.files[0]);
      img.onload = () => URL.revokeObjectURL(img.src);
    } else {
      clearViewer();
    }
  });
};

const addChangeAvatarListener = () => {
  const input = $("#file");
  input.addEventListener("change", (e) => {
    if (e.target.files.length < 1) return;
    if (e.target.files[0].size > 1000000)
      return showMessage({ text: "Your file is too large", error: false });
    const file = URL.createObjectURL(e.target.files[0]);
    const img = $("#avatar");
    img.src = file;
    const image = {
      name: e.target.files[0].name,
      type: e.target.files[0].type.split("/")[1],
      src: null,
    };
    img.onload = async () => {
      URL.revokeObjectURL(e.target.files[0]);
      image.src = imageToBase64(img);
      const params = {
        method: "POST",
        headers: {
          "Content-Type": "application/json;charset=utf-8",
        },
        body: JSON.stringify(image),
      };
      const response = await fetch("api/image.php/avatar", params);
      const txt = await response.text();
      try {
        result = JSON.parse(txt);
        showMessage({ text: result.message, error: !result.success });
      } catch (e) {
        log(txt);
      }
    };
  });
};

const toggleGallery = () => {
  const gallery = $(".gallery");
  if (gallery.parentNode.classList.contains("noDisplay")) {
    gallery.parentNode.classList.remove("noDisplay");
    setTimeout(() => (gallery.parentNode.style.right = "0px"), 100);
    if (gallery.children.length === 0) loadImages();
  } else {
    gallery.parentNode.style.right = "-500px";
    setTimeout(() => gallery.parentNode.classList.add("noDisplay"), 100);
  }
};

const initControls = () => {
  // const rotSlider = $("#rotation");
  // const rotValue = $("#rotationValue");
  // const scaleSlider = $("#scale");
  // const scaleValue = $("#scaleValue");
  const opacitySlider = $("#opacity");
  const opacityValue = $("#opacityValue");

  // let transform = { rotateZ: 0, scale: 1 };

  // function setTransform({
  //   rotateZ = transform.rotateZ,
  //   scale = transform.scale,
  // }) {
  //   transform = { rotateZ: rotateZ, scale: scale };
  //   trString = `rotateZ(${transform.rotateZ}deg) scale(${transform.scale})`;
  //   activeSnippet.style.transform = trString;
  //   activeSnippet.style.webkitTransform = trString;
  // }

  // rotSlider.addEventListener("input", (e) => {
  //   const value = e.target.value;
  //   rotValue.innerHTML = value;

  //   setTransform({ rotateZ: value });
  // });

  // scaleSlider.addEventListener("input", (e) => {
  //   const value = e.target.value / 100;
  //   scaleValue.innerHTML = value;

  //   setTransform({ scale: value });
  // });

  opacitySlider.addEventListener("input", (e) => {
    const value = e.target.value / 100;
    opacityValue.innerHTML = value;

    if (!activeSnippet) return;
    activeSnippet.style.opacity = value;
  });
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

const editProfile = (e) => {
  const username = $(".settings__username");
  const info = $(".settings__profile-info");
  const template = `<div class="settings__info-controls">
    <button id="cancel" class="button settings__edit-button">Cancel</button>
    <button id="save" class="button settings__edit-button">Save</button>
  </div>`;
  const editButton = e.target;
  const controls = htmlToElement(template);
  editButton.parentNode.insertBefore(controls, editButton);
  editButton.classList.add("noDisplay");
  const initialUserInfo = {
    username: username.textContent,
    info: info.textContent,
  };

  const cancelInfo = () => {
    [username, info].forEach((el) => {
      el.innerHTML =
        el === username ? initialUserInfo.username : initialUserInfo.info;
      el.classList.remove("edit");
      el.removeAttribute("contenteditable");
    });

    controls.remove();
    editButton.classList.remove("noDisplay");
  };

  const saveInfo = async () => {
    [username, info].forEach((el) => {
      el.classList.remove("edit");
      el.removeAttribute("contenteditable");
    });
    controls.remove();
    editButton.classList.remove("noDisplay");
    const body = {
      action: "Update info",
      data: {
        newUsername:
          initialUserInfo.username === username.textContent
            ? ""
            : username.textContent,
        newInfo:
          info.textContent.trim().length === 0 ? "No info" : info.textContent,
      },
    };
    if (username.textContent.search(/[^a-zA-Z0-9_]/) !== -1) {
      showMessage({
        text: "Wrong username, only letters, numbers and underscore allowed",
      });
      return;
    }
    const params = {
      method: "POST",
      headers: {
        "Content-Type": "application/json;charset=utf-8",
      },
      body: JSON.stringify(body),
    };
    let response = await fetch("api/users.php", params);
    const txt = await response.text();
    try {
      const result = JSON.parse(txt);
      log(result);
      showMessage({ text: result.message, error: !result.success });
      if (!result.success) {
        username.textContent = initialUserInfo.username;
        info.textContent = initialUserInfo.info;
      } else {
        const h1 = $("h1");
        h1.textContent = h1.textContent.replace(
          initialUserInfo.username,
          username.textContent
        );
      }
    } catch (e) {
      log(txt);
      return;
    }
  };

  [username, info].forEach((el) => {
    el.setAttribute("contenteditable", true);
    el.classList.add("edit");
    el === username
      ? (initialUserInfo.username = username.innerHTML)
      : (initialUserInfo.info = info.innerHTML);
  });
  controls
    .querySelector("#cancel")
    .addEventListener("click", () => cancelInfo());
  controls.querySelector("#save").addEventListener("click", () => saveInfo());
};
