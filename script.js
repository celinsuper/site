function cerrarBurbuja(event) {
  event.stopPropagation(); // Evita abrir WhatsApp al dar clic en la 'X'
  let badge = document.getElementById("waBadge");
  if (badge) {
    badge.style.display = "none";
  }
}