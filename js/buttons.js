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
  let dragged;
  let currentDroppable = null;
  let inDraggableZone = true;
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
  }
  // double click to remove snippet from image
  function onMouseDoubleClick(event) {
    if (this.draggable == false) return;
    console.log("double click");
    console.log(event);
    console.log(`pageOffset: ${pageXOffset} ${pageYOffset}`);

    // if clicked element is actually in viewer
    // then we should find it's index in snippets array
    // to place it back to the gallery of snippets
    const snippetPlaceholder = $(".snippets").children[this.index];
    const elem = viewer.removeChild(this);
    this.dx = null;
    this.dy = null;
    elem.setAttribute("draggable", false);
    snippetPlaceholder.appendChild(elem);
  }
  document.addEventListener("keydown", (e) => {
    console.log(e);
    if (e.key === "Escape" && dragged) {
      document.removeEventListener("mousemove", onMouseMoveEvent);
      dragged.style.opacity = "";
      dragged.onmouseup = null;
      dragged = null;
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
        obj.adx = viewer.offsetLeft;
        obj.ady = viewer.offsetTop;

        obj.onmousedown = function (e) {
          if (this.draggable == false) return;
          console.log(e);

          var rect = obj.getBoundingClientRect();
          obj.dx = rect.left - e.clientX;
          obj.dy = rect.top - e.clientY;
          dragged = this;

          document.onmousemove = onMouseMoveEvent;
        };

        obj.onmouseup = function (e) {
          dragged = null;
        };

        onMouseMoveEvent = function (e) {
          // check what element is under cursor
          if (!dragged) return;
          dragged.hidden = true;
          let elemBelow = document.elementFromPoint(
            event.clientX,
            event.clientY
          );
          dragged.hidden = false;

          if (!elemBelow) return;

          let droppableBelow = elemBelow.closest(".droppable");
          if (currentDroppable != droppableBelow) {
            if (currentDroppable) {
              // null when we were not over a droppable before target event
              inDraggableZone = false;
            }
            currentDroppable = droppableBelow;
            if (currentDroppable) {
              // null if we're not coming over a droppable now
              inDraggableZone = true;
            }
          }

          if (dragged && inDraggableZone) {
            dragged.style.left = e.pageX - dragged.adx + dragged.dx + "px";
            dragged.style.top = e.pageY - dragged.ady + dragged.dy + "px";
          }
        };
      }
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
