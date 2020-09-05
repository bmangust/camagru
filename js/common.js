const $ = (selector) => document.querySelector(selector);
const $$ = (selector) => document.querySelectorAll(selector);
const logEnable = true;
const baseURL = "http://localhost/camagru/";
const log = (...messages) => {
  if (logEnable) messages.forEach((message) => console.log(message));
};
let activeSnippet = null;

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

const getCookie = (name) => {
  let matches = document.cookie.match(
    new RegExp(
      "(?:^|; )" +
        name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, "\\$1") +
        "=([^;]*)"
    )
  );
  return matches ? decodeURIComponent(matches[1]) : undefined;
};

const showMessage = ({ text, error = true }) => {
  const className = error ? "float error" : "float";
  const template = `<div class="${className}">
      <div>${text}</div>
</div>`;
  const message = htmlToElement(template);
  $`main`.appendChild(message);
  setTimeout(() => {
    $("main").removeChild(message);
  }, 10000);
};

const imageToBase64 = (img) => {
  const canvas = document.createElement("canvas");
  canvas.width = img.width;
  canvas.height = img.height;
  const ctx = canvas.getContext("2d");
  const size = {
    width: img.naturalWidth,
    height: img.naturalHeight,
  };
  size.size = Math.min(size.width, size.height);
  size.sx = Math.abs(size.size - size.width) / 2;
  size.sy = Math.abs(size.size - size.height) / 2;
  ctx.drawImage(
    img,
    size.sx,
    size.sy,
    size.size,
    size.size,
    0,
    0,
    canvas.width,
    canvas.height
  );
  return canvas.toDataURL("image/png");
};
