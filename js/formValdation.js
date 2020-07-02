const makeAlert = (field, form, text) => {
  field.classList.add("invalid");
  let alert = document.querySelector("#alert");
  if (!alert) {
    alert = document.createElement("div");
    alert.setAttribute("id", "alert");
  }
  alert.innerHTML = text;
  document.forms[form].appendChild(alert);
  return false;
};
const selectInput = (form, selector) => {
  return document.forms[form][selector];
};

const validateEmail = () => {
  const field = selectInput("register", "email");
  if (field.value === "") {
    return makeAlert(field, "register", "Email must not be empty");
  } else if (field.value.search(/\w+@\w+\.[a-z]+/) === -1) {
    return makeAlert(field, "register", "This does not look like email");
  }
  field.classList.remove("invalid");
  return true;
};
const validateUsername = (form) => {
  const field = selectInput(form, "username");
  if (field.value === "") {
    return makeAlert(field, form, "Username must not be empty");
  } else if (field.value.search(/[^a-zA-Z0-9_]/) !== -1) {
    return makeAlert(
      field,
      form,
      "Only latin letters, numbers and underscore is allowed"
    );
  }
  field.classList.remove("invalid");
  return true;
};
const validatePassword = () => {
  const field = selectInput("register", "password");
  if (field.value.length < 8) {
    return makeAlert(
      field,
      "register",
      "Password must be at least 8 characters long"
    );
  } else if (field.value.search(/[a-z]/) === -1) {
    return makeAlert(
      field,
      "register",
      "Password must contain lowercase letters"
    );
  } else if (field.value.search(/[A-Z]/) === -1) {
    return makeAlert(
      field,
      "register",
      "Password must contain uppercase letters"
    );
  } else if (field.value.search(/\s/) !== -1) {
    return makeAlert(field, "register", "Password must not contain spaces");
  } else if (field.value.search(/\W/) === -1) {
    return makeAlert(
      field,
      "register",
      "Password must contain at least one special character"
    );
  }
  field.classList.remove("invalid");
  return true;
};
const validateLoginPassword = () => {
  const field = selectInput("login", "password");
  if (field.value.length === 0) {
    return makeAlert(field, "login", "Password must not be empty");
  }
  field.classList.remove("invalid");
  return true;
};
const confirmPassword = () => {
  const password = selectInput("register", "password").value;
  const confirm = selectInput("register", "confirm");
  if (password.localeCompare(confirm.value)) {
    return makeAlert(confirm, "register", "Password is not confirmed");
  }
  confirm.classList.remove("invalid");
  return true;
};

const validateRegisterForm = () => {
  return (
    validateEmail() &&
    validateUsername("register") &&
    validatePassword() &&
    confirmPassword()
  );
};

const validateLoginForm = () => {
  return validateUsername("login") && validateLoginPassword();
};
