const register = () => {
  window.location.href = "index.php?route=register";
};

const login = () => {
  window.location.href = "index.php?route=login";
};

const forgot = () => {
  window.location.href = "index.php?route=forgot";
};

// add email and submit form
window.onload = () => {
  const formRestore = document.querySelector("#restorePassword");
  if (formRestore) {
    formRestore.addEventListener("submit", async (e) => {
      const urlParams = new URLSearchParams(window.location.search);
      const email = document.createElement("input");
      email.type = "hidden";
      email.name = "email";
      email.value = urlParams.get("email");
      this.appendChild(email);
    });
  }
};
