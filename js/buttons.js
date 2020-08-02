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

const sendImages = () => {
  const form = $`.controls_form`;
  const snippets = $$`#imgViewer .snippet`;
  const img = $`#imgViewer .base`;
  snippets.forEach((el) => {
    const snippetData = {
      path: el.src.substring(el.src.indexOf("assets")),
      left: el.style.left ? parseFloat(el.style.left) : 0,
      top: el.style.top ? parseFloat(el.style.top) : 0,
      offsetLeft: el.offsetLeft,
      offsetTop: el.offsetTop,
      width: el.offsetWidth,
      height: el.offsetHeight,
      drawerWidth: img.offsetWidth,
      drawerHeight: img.offsetHeight,
    };
    const snippet = document.createElement("input");
    snippet.type = "hidden";
    snippet.name = "snippet[]";
    snippet.value = JSON.stringify(snippetData);
    form.appendChild(snippet);
  });
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
  // let inDroppableZone = true;
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
        obj.onmousedown = function (e) {
          const parent = viewer.getBoundingClientRect();
          obj.adx = parent.left;
          obj.ady = parent.top;
          if (this.draggable == false) return;
          // console.log(e);

          var rect = obj.getBoundingClientRect();
          obj.dx = e.clientX - rect.left;
          obj.dy = e.clientY - rect.top;
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

const addUploadListener = () => {
  const input = $(".input-file");
  const viewer = $("#imgViewer .base");
  const colorGreen = "#5aac7b";
  const tick = "M27 4l-15 15-7-7-5 5 12 12 20-20z";
  const iconPath = $(".icon path");
  if (input) {
    input.addEventListener("change", (element) => {
      const $label = $(".file_label");
      var fileName = "";
      if (element.target.value) {
        input.lastFile = element.target.files[0];
        fileName = element.target.value.split("\\").pop();
      }
      if (fileName) {
        iconPath.setAttribute("d", tick);
        iconPath.setAttribute("fill", colorGreen);
        $label.querySelector(".file_name").innerHTML = fileName;
        viewer.src = URL.createObjectURL(element.target.files[0]);
        viewer.onload = () => URL.revokeObjectURL(viewer.src);
      } else {
        viewer.src = URL.createObjectURL(input.lastFile);
        viewer.onload = () => URL.revokeObjectURL(viewer.src);
      }
    });
  }
};

const clearEdit = () => {
  const viewer = $("#imgViewer .base");
  const $label = $(".file_label");
  const input = $("#file");
  const iconPath = $(".icon path");
  const colorWhite = "#fff";
  const upload =
    "M15 22h-15v8h30v-8h-15zM28 26h-4v-2h4v2zM7 10l8-8 8 8h-5v10h-6v-10z";
  const resetSnippets = () => {
    const snippets = $$`#imgViewer .snippet`;
    snippets.forEach((el) => {
      const snippetPlaceholder = $(".snippets").children[el.index];
      const elem = viewer.parentElement.removeChild(el);
      el.dx = null;
      el.dy = null;
      elem.setAttribute("draggable", false);
      snippetPlaceholder.appendChild(elem);
    });
  };
  resetSnippets();
  if (input.files.length) {
    iconPath.setAttribute("d", upload);
    iconPath.setAttribute("fill", colorWhite);
    $label.querySelector(".file_name").innerHTML = "Upload file";
    viewer.setAttribute(
      "src",
      "https://image.freepik.com/free-vector/abstract-background-flowing-dots_1048-12616.jpg"
    );
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
