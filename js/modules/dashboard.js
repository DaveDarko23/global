import fetchAsync from "./asyncFetch.js";
import navController, { clickListener } from "./navegacion.js";
import { host } from "./url.js";

const $d = document;
let statusType = "";

$d.addEventListener("DOMContentLoaded", (e) => {
  statusType = localStorage.getItem("userType");
  console.log(statusType);
  navController(statusType, $d);

  const busqueda = getParameterByName("search");
  let data;
  if (statusType === "vendedor") {
    data = busqueda === "" ? null : busqueda;
    getProducts(
      "sellerDashboard.php",
      JSON.stringify({
        PK_Vendedor: localStorage.getItem("PK_Type"),
        busqueda: data,
      })
    );
  } else {
    data = busqueda === "" ? null : JSON.stringify({ busqueda });
    getProducts("dashboard.php", data);
  }

  const $btnBuscar = $d.querySelector(".button-search");
  const $search = $d.querySelector(".search-bar");
  $btnBuscar.addEventListener("click", (e) => {
    console.log($search.value);
    if ($search.value !== "") {
      //alert("Buscar");
      location.href = "http://" + url + "/index.html?search=" + $search.value;
    } else {
      alert("Escribe un producto para buscar");
    }
  });
});

function getParameterByName(name) {
  name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
  return results === null
    ? ""
    : decodeURIComponent(results[1].replace(/\+/g, " "));
}

const getProducts = (url, data) => {
  const envio = {
    url: "http://" + host + "/scripts/" + url,
    method: "POST",
    success: (userInfo) => {
      const set = new Set();

      userInfo.producto.forEach((e) => set.add(e.categoria));
      console.log(set);
      set.forEach((e) => {
        const $categorieName = $d.createElement("h2");
        $categorieName.textContent = e;
        const $sectionProduct = $d.createElement("section");
        $sectionProduct.classList.add("section-product");
        $sectionProduct.setAttribute("id", e);
        $d.querySelector(".categories-section").insertAdjacentElement(
          "beforeend",
          $categorieName
        );
        $d.querySelector(".categories-section").insertAdjacentElement(
          "beforeend",
          $sectionProduct
        );
        console.log(userInfo.producto.reverse());
        fillCards(userInfo.producto.reverse(), e);
      });
      cardsInteraction();
    },
    failed: () => {},
    data,
  };

  console.log(envio);

  fetchAsync(envio);
};

const fillCards = (userInfo, categorieSection) => {
  const template = statusType || "";
  const $cardSection = $d.querySelector(".section-product#" + categorieSection),
    $fragment = $d.createDocumentFragment();
  const $cardTemplate = $d.getElementById("card-template-" + template).content;

  userInfo.forEach((element) => {
    console.log(element);
    if (categorieSection === element.categoria) {
      const $card = $cardTemplate.querySelector("card");
      $cardTemplate
        .querySelector("card")
        .setAttribute("id", element.PK_Producto);
      $cardTemplate.querySelector("img").setAttribute("src", element.imagen);
      $cardTemplate.querySelector("h3").textContent = element.nombre;
      // $cardTemplate.querySelector(".description").innerHTML =
      //   `<p>` + element.descripcion + `</p>`;
      $cardTemplate.querySelector("h4").textContent = element.username;
      // $cardTemplate.querySelector("#categoria").textContent = element.categoria;
      $cardTemplate.querySelector("#precio").textContent = element.precio;
      $cardTemplate.querySelector("#stock").textContent = element.stock;
      if (element.stock === "0") {
        $card.style.backgroundColor = "#e00000";
        /*$card
          .querySelector(".product-buttons")
          .removeChild($cardTemplate.querySelector("#delete"));*/
      } else {
        const deleteButton = $card.querySelector("#delete");
        $card.style.backgroundColor = "#ffffff";
        if (
          deleteButton === null &&
          localStorage.getItem("userType") === "vendedor"
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
      console.log($cardTemplate);
      console.log($clone);
      console.log($fragment);
    }
  });

  $cardSection.appendChild($fragment);
};

const cardsInteraction = () => {
  const $cards = $d.querySelectorAll(".card");
  console.log($cards);

  $cards.forEach(($card) => {
    $card.addEventListener("click", (e) => {
      if (statusType === "vendedor") {
        buttonCardControllerVendedor($card, e);
      }

      if (statusType === "comprador") {
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
      url: "http://" + host + "/scripts/deleteProduct.php",
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
      "http://" + host + "/edit-product.html?id=" + $card.getAttribute("id");
  }
}

function buttonsCardControllerComprador($card, e) {
  const $carrito = $card.querySelector("#carrito");
  const $deseos = $card.querySelector("#deseos");
  let url = "",
    color = "";

  if ($carrito === e.target) {
    url = "http://" + host + "/scripts/addCarrito.php";
    color = "#72cb10";
  }
  if ($deseos === e.target) {
    url = "http://" + host + "/scripts/addDeseos.php";
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
  clickListener(e, host);
});
