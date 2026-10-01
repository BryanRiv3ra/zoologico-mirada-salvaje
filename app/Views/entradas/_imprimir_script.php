<script>
/*
 * Imprime el recibo/ticket esperando a que terminen de cargar las imágenes
 * (los QR vienen de un servicio externo). Si alguna no carga, imprime igual
 * pasado 3 s para no bloquear al usuario.
 */
(function () {
  function imprimirConImagenes() {
    var imagenes = Array.prototype.slice.call(document.querySelectorAll('img'));
    var pendientes = imagenes.filter(function (img) { return !img.complete; });

    var yaImpreso = false;
    function imprimir() {
      if (yaImpreso) { return; }
      yaImpreso = true;
      window.print();
    }

    if (pendientes.length === 0) {
      imprimir();
      return;
    }

    var listas = 0;
    var temporizador = setTimeout(imprimir, 3000);

    function alTerminar() {
      listas += 1;
      if (listas >= pendientes.length) {
        clearTimeout(temporizador);
        imprimir();
      }
    }

    pendientes.forEach(function (img) {
      img.addEventListener('load', alTerminar);
      img.addEventListener('error', alTerminar);
    });
  }

  document.querySelectorAll('[data-imprimir]').forEach(function (boton) {
    boton.addEventListener('click', function (evento) {
      evento.preventDefault();
      imprimirConImagenes();
    });
  });
})();
</script>
