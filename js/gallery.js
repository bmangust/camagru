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

const placeImages = (data, target) => {
  data.forEach((el, index) => {
    // if (+data[index].isPrivate) return;
    const classes = data[index].imgid ? "like liked" : "like";
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
    img.querySelector(".like").addEventListener("click", (e) => {
      e.preventDefault();
      like(e.target);
    });
    target.appendChild(img);
  });
};

const placeImagesWithControls = (data, target) => {
  data.forEach((el, index) => {
    const template = `
    <div class="imgWrapper">
        <a href="index.php?img=${el.name}&id=${el.id}"><img src="assets/uploads/${el.name}">
        <div class="info">
            <div class="author">
                <span class="author-name">${el.user}</span>
            </div>
            <div class="private"><svg><path/></svg></div>
            <div class="remove">
                <svg viewBox="0 0 512 512">
                <path d="M284.286,256.002L506.143,34.144c7.811-7.811,7.811-20.475,0-28.285c-7.811-7.81-20.475-7.811-28.285,0L256,227.717    L34.143,5.859c-7.811-7.811-20.475-7.811-28.285,0c-7.81,7.811-7.811,20.475,0,28.285l221.857,221.857L5.858,477.859    c-7.811,7.811-7.811,20.475,0,28.285c3.905,3.905,9.024,5.857,14.143,5.857c5.119,0,10.237-1.952,14.143-5.857L256,284.287    l221.857,221.857c3.905,3.905,9.024,5.857,14.143,5.857s10.237-1.952,14.143-5.857c7.811-7.811,7.811-20.475,0-28.285    L284.286,256.002z" fill="rgb(255,255,255)"/>
                </svg>
            </div>
        </div>
        </a>
    </div>`;
    const img = htmlToElement(template);
    if (+data[index].isPrivate) {
      showPrivateIcon(img.querySelector(".private"));
    } else {
      showPublicIcon(img.querySelector(".private"));
    }
    img.querySelector(".private").addEventListener("click", (e) => {
      e.preventDefault();
      updatePrivacy(e.target);
    });
    img.querySelector(".remove").addEventListener("click", (e) => {
      e.preventDefault();
      removeImage(e.target);
    });
    target.appendChild(img);
  });
};

const like = async (el) => {
  const url = "api/image.php/like";
  const img = el.closest("a");
  const urlParams = new URLSearchParams(img.href.split("?")[1]);
  const id = urlParams.get("id");
  const name = urlParams.get("img");
  const params = {
    method: "PUT",
    headers: {
      "Content-Type": "application/json;charset=utf-8",
    },
  };
  const body = { liked: false, id: id, name: name };
  let response;
  if (el.classList.contains("liked")) {
    params.body = JSON.stringify(body);
    response = await fetch(url, params);
  } else {
    body.liked = true;
    params.body = JSON.stringify(body);
    response = await fetch(url, params);
  }
  const txt = await response.text();
  let result;
  try {
    result = JSON.parse(txt);
  } catch (e) {
    log(txt);
    return;
  }
  if (result.success && result.data === "Like added") el.classList.add("liked");
  else if (result.success && result.data === "Like removed")
    el.classList.remove("liked");
};

