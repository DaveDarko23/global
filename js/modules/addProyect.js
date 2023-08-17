import fetchAsync from "./asyncFetch.js";

const $d = document;
let statusType = "";

$d.addEventListener("DOMContentLoaded", (e) => {
  statusType = localStorage.getItem("userType");
  if (statusType !== "Vendedor") location.href = "http://192.168.100.6/global";

  navController();
});

$d.addEventListener("submit", (e) => {
  e.preventDefault();
  const $fk = $d.querySelector("#fk_vendedor");
  $fk.setAttribute("value", localStorage.getItem("PK_Type"));
  console.log($fk);
  const envio = {
    url: "http://192.168.100.6/Global/scripts/addProduct.php",
    method: "POST",
    success: (answer) => {
      if (answer === 200) {
        location.href = "http://192.168.100.6/global";
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

  if (statusType === "Vendedor") {
    addLi($nav, "Cerrar Sesión");
    addLi($nav, "Compras");
    addLi($nav, "Home");
  }

  if (statusType === "Comprador") {
    addLi($nav, "Cerrar Sesión");
    addLi($nav, "Compras");
    addLi($nav, "Carrito");
    addLi($nav, "Deseos");
    addLi($nav, "Home");
  }
};

$d.addEventListener("click", (e) => {
  const url = "http://192.168.100.6/global/";
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
