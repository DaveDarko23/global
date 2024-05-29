import access from "./access.js";
import fetchAsync from "./asyncFetch.js";
import { host } from "./url.js";

const $d = document;

$d.addEventListener("DOMContentLoaded", (e) => {
  console.log("Comida Rica");
});

$d.addEventListener("submit", (e) => {
  e.preventDefault();
  console.log("Click");
  const envio = {
    url: "http://" + host + "/global/scripts/register.php",
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
  const $button = $d.getElementById("logIn");
  console.log(e.target);

  if (e.target === $button) {
    location.href = "http://" + host + "/global/login.html";
  }
});