const showPrivateIcon = (el) => {
  el.classList.add("isPrivate");
  el.querySelector("path").setAttribute(
    "d",
    "M474.609,228.901c-29.006-38.002-63.843-71.175-103.219-98.287l67.345-67.345c6.78-6.548,6.968-17.352,0.42-24.132    c-6.548-6.78-17.352-6.968-24.132-0.42c-0.142,0.137-0.282,0.277-0.42,0.42l-73.574,73.506    c-31.317-17.236-66.353-26.607-102.093-27.307C109.229,85.336,7.529,223.03,3.262,228.9c-4.349,5.983-4.349,14.087,0,20.07    c29.006,38.002,63.843,71.175,103.219,98.287l-67.345,67.345c-6.78,6.548-6.968,17.352-0.42,24.132    c6.548,6.78,17.352,6.968,24.132,0.42c0.142-0.137,0.282-0.277,0.42-0.42l73.574-73.506    c31.317,17.236,66.353,26.607,102.093,27.307c129.707,0,231.407-137.694,235.674-143.565    C478.959,242.988,478.959,234.884,474.609,228.901z M131.296,322.494c-34.767-23.156-65.931-51.311-92.484-83.558    c25.122-30.43,106.598-119.467,200.124-119.467c26.609,0.538,52.77,6.949,76.612,18.773L285.92,167.87    c-39.2-26.025-92.076-15.345-118.101,23.855c-18.958,28.555-18.958,65.691,0,94.246L131.296,322.494z M285.016,217.005    c3.34,6.83,5.091,14.328,5.12,21.931c0,28.277-22.923,51.2-51.2,51.2c-7.603-0.029-15.101-1.78-21.931-5.12L285.016,217.005z     M192.856,260.866c-3.34-6.83-5.091-14.328-5.12-21.931c0-28.277,22.923-51.2,51.2-51.2c7.603,0.029,15.101,1.78,21.931,5.12    L192.856,260.866z M238.936,358.402c-26.609-0.538-52.769-6.949-76.612-18.773l29.628-29.628    c39.2,26.025,92.076,15.345,118.101-23.855c18.958-28.555,18.958-65.691,0-94.246l36.523-36.523    c34.767,23.156,65.931,51.312,92.484,83.558C413.937,269.366,332.461,358.402,238.936,358.402z"
  );
  el.querySelector("path").setAttribute("fill", "rgb(255,255,255)");
  el.querySelector("svg").setAttribute("viewBox", "0 0 478 478");
};

const showPublicIcon = (el) => {
  el.classList.remove("isPrivate");
  el.querySelector("path").setAttribute(
    "d",
    "M508.745,246.041c-4.574-6.257-113.557-153.206-252.748-153.206S7.818,239.784,3.249,246.035    c-4.332,5.936-4.332,13.987,0,19.923c4.569,6.257,113.557,153.206,252.748,153.206s248.174-146.95,252.748-153.201    C513.083,260.028,513.083,251.971,508.745,246.041z M255.997,385.406c-102.529,0-191.33-97.533-217.617-129.418    c26.253-31.913,114.868-129.395,217.617-129.395c102.524,0,191.319,97.516,217.617,129.418    C447.361,287.923,358.746,385.406,255.997,385.406z M255.997,154.725c-55.842,0-101.275,45.433-101.275,101.275s45.433,101.275,101.275,101.275    s101.275-45.433,101.275-101.275S311.839,154.725,255.997,154.725z M255.997,323.516c-37.23,0-67.516-30.287-67.516-67.516    s30.287-67.516,67.516-67.516s67.516,30.287,67.516,67.516S293.227,323.516,255.997,323.516z"
  );
  el.querySelector("path").setAttribute("fill", "rgb(255,255,255)");
  el.querySelector("svg").setAttribute("viewBox", "0 0 512 512");
};

const updatePrivacy = async (el) => {
  const url = "api/image.php/private";
  const img = el.closest("a");
  const private = el.closest(".private");
  const urlParams = new URLSearchParams(img.href.split("?")[1]);
  const id = urlParams.get("id");
  const name = urlParams.get("img");
  const params = {
    method: "PUT",
    headers: {
      "Content-Type": "application/json;charset=utf-8",
    },
  };
  const body = { private: false, id: id, name: name };
  let response;
  if (private.classList.contains("isPrivate")) {
    params.body = JSON.stringify(body);
    response = await fetch(url, params);
  } else {
    body.private = true;
    params.body = JSON.stringify(body);
    response = await fetch(url, params);
  }
  const txt = await response.text();
  log(txt);
  let result;
  try {
    result = JSON.parse(txt);
  } catch (e) {
    log(txt);
    return;
  }
  if (result.success && result.data === "private") {
    showPrivateIcon(private);
  } else if (result.success && result.data === "public") {
    showPublicIcon(private);
  }
};

const getGallerySize = async () => {
  const url = new URL("api/image.php/size", baseURL);
  let response = await fetch(url);
  let resp = await response.json();
  return +resp.data;
};

let offset = 0;
let limit = 6;
let gallerySize;
getGallerySize().then((val) => (gallerySize = val));
let currentPage = 1;

