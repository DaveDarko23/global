import fetchAsync from "./asyncFetch.js";
import navController, { clickListener } from "./navegacion.js";

const $d = document;
let statusImage = "No Actualizado";
let statusType = "";

$d.addEventListener("DOMContentLoaded", (e) => {
  statusType = localStorage.getItem("userType");

  if (statusType !== "Vendedor") {
    location.href = "http://192.168.100.6/global";
  }

  navController(statusType, $d);

  const envio = {
    url: "http://192.168.100.6/global/scripts/getProduct.php",
    method: "POST",
    success: (answer) => {
      const $nombre = $d.querySelector("[name=name]"),
        $descripcion = $d.querySelector("[name=descripcion]"),
        $precio = $d.querySelector("[name=precio]"),
        $stock = $d.querySelector("[name=stock]"),
        $categoria = $d.querySelector("[name=categoria]"),
        $file = $d.querySelector("[name=archivo]"),
        $imagen = $d.querySelector("img");

      $imagen.setAttribute("src", answer.imagen);

      $nombre.setAttribute("value", answer.nombre);
      $descripcion.setAttribute("value", answer.descripcion);
      $precio.setAttribute("value", answer.precio);
      $stock.setAttribute("value", answer.stock);
      $categoria.setAttribute("value", answer.categoria);
      console.log($file);

      $file.addEventListener("change", (e) => {
        statusImage = "Actualizado";
      });
    },
    failed: () => alert("Ocurrió un Accidente"),
    data: JSON.stringify({ FK_Producto: getParameterByName("id") }),
  };

  fetchAsync(envio);
});

function getParameterByName(name) {
  name = name.replace(/[\[]/, "\\[").replace(/[\]]/, "\\]");
  var regex = new RegExp("[\\?&]" + name + "=([^&#]*)"),
    results = regex.exec(location.search);
  return results === null
    ? ""
    : decodeURIComponent(results[1].replace(/\+/g, " "));
}

$d.addEventListener("submit", (e) => {
  e.preventDefault();

  const $fk = $d.querySelector("#fk_vendedor");
  $fk.setAttribute("value", getParameterByName("id"));
  console.log($fk);

  const formData = new FormData(e.target);
  if (statusImage === "No Actualizado") {
    const $imagen = $d.querySelector("img");

    formData.append("oldImage", $imagen.getAttribute("src"));
  }

  const envio = {
    url: "http://192.168.100.6/global/scripts/editProduct.php",
    method: "POST",
    success: (answer) => {
      console.log(answer);
      if (answer === 200) {
        location.href = "http://192.168.100.6/global";
      }
    },
    failed: () => alert("Ocurrió un Accidente"),
    data: formData,
  };

  fetchAsync(envio);
});

$d.addEventListener("click", (e) => {
  clickListener(e);
});
