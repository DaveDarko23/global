import fetchAsync from "./asyncFetch.js";
import navController, { clickListener } from "./navegacion.js";

const $d = document;
let statusType = "";

$d.addEventListener("DOMContentLoaded", (e) => {
  statusType = localStorage.getItem("userType");

  if (statusType === null) {
    location.href = "http://10.0.0.3/global";
  }

  navController(statusType, $d);

  getProducts();
});

const getProducts = () => {
  const envio = {
    url: "http://10.0.0.3/Global/scripts/compras.php",
    method: "POST",
    success: (userInfo) => {
      if (userInfo.length === 0) {
        alert("No tienes ventas por ahora");
        return;
      }
      fillCards(userInfo.producto);
    },
    failed: () => alert("Usuario Incorrecto"),
    data: JSON.stringify({
      FK_Usuario: localStorage.getItem("PK_Type"),
      field: localStorage.getItem("userType"),
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
    const $h5 = $cardTemplate.querySelector("h5");
    $h5.style.textAlign = "center";

    console.log(localStorage.getItem("userType"));

    /*if (localStorage.getItem("userType") === "Comprador") {
      if (element.status == 1) {
        // Pedido pero no entregado
        $cardTemplate.querySelector("card").style.backgroundColor = "#ffff72";
        $h5.textContent = "Esperando el Envío";
      } else {
        // Entregado
        $cardTemplate.querySelector("card").style.backgroundColor = "#72cb10";
        $h5.textContent = "Comprado";
      }
    }*/
    if (localStorage.getItem("userType") === "Comprador") {
      if (element.status == 1) {
        // Pedido pero no entregado
        $cardTemplate.querySelector("card").style.backgroundColor = "#ffff72";
        $h5.textContent = "Esperando el Envío";
      } else {
        // Entregado
        $cardTemplate.querySelector("card").style.backgroundColor = "#72cb10";
        $h5.textContent = "Entregado";
      }
    }

    if (localStorage.getItem("userType") === "Vendedor") {
      const $nodo = $d.createElement("button");
      $cardTemplate
        .querySelector(".product-buttons")
        .insertAdjacentElement("beforeend", $nodo);
      $cardTemplate
        .querySelector("card")
        .setAttribute("data-user", element.PK_Carrito);

      const $button = $cardTemplate.querySelector("button");
      $button.textContent = "Enviar";
      $button.classList.add("button-enviar");

      const $a = $d.createElement("a");
      $cardTemplate
        .querySelector(".product-buttons")
        .insertAdjacentElement("beforeend", $a);
    }

    const $pdf = $cardTemplate.querySelector("a");
    $pdf.setAttribute("href", element.pdf);
    $cardTemplate.querySelector("img").setAttribute("src", element.imagen);
    $cardTemplate.querySelector("h3").textContent = element.nombre;
    // $cardTemplate.querySelector(".description").innerHTML =
    //   `<p>` + element.descripcion + `</p>`;
    // $cardTemplate.querySelector("h4").textContent = element.username;

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
      const $enviar = $card.querySelector(".button-enviar");
      const $deseos = $card.querySelector("#deseos");

      if ($enviar === e.target) {
        const Envio = {
          url: "http://10.0.0.3/Global/scripts/sendProduct.php",
          method: "POST",
          success: (userInfo) => {
            alert("Producto Enviado");
            $card.style.backgroundColor = "#72cb10";
          },
          failed: () => alert("Usuario Incorrecto"),
          data: JSON.stringify({
            PK_Carrito: $card.getAttribute("data-user"),
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