const loadImages = async () => {
  const url = new URL("api/image.php/more", baseURL);
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let response = await fetch(url);
  let imgs = await response.json();
  if (limit >= gallerySize) {
    let params = new URL(document.location).searchParams;
    let route = params.get("route");
    if (route === "create") {
      $`#next`.setAttribute("disabled", true);
      $`#last`.setAttribute("disabled", true);
    } else if (route === "gallery") {
      $`#more`.setAttribute("disabled", true);
    }
  }
  const gallery = $(".gallery");
  placeImages(imgs.data, gallery);
};

const showGallery = async () => {
  const url = new URL("api/image.php/my", baseURL);
  const params = { offset: 0 };
  url.search = new URLSearchParams(params);
  let response = await fetch(url);
  const txt = await response.text();
  let imgs;
  try {
    imgs = JSON.parse(txt);
  } catch (e) {
    log(txt);
    return;
  }
  const gallery = document.createElement("div");
  gallery.classList.add("gallery");
  let target = $`main`;
  target.innerHTML = "";
  placeImagesWithControls(imgs.data, gallery);
  target.appendChild(gallery);
};

const moreImages = async () => {
  const url = new URL("api/image.php/more", baseURL);
  offset += limit;
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let response = await fetch(url);
  const txt = await response.text();
  let imgs;
  try {
    imgs = JSON.parse(txt);
  } catch (e) {
    log(txt);
    return;
  }
  currentPage++;
  if (currentPage * limit >= gallerySize) {
    const more = $("#more");
    more.innerHTML = "No more images";
    more.setAttribute("disabled", true);
  }
  const gallery = $(".gallery");
  placeImages(imgs.data, gallery);
};

const nextPage = async () => {
  const url = new URL("api/image.php/more", baseURL);
  offset += limit;
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let response = await fetch(url);
  const txt = await response.text();
  let imgs;
  try {
    imgs = JSON.parse(txt);
  } catch (e) {
    log(txt);
    return;
  }
  currentPage++;
  $`#first`.removeAttribute("disabled");
  $`#prev`.removeAttribute("disabled");
  if (currentPage * limit >= gallerySize || imgs.data.length < limit) {
    $`#next`.setAttribute("disabled", true);
    $`#last`.setAttribute("disabled", true);
  }
  const gallery = $(".gallery");
  gallery.innerHTML = "";
  placeImages(imgs.data, gallery);
};

const prevPage = async () => {
  const url = new URL("api/image.php/more", baseURL);
  offset -= limit;
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let response = await fetch(url, { credentials: "include" });
  const txt = await response.text();
  let imgs;
  try {
    imgs = JSON.parse(txt);
  } catch (e) {
    log(txt);
    return;
  }
  currentPage--;
  $`#next`.removeAttribute("disabled");
  $`#last`.removeAttribute("disabled");
  if (currentPage === 1) {
    $`#prev`.setAttribute("disabled", true);
    $`#first`.setAttribute("disabled", true);
  }
  const gallery = $(".gallery");
  gallery.innerHTML = "";
  placeImages(imgs.data, gallery);
};

const firstPage = async () => {
  const url = new URL("api/image.php/more", baseURL);
  currentPage = 1;
  offset = 0;
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let response = await fetch(url, { credentials: "include" });
  const txt = await response.text();
  let imgs;
  try {
    imgs = JSON.parse(txt);
  } catch (e) {
    log(txt);
    return;
  }
  $`#prev`.setAttribute("disabled", true);
  $`#first`.setAttribute("disabled", true);
  $`#next`.removeAttribute("disabled");
  $`#last`.removeAttribute("disabled");
  const gallery = $(".gallery");
  gallery.innerHTML = "";
  placeImages(imgs.data, gallery);
};

const lastPage = async () => {
  const url = new URL("api/image.php/more", baseURL);
  currentPage = Math.floor(gallerySize / limit) + 1;
  offset = (currentPage - 1) * limit;
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let response = await fetch(url, { credentials: "include" });
  const txt = await response.text();
  let imgs;
  try {
    imgs = JSON.parse(txt);
  } catch (e) {
    log(txt);
    return;
  }
  $`#next`.setAttribute("disabled", true);
  $`#last`.setAttribute("disabled", true);
  $`#prev`.removeAttribute("disabled");
  $`#first`.removeAttribute("disabled");
  const gallery = $(".gallery");
  gallery.innerHTML = "";
  placeImages(imgs.data, gallery);
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