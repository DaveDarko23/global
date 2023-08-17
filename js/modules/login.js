import access from "./access.js";
import fetchAsync from "./asyncFetch.js";

const $d = document;

$d.addEventListener("DOMContentLoaded", (e) => {
  console.log(localStorage.getItem("username"));
  if (localStorage.getItem("username") !== null) {
    location.href = "http://192.168.100.6/Global";
  }
  /*
  const array = {
    array: [
      [5, "Comida"],
      [35, "Ediardo"],
      [25, "Prieda"],
    ],
  };

  console.log(array);
  console.log(JSON.stringify(array));

  const envio = {
    url: "http://192.168.100.6/Global/scripts/prueba.php",
    method: "POST",
    success: (userInfo) => {
      alert(userInfo);
    },
    failed: () => {},
    data: JSON.stringify(array),
  };
  console.log(envio);
  fetchAsync(envio);*/
});

$d.addEventListener("submit", (e) => {
  e.preventDefault();

  const envio = {
    url: "http://192.168.100.6/Global/scripts/login.php",
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
  const $button = $d.getElementById("signUp");
  console.log(e.target === $button);

  if (e.target === $button) {
    location.href = "http://192.168.100.6/Global/register.html";
  }
});
