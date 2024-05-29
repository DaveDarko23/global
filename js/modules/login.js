import access from "./access.js";
import fetchAsync from "./asyncFetch.js";
import { host } from "./url.js";

const $d = document;
console.log(host);

$d.addEventListener("DOMContentLoaded", (e) => {
  console.log(localStorage.getItem("username"));
  if (localStorage.getItem("username") !== null) {
    location.href = "http://" + host + "";
  }
});

$d.addEventListener("submit", (e) => {
  e.preventDefault();

  const envio = {
    url: "http://" + host + "/scripts/login.php",
    method: "POST",
    success: (userInfo) => {
      if (userInfo.PK_Usuario > 0) {
        access(userInfo, host);
      } else {
        failed();
      }
    },
    failed: () => alert("Usuario Incorrecto"),
    data: new FormData(e.target),
  };
  console.log(envio);
  fetchAsync(envio);
});

$d.addEventListener("click", (e) => {
  const $button = $d.getElementById("signUp");
  console.log(e.target);
  console.log(e.target === $button);

  if (e.target === $button) {
    location.href = "http://" + host + "/register.html";
  }
});
