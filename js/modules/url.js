const url = window.location.href; // Obtiene la URL completa
const urlObject = new URL(url); // Crea un objeto URL

export const host = urlObject.hostname;
