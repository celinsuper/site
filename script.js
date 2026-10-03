function cerrarBurbuja(event) {
  event.stopPropagation(); // Evita abrir WhatsApp al dar clic en la 'X'
  let badge = document.getElementById("waBadge");
  if (badge) {
    badge.style.display = "none";
  }
}
/* inicia Sección Carrusel / Galería */
// Variable global para controlar el carrusel
let indiceCarrusel = 0;
let intervaloCarrusel = null;

function actualizarCarrusel() {
  const track = document.getElementById('carruselTrack');
  if (!track) return;
  
  // Desplaza el contenedor por porcentaje exacto (-0%, -100%, -200%, etc.)
  track.style.transform = `translateX(-${indiceCarrusel * 100}%)`;
}

function moverCarrusel(direccion) {
  const items = document.querySelectorAll('.carrusel-item');
  if (items.length === 0) return;

  indiceCarrusel += direccion;

  if (indiceCarrusel < 0) {
    indiceCarrusel = items.length - 1;
  } else if (indiceCarrusel >= items.length) {
    indiceCarrusel = 0;
  }

  actualizarCarrusel();
  reiniciarAutoPlay();
}

function iniciarAutoPlay() {
  pausarCarrusel(); // Evita múltiples temporizadores simultáneos
  intervaloCarrusel = setInterval(() => {
    moverCarrusel(1);
  }, 4000); // Cambia cada 4 segundos
}

function pausarCarrusel() {
  if (intervaloCarrusel) {
    clearInterval(intervaloCarrusel);
    intervaloCarrusel = null;
  }
}

function reiniciarAutoPlay() {
  pausarCarrusel();
  iniciarAutoPlay();
}

// Inicia el carrusel en cuanto carga el navegador
document.addEventListener('DOMContentLoaded', () => {
  actualizarCarrusel();
  iniciarAutoPlay();
});
/* inicia Sección Carrusel / Galería */