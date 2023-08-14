import fetchAsync from "./asyncFetch.js";

const $d = document;
let statusType = "";

$d.addEventListener("DOMContentLoaded", (e) => {
  statusType = localStorage.getItem("userType");
  navController();

  getProducts();
});

const getProducts = () => {
  const envio = {
    url: "http://192.168.100.6/Global/scripts/dashboard.php",
    method: "POST",
    success: (userInfo) => {
      console.log(userInfo);
      console.log(userInfo.producto);

      fillCards(userInfo.producto);
    },
    failed: () => alert("Usuario Incorrecto"),
    data: null,
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
      const $deseos = $card.querySelector("#deseos");

      if ($carrito === e.target) {
        /*const Envio = {
          url: "http://192.168.100.6/Global/scripts/dashboard.php",
          method: "POST",
          success: (userInfo) => {
            console.log(userInfo);
            console.log(userInfo.producto);

            fillCards(userInfo.producto);
          },
          failed: () => alert("Usuario Incorrecto"),
          data: null,
        };*/
        return;
      }
      if ($deseos === e.target) {
        const Envio = {
          url: "http://192.168.100.6/Global/scripts/addDeseos.php",
          method: "POST",
          success: (userInfo) => {
            alert("Producto Agregado correctamente");
          },
          failed: () => alert("Usuario Incorrecto"),
          data: JSON.stringify({
            FK_Producto: $card.getAttribute("id"),
            FK_Usuario: localStorage.getItem("PK_Usuario"),
          }),
        };

        fetchAsync(Envio);
        return;
      }

      console.log(e.target);
      console.log($card.getAttribute("id"));
    });
  });
};

const navController = () => {
  const $liDeseos = $d.querySelector("#deseos");
  $liDeseos.addEventListener("click", (e) => {
    location.href = `http://192.168.100.6/global/deseos.html`;
  });

  const $nav = $d.querySelector(".header-nav"),
    $li = $d.createElement("li");
  $li.classList.add("nav-header");

  $li.textContent = statusType === null ? "Iniciar Sesión" : "Cerrar Sesión";

  $nav.insertAdjacentElement("beforeend", $li);
};

$d.addEventListener("click", (e) => {
  if (e.target.innerHTML === "Iniciar Sesión") {
    location.href = "http://192.168.100.6/global";
  }
  if (e.target.innerHTML === "Cerrar Sesión") {
    localStorage.clear();
    location.reload();
  }
});
