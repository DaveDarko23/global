let $d;

const addLi = ($nav, text) => {
  const $li = $d.createElement("li");
  $li.textContent = text;
  $nav.insertAdjacentElement("afterbegin", $li);
};

const navController = (statusType, $doc) => {
  $d = $doc;
  const $nav = $doc.querySelector(".header-nav");
  if (statusType === null) {
    addLi($nav, "Iniciar Sesión");
  }

  if (statusType === "Vendedor") {
    addLi($nav, "Cerrar Sesión");
    addLi($nav, "Compras");
    addLi($nav, "Agregar Producto");
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

export const clickListener = (e) => {
  const url = "http://10.0.0.3/global/";
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
    case "Agregar Producto":
      location.href = url + "new-product.html";
      break;
    case "Carrito":
      location.href = url + "carrito.html";
      break;
    case "Deseos":
      location.href = url + "deseos.html";
      break;
    case "Home":
      location.href = url;
      break;
  }
};

export default navController;
