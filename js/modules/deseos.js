import fetchAsync from "./asyncFetch.js";
import navController, { clickListener } from "./navegacion.js";

const $d = document;
let statusType = "";

$d.addEventListener("DOMContentLoaded", (e) => {
  statusType = localStorage.getItem("userType");

  if (statusType !== "Comprador") {
    location.href = "http://192.168.100.6/Global";
  }

  navController(statusType, $d);

  getProducts();
});

const getProducts = () => {
  const envio = {
    url: "http://192.168.100.6/Global/scripts/deseos.php",
    method: "POST",
    success: (userInfo) => {
      console.log(userInfo);
      if (userInfo.length === 0) {
        alert("No tienes deseos");
        return;
      }
      fillCards(userInfo.producto);
    },
    failed: () => alert("Ocurrió un error"),
    data: JSON.stringify({
      FK_Usuario: localStorage.getItem("PK_Usuario"),
    }),
  };

  fetchAsync(envio);
};

const fillCards = (userInfo) => {
  const $cardSection = $d.querySelector(".section-product"),
    $cardTemplate = $d.getElementById("card-template").content,
    $fragment = $d.createDocumentFragment();

  userInfo.forEach((element) => {
    $cardTemplate.querySelector("card").setAttribute("id", element.PK_Producto);
    $cardTemplate.querySelector("img").setAttribute("src", element.imagen);
    $cardTemplate.querySelector("h3").textContent = element.nombre;
    $cardTemplate.querySelector(".description").innerHTML =
      `<p>` + element.descripcion + `</p>`;
    $cardTemplate.querySelector("h4").textContent = element.username;

    let $clone = $d.importNode($cardTemplate, true);
    $fragment.appendChild($clone);
  });

  $cardSection.appendChild($fragment);

  cardsInteraction();
};

const cardsInteraction = () => {
  const $cards = $d.querySelectorAll(".card");

  $cards.forEach(($card) => {
    $card.addEventListener("click", (e) => {
      const $carrito = $card.querySelector("#carrito");
      const $delete = $card.querySelector("#delete");

      let url = "",
        color = "";

      if ($carrito === e.target) {
        url = "http://192.168.100.6/Global/scripts/addCarrito.php";
        color = "#72cb10";
      }
      if ($delete === e.target) {
        url = "http://192.168.100.6/Global/scripts/deleteDeseos.php";
        color = "#ffff72";
      }

      if ($carrito === e.target || $delete === e.target) {
        const Envio = {
          url,
          method: "POST",
          success: (userInfo) => {
            if (color === "#72cb10") $card.style.backgroundColor = color;
            else $d.querySelector(".section-product").removeChild($card);
          },
          failed: () => alert("Usuario Incorrecto"),
          data: JSON.stringify({
            FK_Producto: $card.getAttribute("id"),
            FK_Usuario: localStorage.getItem("PK_Type"),
          }),
        };

        fetchAsync(Envio);
      }
    });
  });
};

$d.addEventListener("click", (e) => {
  clickListener(e);
});
