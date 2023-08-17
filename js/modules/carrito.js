import fetchAsync from "./asyncFetch.js";
import navController, { clickListener } from "./navegacion.js";

const $d = document;
let statusType = "";

$d.addEventListener("DOMContentLoaded", (e) => {
  statusType = localStorage.getItem("userType");

  if (statusType === null) {
    location.href = "http://192.168.100.6/Global";
  }

  navController(statusType, $d);

  getProducts();

  const $comprar = $d.querySelector("#buy");
  $comprar.addEventListener("click", (e) => {
    const $sectionForm = $d.querySelector("#form");
    $sectionForm.innerHTML = `<form class="estados-form">
    <input
      class="input-text"
      type="text"
      name="domicilio"
      placeholder="Domicilio"
      required
    />
    <select name="estado" class="input-text"></select>
    <input class="button" id="logIn" type="submit" value="Confirmar Domicilio" />
    <input type="hidden" name="id" />
  </form>`;

    const envio = {
      url: "http://192.168.100.6/Global/scripts/getStates.php",
      method: "POST",
      success: (userInfo) => {
        const $select = $sectionForm.querySelector("select");
        userInfo.Estado.forEach((estado) => {
          const option = `<option value="${estado.PK_Estado}">${estado.Nombre}</option>`;
          $select.innerHTML += option;
        });
      },
      failed: () => alert("Usuario Incorrecto"),
      data: null,
    };
    fetchAsync(envio);

    $d.addEventListener("submit", (e) => {
      e.preventDefault();
      const $id = $sectionForm.querySelector("[name='id']");
      $id.setAttribute("value", localStorage.getItem("PK_Type"));
      console.log($id);
      const envio = {
        url: "http://192.168.100.6/Global/scripts/setDomicilio.php",
        method: "POST",
        success: (userInfo) => {
          enviarCompra();
        },
        failed: () => alert("Usuario Incorrecto"),
        data: new FormData(e.target),
      };
      console.log(envio);
      fetchAsync(envio);
    });
  });
});

function enviarCompra() {
  const $cards = $d.querySelectorAll(".card");

  const array = { array: [] };
  $cards.forEach(($card) => {
    console.log($card.getAttribute("id"));
    const $cantidad = $card.querySelector(".square-value").textContent;
    const $name = $card.querySelector("h3").textContent;
    const $precio = $card.querySelector("#precio").textContent;
    array.array.push([$card.getAttribute("id"), $name, $cantidad, $precio]);
  });

  const envio = {
    url: "http://192.168.100.6/Global/scripts/compra.php",
    method: "POST",
    success: (userInfo) => {
      location.reload();
    },
    failed: () => alert("Usuario Incorrecto"),
    data: JSON.stringify({
      PK_Usuario: localStorage.getItem("PK_Type"),
      precioTotal: $d.querySelector("#precio-total").textContent,
      array,
    }),
  };

  fetchAsync(envio);
}

const getProducts = () => {
  const envio = {
    url: "http://192.168.100.6/Global/scripts/carrito.php",
    method: "POST",
    success: (userInfo) => {
      $d.querySelector("#numero-elementos").textContent =
        userInfo.producto.length;

      const comida = userInfo.producto.map((e) => e.precio * e.cantidad);

      let precioTotal = 0;
      for (let i = 0; i < comida.length; i++) {
        precioTotal += Number(comida[i]);
      }

      $d.querySelector("#precio-total").textContent = precioTotal;
      fillCards(userInfo.producto);
    },
    failed: () => alert("No tienes productos en el carrito"),
    data: JSON.stringify({
      FK_Usuario: localStorage.getItem("PK_Type"),
      FK_Producto: "",
      action: "0",
    }),
  };

  fetchAsync(envio);
};

const fillCards = (userInfo) => {
  const $cardSection = $d.querySelector(".section-product"),
    $cardTemplate = $d.getElementById("card-template").content,
    $fragment = $d.createDocumentFragment();

  console.log("Numero de elementos: " + userInfo.length);

  userInfo.forEach((element) => {
    $cardTemplate.querySelector("card").setAttribute("id", element.PK_Producto);
    $cardTemplate.querySelector("img").setAttribute("src", element.imagen);
    $cardTemplate.querySelector("h3").textContent = element.nombre;
    $cardTemplate.querySelector(".description").innerHTML =
      `<p>` + element.descripcion + `</p>`;
    $cardTemplate.querySelector("h4").textContent = element.username;
    $cardTemplate.querySelector("#categoria").textContent = element.categoria;
    $cardTemplate.querySelector("#precio").textContent =
      element.precio * element.cantidad;
    $cardTemplate.querySelector("#stock").textContent = element.stock;
    $cardTemplate.querySelector(".square-value").textContent = element.cantidad;

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
      const $aumentar = $card.querySelector("#aumentar"),
        $disminuir = $card.querySelector("#disminuir"),
        $eliminar = $card.querySelector("#eliminar"),
        $value = $card.querySelector(".square-value"),
        $stock = $card.querySelector("#stock");

      let cantidad = Number($value.textContent);
      const precio = Number($card.querySelector("#precio").textContent);

      let precioIndividual;
      if (cantidad == "0" || cantidad == "1") {
        precioIndividual = precio;
      }
      precioIndividual = precio / cantidad;
      let action = "";

      if ($aumentar === e.target) {
        if ($value.textContent === $stock.textContent) {
          alert("No puedes aumentar mas");
          return;
        }
        console.log("Aumentando");
        $value.textContent = Number($value.textContent) + 1;
        cantidad = Number($value.textContent);
        $card.querySelector("#precio").textContent =
          precioIndividual * cantidad;

        action = "1";
      }
      if ($disminuir === e.target) {
        if ($value.textContent === "1") {
          alert("No puedes disminuir mas");
          return;
        }
        console.log("Disminuyendo");
        $value.textContent = Number($value.textContent) - 1;
        cantidad = Number($value.textContent);
        $card.querySelector("#precio").textContent =
          precioIndividual * cantidad;
        action = "2";
      }
      if ($eliminar === e.target) {
        action = "3";
      }

      const envio = {
        url: "http://192.168.100.6/Global/scripts/carrito.php",
        method: "POST",
        success: (userInfo) => {
          const precioTotalAntiguo =
            $d.querySelector("#precio-total").textContent;
          let newPrecio = 0;

          if (action === "1")
            newPrecio = Number(precioTotalAntiguo) + Number(precioIndividual);
          if (action === "2")
            newPrecio = Number(precioTotalAntiguo) - Number(precioIndividual);
          if (action === "3") {
            const $precio = $d.querySelector("#precio-total");
            console.log($precio);
            $precio.textContent =
              Number($precio.textContent) -
              Number($card.querySelector("#precio").textContent);

            $d.querySelector("#numero-elementos").textContent =
              Number($d.querySelector("#numero-elementos").textContent) - 1;

            $d.querySelector(".section-product").removeChild($card);
            return;
          }

          $d.querySelector("#precio-total").textContent = newPrecio;
        },
        failed: () => alert("Usuario Incorrecto"),
        data: JSON.stringify({
          FK_Usuario: localStorage.getItem("PK_Type"),
          FK_Producto: $card.getAttribute("id"),
          action,
        }),
      };

      fetchAsync(envio);
    });
  });
};

$d.addEventListener("click", (e) => {
  clickListener(e);
});
