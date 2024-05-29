import fetchAsync from "./asyncFetch.js";
import { host } from "./url.js";
// console.log(host);

const $d = document;
let statusType = "";

$d.addEventListener("DOMContentLoaded", (e) => {
  statusType = localStorage.getItem("userType");
  if (statusType !== "vendedor") location.href = "http://" + host + "/global";

  const envio = {
    url: "http://" + host + "/global/scripts/getCategories.php",
    method: "POST",
    success: (answer) => {
      const $selector = $d.querySelector("[name='categoria']");
      answer.categoria.forEach((element) => {
        const $option = $d.createElement("option");
        $option.setAttribute("value", element.PK_Categoria);
        $option.textContent = element.nombre;
        $selector.insertAdjacentElement("beforeend", $option);
      });
    },
    failed: () => alert("Ocurrió un Accidente"),
    data: null,
  };

  fetchAsync(envio);

  navController();
});

$d.addEventListener("submit", (e) => {
  e.preventDefault();
  const $fk = $d.querySelector("#fk_vendedor");
  $fk.setAttribute("value", localStorage.getItem("PK_Type"));
  console.log($fk);
  const envio = {
    url: "http://" + host + "/global/scripts/addProduct.php",
    method: "POST",
    success: (answer) => {
      if (answer === 200) {
        location.href = "http://" + host + "/global";
      }
    },
    failed: () => alert("Ocurrió un Accidente"),
    data: new FormData(e.target),
  };

  fetchAsync(envio);
});

const addLi = ($nav, text) => {
  const $li = $d.createElement("li");
  $li.textContent = text;
  $nav.insertAdjacentElement("afterbegin", $li);
};

const navController = () => {
  const $nav = $d.querySelector(".header-nav");
  if (statusType === null) {
    addLi($nav, "Iniciar Sesión");
  }

  if (statusType === "vendedor") {
    addLi($nav, "Cerrar Sesión");
    addLi($nav, "Compras");
    addLi($nav, "Home");
  }

  if (statusType === "comprador") {
    addLi($nav, "Cerrar Sesión");
    addLi($nav, "Compras");
    addLi($nav, "Carrito");
    addLi($nav, "Deseos");
    addLi($nav, "Home");
  }
};

$d.addEventListener("click", (e) => {
  const url = "http://" + host + "/global/";
  console.log("Nav URL: " + url);
  switch (e.target.innerHTML) {
    case "Iniciar Sesión":
      location.href = url + "login.html";
      break;
    case "Cerrar Sesión":
      localStorage.clear();
      location.reload();
      break;
    case "Compras":
      location.href = url + "compras.html";
      break;
    case "Carrito":
      location.href = url + "carrito.html";
      break;
    case "Deseos":
      location.href = url + "deseos.html";
      break;
    case "Deseos":
      break;
  }
});
