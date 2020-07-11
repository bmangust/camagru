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

const validateEmail = (form) => {
  const field = selectInput(form, "email");
  if (field.value === "") {
    return makeAlert(field, form, "Email must not be empty");
  } else if (field.value.toLowerCase().search(/\w+@\w+\.[a-z]+/) === -1) {
    return makeAlert(field, form, "This does not look like email");
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
const validatePassword = (form) => {
  const field = selectInput(form, "password");
  if (field.value.length < 8) {
    return makeAlert(
      field,
      form,
      "Password must be at least 8 characters long"
    );
  } else if (field.value.search(/[a-z]/) === -1) {
    return makeAlert(field, form, "Password must contain lowercase letters");
  } else if (field.value.search(/[A-Z]/) === -1) {
    return makeAlert(field, form, "Password must contain uppercase letters");
  } else if (field.value.search(/\s/) !== -1) {
    return makeAlert(field, form, "Password must not contain spaces");
  } else if (field.value.search(/\W/) === -1) {
    return makeAlert(
      field,
      form,
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
const confirmPassword = (form) => {
  const password = selectInput(form, "password").value;
  const confirm = selectInput(form, "confirm");
  if (password.localeCompare(confirm.value)) {
    return makeAlert(confirm, form, "Password is not confirmed");
  }
  confirm.classList.remove("invalid");
  return true;
};

const validateRegisterForm = () => {
  return (
    validateEmail("register") &&
    validateUsername("register") &&
    validatePassword("register") &&
    confirmPassword("register")
  );
};

const validateLoginForm = () => {
  return validateUsername("login") && validateLoginPassword();
};

const validateRestoreForm = () => {
  return validatePassword("restore") && confirmPassword("restore");
};

const validateForgotForm = () => {
  return validateEmail("forgot");
};
