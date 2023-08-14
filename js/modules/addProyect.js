import fetchAsync from "./asyncFetch.js";

const $d = document;

$d.addEventListener("DOMContentLoaded", (e) => {});

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
        location.href = "http://192.168.100.6/global/dashboard.html";
      }
    },
    failed: () => alert("Ocurrió un Accidente"),
    data: new FormData(e.target),
  };

  fetchAsync(envio);
});
