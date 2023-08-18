import fetchAsync from "./asyncFetch.js";
import navController, { clickListener } from "./navegacion.js";

const $d = document;
let statusType = "";

$d.addEventListener("DOMContentLoaded", (e) => {
  statusType = localStorage.getItem("userType");
  navController(statusType, $d);

  if (statusType === "Vendedor") {
    getProducts(
      "sellerDashboard.php",
      JSON.stringify({ PK_Vendedor: localStorage.getItem("PK_Type") })
    );
  } else {
    getProducts("dashboard.php", null);
  }
});

const getProducts = (url, data) => {
  const envio = {
    url: "http://192.168.100.6/Global/scripts/" + url,
    method: "POST",
    success: (userInfo) => {
      fillCards(userInfo.producto.reverse());
    },
    failed: () => {},
    data,
  };

  fetchAsync(envio);
};

const fillCards = (userInfo) => {
  const template = statusType || "";
  const $cardSection = $d.querySelector(".section-product"),
    $fragment = $d.createDocumentFragment();
  const $cardTemplate = $d.getElementById("card-template-" + template).content;

  userInfo.forEach((element) => {
    const $card = $cardTemplate.querySelector("card");
    $cardTemplate.querySelector("card").setAttribute("id", element.PK_Producto);
    $cardTemplate.querySelector("img").setAttribute("src", element.imagen);
    $cardTemplate.querySelector("h3").textContent = element.nombre;
    $cardTemplate.querySelector(".description").innerHTML =
      `<p>` + element.descripcion + `</p>`;
    $cardTemplate.querySelector("h4").textContent = element.username;
    $cardTemplate.querySelector("#categoria").textContent = element.categoria;
    $cardTemplate.querySelector("#precio").textContent = element.precio;
    $cardTemplate.querySelector("#stock").textContent = element.stock;
    if (element.stock === "0") {
      $card.style.backgroundColor = "#e00000";
      $card
        .querySelector(".product-buttons")
        .removeChild($cardTemplate.querySelector("#delete"));
    } else {
      const deleteButton = $card.querySelector("#delete");
      $card.style.backgroundColor = "#ffffff";
      if (
        deleteButton === null &&
        localStorage.getItem("userType") === "Vendedor"
      ) {
        const $button = $d.createElement("button");
        $button.classList.add("button");
        $button.setAttribute("id", "delete");
        $button.textContent = "Eliminar";
        $card
          .querySelector(".product-buttons")
          .insertAdjacentElement("beforeend", $button);
      }
    }

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
      if (statusType === "Vendedor") {
        buttonCardControllerVendedor($card, e);
      }

      if (statusType === "Comprador") {
        buttonsCardControllerComprador($card, e);
      }
    });
  });
};

function buttonCardControllerVendedor($card, e) {
  const $editar = $card.querySelector("#edit");
  const $eliminar = $card.querySelector("#delete");

  if ($eliminar === e.target) {
    const Envio = {
      url: "http://192.168.100.6/Global/scripts/deleteProduct.php",
      method: "POST",
      success: (userInfo) => {
        location.reload();
      },
      failed: () => alert("Usuario Incorrecto"),
      data: JSON.stringify({
        FK_Producto: $card.getAttribute("id"),
      }),
    };

    fetchAsync(Envio);
  }

  if ($editar === e.target) {
    location.href =
      "http://192.168.100.6/global/edit-product.html?id=" +
      $card.getAttribute("id");
  }
}

function buttonsCardControllerComprador($card, e) {
  const $carrito = $card.querySelector("#carrito");
  const $deseos = $card.querySelector("#deseos");
  let url = "",
    color = "";

  if ($carrito === e.target) {
    url = "http://192.168.100.6/Global/scripts/addCarrito.php";
    color = "#72cb10";
  }
  if ($deseos === e.target) {
    url = "http://192.168.100.6/Global/scripts/addDeseos.php";
    color = "#ffff72";
  }

  if ($carrito === e.target || $deseos === e.target) {
    const Envio = {
      url,
      method: "POST",
      success: (userInfo) => {
        alert("Producto Agregado correctamente");
        $card.style.backgroundColor = color;
      },
      failed: () => alert("Usuario Incorrecto"),
      data: JSON.stringify({
        FK_Producto: $card.getAttribute("id"),
        FK_Usuario: localStorage.getItem("PK_Type"),
      }),
    };

    fetchAsync(Envio);
  }
}

$d.addEventListener("click", (e) => {
  clickListener(e);
});
