import access from "./access.js";
import fetchAsync from "./asyncFetch.js";

const $d = document;

$d.addEventListener("DOMContentLoaded", (e) => {
  console.log("Comida Rica");
});

$d.addEventListener("submit", (e) => {
  e.preventDefault();
  console.log("Click");
  const envio = {
    url: "http://10.0.0.3/global/scripts/register.php",
    method: "POST",
    success: (userInfo) => {
      if (userInfo.PK_Usuario > 0) {
        access(userInfo);
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
    location.href = "http://10.0.0.3/global/login.html";
  }
});
