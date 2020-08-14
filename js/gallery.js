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
    img.querySelector(".like").addEventListener("click", (e) => {
      e.preventDefault();
      like(e.target);
    });
    gallery.appendChild(img);
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
  const res = await response.text();
  if (res.startsWith("<")) {
    log(res);
    return;
  }
  const result = JSON.parse(res);
  if (result.success && result.data === "Like added") el.classList.add("liked");
  if (result.success && result.data === "Like removed")
    el.classList.remove("liked");
};

const getGallerySize = async () => {
  const url = new URL("api/image.php/size", baseURL);
  let resposne = await fetch(url);
  let resp = await resposne.json();
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
  let resposne = await fetch(url);
  let imgs = await resposne.json();
  if (limit >= gallerySize) {
    $`#next`.setAttribute("disabled", true);
    $`#last`.setAttribute("disabled", true);
  }
  placeImages(imgs.data);
};

const moreImages = async () => {
  const url = new URL("api/image.php/more", baseURL);
  offset += limit;
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let resposne = await fetch(url);
  let imgs = await resposne.json();
  currentPage++;
  if (currentPage * limit >= gallerySize) {
    const more = $("#more");
    more.innerHTML = "No more images";
    more.setAttribute("disabled", true);
  }
  placeImages(imgs.data);
};

const nextPage = async () => {
  const url = new URL("api/image.php/more", baseURL);
  offset += limit;
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let resposne = await fetch(url);
  const txt = await resposne.text();
  if (txt.startsWith("<")) {
    log(txt);
    return;
  }
  let imgs = JSON.parse(txt);
  currentPage++;
  $`#first`.removeAttribute("disabled");
  $`#prev`.removeAttribute("disabled");
  if (currentPage * limit >= gallerySize || imgs.data.length < limit) {
    $`#next`.setAttribute("disabled", true);
    $`#last`.setAttribute("disabled", true);
  }
  const gallery = $("#gallery");
  gallery.innerHTML = "";
  placeImages(imgs.data);
};

const prevPage = async () => {
  const url = new URL("api/image.php/more", baseURL);
  offset -= limit;
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let resposne = await fetch(url, { credentials: "include" });
  const txt = await resposne.text();
  if (txt.startsWith("<")) {
    log(txt);
    return;
  }
  let imgs = JSON.parse(txt);
  currentPage--;
  $`#next`.removeAttribute("disabled");
  $`#last`.removeAttribute("disabled");
  if (currentPage === 1) {
    $`#prev`.setAttribute("disabled", true);
    $`#first`.setAttribute("disabled", true);
  }
  const gallery = $("#gallery");
  gallery.innerHTML = "";
  placeImages(imgs.data);
};

const firstPage = async () => {
  const url = new URL("api/image.php/more", baseURL);
  currentPage = 1;
  offset = 0;
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let resposne = await fetch(url, { credentials: "include" });
  const txt = await resposne.text();
  if (txt.startsWith("<")) {
    log(txt);
    return;
  }
  let imgs = JSON.parse(txt);
  $`#prev`.setAttribute("disabled", true);
  $`#first`.setAttribute("disabled", true);
  $`#next`.removeAttribute("disabled");
  $`#last`.removeAttribute("disabled");
  const gallery = $("#gallery");
  gallery.innerHTML = "";
  placeImages(imgs.data);
};

const lastPage = async () => {
  const url = new URL("api/image.php/more", baseURL);
  currentPage = Math.floor(gallerySize / limit) + 1;
  offset = (currentPage - 1) * limit;
  const params = { offset: offset, limit: limit };
  url.search = new URLSearchParams(params);
  let resposne = await fetch(url, { credentials: "include" });
  const txt = await resposne.text();
  if (txt.startsWith("<")) {
    log(txt);
    return;
  }
  let imgs = JSON.parse(txt);
  $`#next`.setAttribute("disabled", true);
  $`#last`.setAttribute("disabled", true);
  $`#prev`.removeAttribute("disabled");
  $`#first`.removeAttribute("disabled");
  const gallery = $("#gallery");
  gallery.innerHTML = "";
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
