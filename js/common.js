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
