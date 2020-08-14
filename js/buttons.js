const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => document.querySelectorAll(selector);
const logEnable = true;
const baseURL = "http://localhost/camagru/";
const log = (message) => {
  if (logEnable) console.log(message);
};

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

const htmlToElements = (html) => {
  var template = document.createElement("template");
  template.innerHTML = html.trim();
  return template.content.childNodes;
};

const htmlToElement = (html) => {
  var template = document.createElement("template");
  template.innerHTML = html.trim();
  return template.content.firstChild;
};

function getCookie(name) {
  let matches = document.cookie.match(
    new RegExp(
      "(?:^|; )" +
        name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, "\\$1") +
        "=([^;]*)"
    )
  );
  return matches ? decodeURIComponent(matches[1]) : undefined;
}

const placeImages = (data) => {
  const gallery = $("#gallery");
  data.forEach((el) => {
    const classes = el.imgid ? "like liked" : "like";
    const template = `
    <div class="imgWrapper">
        <a href="index.php?img=${el.name}&id=${el.id}"><img src="assets/uploads/${el.name}">
        <div class="info">
            <div class="author">
                <span class="author-name">${el.user}</span>
            </div>
            <div class="${classes}"></div>
        </div>
        </a>
    </div>`;
    const img = htmlToElement(template);
    gallery.appendChild(img);
  });
};

const moreImages = async () => {
  const url = new URL("api/image.php/more", baseURL);
  const limit = +getCookie("limit");
  let offset = +getCookie("offset");
  const params = { offset: offset, limit: limit };
  offset += limit;
  document.cookie = `limit=${limit}`;
  document.cookie = `offset=${offset}`;
  url.search = new URLSearchParams(params);
  let resposne = await fetch(url, { credentials: "include" });
  let imgs = await resposne.json();
  // console.log(imgs);
  if (imgs.data.length === 0 || imgs.data.length < limit) {
    const more = $("#more");
    more.innerHTML = "No more images";
    more.setAttribute("disabled", true);
  }
  placeImages(imgs.data);
};

const nextPage = async () => {
  const gallery = $("#gallery");
  // const galleryWidth = gallery.offsetWidth;
  // const numberOfCols = Math.floor(galleryWidth / 100);
  // const number = numberOfCols * 3;

  const url = new URL("api/image.php/more", baseURL);
  let limit = +getCookie("limit");
  // limit = number;
  let offset = +getCookie("offset");
  // let offset = $$(".info").length;
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let resposne = await fetch(url, { credentials: "include" });
  let imgs = await resposne.json();
  console.log(imgs);
  $`#prev`.removeAttribute("disabled");
  if (imgs.data.length === 0 || imgs.data.length < limit) {
    $`#next`.setAttribute("disabled", true);
    offset -= limit;
  } else {
    offset += limit;
  }
  document.cookie = `limit=${limit}`;
  document.cookie = `offset=${offset}`;
  while (gallery.children.length > 0) {
    gallery.removeChild(gallery.children[0]);
  }
  placeImages(imgs.data);
};

const prevPage = async () => {
  const gallery = $("#gallery");
  // const galleryWidth = gallery.offsetWidth;
  // const numberOfCols = Math.floor(galleryWidth / 100);
  // const number = numberOfCols * 3;

  const url = new URL("api/image.php/more", baseURL);
  let limit = +getCookie("limit");
  // limit = number;
  let offset = +getCookie("offset");
  // let offset = $$(".info").length;
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let resposne = await fetch(url, { credentials: "include" });
  let imgs = await resposne.json();
  console.log(imgs);
  $`#next`.removeAttribute("disabled");
  if (offset === 0) {
    $`#prev`.setAttribute("disabled", true);
    offset += limit;
  } else {
    offset -= limit;
  }
  document.cookie = `limit=${limit}`;
  document.cookie = `offset=${offset}`;
  while (gallery.children.length > 0) {
    gallery.removeChild(gallery.children[0]);
  }
  placeImages(imgs.data);
};

const sendImages = () => {
  // const input = $(".input-file");
  const form = $`.controls_form`;
  const snippets = $$`#imgViewer .snippet`;
  const img = $`#imgViewer .base`;
  // if (input.files.length === 0 && input.lastFile) {
  //   input.files.push(input.lastFile);
  // }
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
  const form = document.createElement("FORM");
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

const resetSnippets = () => {
  const viewer = $("#imgViewer .base");
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

const clearViewer = () => {
  const viewer = $("#imgViewer .base");
  const $label = $(".file_label");
  const iconPath = $(".icon path");
  const colorWhite = "#fff";
  const upload =
    "M15 22h-15v8h30v-8h-15zM28 26h-4v-2h4v2zM7 10l8-8 8 8h-5v10h-6v-10z";
  iconPath.setAttribute("d", upload);
  iconPath.setAttribute("fill", colorWhite);
  $label.querySelector(".file_name").innerHTML = "Upload file";
  viewer.setAttribute("src", "./assets/bg.jpg");
};

const clearEdit = () => {
  clearViewer();
  resetSnippets();
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
        fileName = element.target.value.split("\\").pop();
      }
      if (fileName) {
        iconPath.setAttribute("d", tick);
        iconPath.setAttribute("fill", colorGreen);
        $label.querySelector(".file_name").innerHTML = fileName;
        viewer.src = URL.createObjectURL(element.target.files[0]);
        viewer.onload = () => URL.revokeObjectURL(viewer.src);
      } else {
        clearViewer();
      }
    });
  }
};

const like = (el) => {
  const url = "api/image.php/like";
  const img = el.closest("a");
  const urlParams = new URLSearchParams(img.href.split("?")[1]);
  const id = urlParams.get("id");
  const name = urlParams.get("img");
  if (el.classList.contains("liked")) {
    fetch(url, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json;charset=utf-8",
      },
      body: JSON.stringify({ liked: false, id: id, name: name }),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) el.classList.remove("liked");
      });
  } else {
    fetch(url, {
      method: "PUT",
      headers: {
        "Content-Type": "application/json;charset=utf-8",
      },
      body: JSON.stringify({ liked: true, id: id, name: name }),
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) el.classList.add("liked");
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
  const likeButtons = $$(".like");
  if (likeButtons) {
    likeButtons.forEach((el) =>
      el.addEventListener("click", (e) => {
        if (e.target === el) {
          e.preventDefault();
          // add check if user is authorized
          like(el);
        }
      })
    );
  }
};
