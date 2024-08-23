var IdProductoActual = -1;
const imagenesCargadas = [];
const videosCargados = [];
const comentarios = [];
const sinComentarios = document.getElementById('no-results');
var rolUsuarioActual = -1;

//Evento de carga de la página
$(document).ready(function() {

  //Boton de agregar a lista deshabilitado por defecto
  $('#btnAgregarLista').prop('disabled', true);
    
  //Configuración de otros eventos
  $('#btnCotizacion').click(function(event) { irACotizacion(); });
  $('#formularioCarrito').submit(function(event) { agregarCarrito(event); });
  $('#opcionesListas').change(function(event) { gestionarBotonAgregarLista(); });
  $('#btnAgregarLista').click(function(event) { agregarALista(); });
  $('#commentForm').submit(function(event) { publicarComentario(event); });

  //Obtener el rol del usuario actual
  obtenerRolUsuario();

  //Cargar la información del producto
  IdProductoActual = JSON.parse(localStorage.getItem('productoId'));
  cargarInfoArticulo(IdProductoActual);

  //Obtener las listas del usuario actual
  obtenerListasUsuario();

  //Si no hay comentarios, mostrar mensaje
  if (comentarios.length == 0) {
    mostrarMensajeSinComentarios();
  }
});

function irACotizacion() {
  localStorage.setItem('productoId', IdProductoActual);
  window.location.href = "cotizacion.html";
}

function cargarImagenEnCarrusel(imagen) {
  // Crear el HTML de la imagen para el carrusel
  let htmlImagen = $('<div class="carousel-item"><img class="d-block w-100" src="data:image/png;base64,' + imagen + '" alt="Imagen del carrusel"></div>');

  // Añadir la imagen cargada a la lista (esto puede variar según el uso específico de la lista de imágenes cargadas)
  imagenesCargadas.push(imagen);

  // Añadir el HTML de la imagen al contenedor del carrusel
  $('#contenidoCarrusel').append(htmlImagen);
  if ($('#contenidoCarrusel').children().length === 1)
      htmlImagen.addClass('active');
}

//Función para agregar un producto a una lista
function agregarALista() {

  //Recopilar los datos del formulario
  let datosFormulario = new FormData();
  datosFormulario.append('idLista', $('#opcionesListas').val());
  datosFormulario.append('idProducto', IdProductoActual);

  //Mandar una petición AJAX al servidor con los datos del formulario
  $.ajax({
      type: "POST",
      url: "API/api.php/api/listas/agregar",
      data: datosFormulario,
      contentType: false,
      processData: false,
      success: function(response) {

          let respuesta = JSON.parse(response);

          //Respuesta exitosa
          if (respuesta.exito) {
              alert(respuesta.mensaje);
          }
          //Respuesta negativa
          else { 
              let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
              for (let i = 0; i < respuesta.mensaje.length; i++) {
                  mensajeFinal += "● " + respuesta.mensaje[i] + "\n\n";
              }
              alert(mensajeFinal);
          }
      },
      //Error
      error: function() {
          alert("Ocurrió un error inesperado al comunicarse con el servidor.");
      }
  });
}

//Función para validar el formulario
function agregarCarrito(event) {

  event.preventDefault();
  let mensajesError = [];

  //Validar que la cantidad no sea 0
  if ($('#campoCantidad').val() == 0)
      mensajesError.push("La cantidad no puede ser 0");                                     

  //Validar que la cantidad no sea negativa
  if ($('#campoCantidad').val() < 0)
      mensajesError.push("La cantidad no puede ser negativa");

  //Validar que la cantidad sea un número
  if (isNaN($('#campoCantidad').val()))
      mensajesError.push("La cantidad debe ser un número");

  //Validar que la cantidad sea un número entero
  else if (!Number.isInteger(parseFloat($('#campoCantidad').val())))
      mensajesError.push("La cantidad debe ser un número entero");

  //Validar que aun haya existencias suficientes para la cantidad deseada
  if (parseInt($('#campoCantidad').val()) > parseInt($('#campoDisponibilidad').val()))
      mensajesError.push("No hay suficientes existencias para la cantidad deseada");

  //Si se detectaron errores, mostrarlos
  if(mensajesError.length > 0) {

      let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
      for (let i = 0; i < mensajesError.length; i++)
          mensajeFinal += "● " + mensajesError[i] + "\n\n";

      alert(mensajeFinal);
  }
  else {
  
    //Recopilar los datos del formulario
    let datosFormulario = new FormData();
    datosFormulario.append('idPropietario', sessionStorage.getItem("IdUsuario"));
    datosFormulario.append('terminado', 0);
    datosFormulario.append('idProducto', localStorage.getItem("Id"));
    datosFormulario.append('Cantidad', $('#campoCantidad').val());  

    //Mandar una petición AJAX al servidor con los datos del formulario
    $.ajax({
        type: "POST",
        url: "API/api.php/api/carrito/agregar",
        data: datosFormulario,
        contentType: false,
        processData: false,
        success: function(response) {

            let respuesta = JSON.parse(response);
    
            //Respuesta exitosa
            if (respuesta.exito) {
                alert(respuesta.mensaje);
                window.location.href = "dashboard.html";
            }
            //Respuesta negativa
            else { 
                let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
                for (let i = 0; i < respuesta.mensaje.length; i++) {
                    mensajeFinal += "● " + respuesta.mensaje[i] + "\n\n";
                }
                alert(mensajeFinal);
            }
        },
        //Error
        error: function() {
            alert("Ocurrió un error inesperado al comunicarse con el servidor.");
        }
    });  
  }
}

function cargarVideoEnCarrusel(video) {
  // Crear el HTML del video para el carrusel
  let htmlVideo = $('<div class="carousel-item"><video class="d-block w-100" controls><source src="data:video/mp4;base64,' + video + '" type="video/mp4">Tu navegador no soporta la etiqueta de video.</video></div>');

  // Añadir el video cargado a la lista (esto puede variar según el uso específico de la lista de videos cargados)
  videosCargados.push(video);

  // Añadir el HTML del video al contenedor del carrusel
  $('#contenidoCarruselVideo').append(htmlVideo);
  if ($('#contenidoCarruselVideo').children().length === 1)
      htmlVideo.addClass('active');
}

//Función para obtener el rol del usuario actual
function obtenerRolUsuario() {
  
    //Mandar una petición AJAX al servidor para obtener el rol del usuario
    $.ajax({
        type: "GET",
        url: "API/api.php/api/usuarios/perfil",
        success: function(response) {

            let respuesta = JSON.parse(response);

            //Respuesta exitosa
            if (respuesta.exito) {
                rolUsuarioActual = respuesta.mensaje.rol;
            }
            //Respuesta negativa
            else { 
                let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
                for (let i = 0; i < respuesta.mensaje.length; i++) {
                    mensajeFinal += "● " + respuesta.mensaje[i] + "\n\n";
                }
                alert(mensajeFinal);
            }
        },
        //Error
        error: function() {
            alert("Ocurrió un error inesperado al comunicarse con el servidor.");
        }
    });
}

// Función para redirigir y llamar a otra función con el ID del producto
function cargarInfoArticulo(productoId) {
  
  // Llama a tu función con el ID del producto (por ejemplo, para registro o análisis)
  console.log(`ID del producto: ${productoId}`);

  let datosFormulario = new FormData();
  datosFormulario.append('busqueda', productoId);
  
  $.ajax({
      type: "POST",
      url: "API/api.php/api/busqueda/buscarId",
      data: datosFormulario,
      contentType: false,
      processData: false,
      success: function(response) {

          let respuesta = JSON.parse(response);

          //Respuesta exitosa
          if (respuesta.exito) {

            console.log(respuesta.mensaje.Id);
            console.log(respuesta.mensaje.IdVendedor);
            $('#campoNombre').val(respuesta.mensaje.Nombre);
            $('#campoDescripcion').val(respuesta.mensaje.Descripcion);
            $('#campoCategorizacion').val(respuesta.mensaje.Categoria);
            $('#campoModo').val(respuesta.mensaje.Modo == 0 ? 'Venta' : 'Cotizacion');
            $('#campoPrecio').val(respuesta.mensaje.Precio);
            $('#campoDisponibilidad').val(respuesta.mensaje.Existencia);
            $('#campoCalificacion').val(parseFloat(respuesta.mensaje.Calificacion).toFixed(1));
            localStorage.setItem('Id', respuesta.mensaje.Id);
            localStorage.setItem('IdVendedor', respuesta.mensaje.IdVendedor);
            localStorage.setItem('NombreArticulo', respuesta.mensaje.Nombre);
            localStorage.setItem('DescripcionArticulo', respuesta.mensaje.Descripcion);
            localStorage.setItem('esCliente', "1");

            //Cargar imagenes en el carrusel
            for (let i = 0; i < respuesta.mensaje.Imagenes.length; i++) {
              cargarImagenEnCarrusel(respuesta.mensaje.Imagenes[i]);
            }

            //Cargar videos en el carrusel
            for (let i = 0; i < respuesta.mensaje.Videos.length; i++) {
              cargarVideoEnCarrusel(respuesta.mensaje.Videos[i]);
            }

            //Cargar los comentarios del producto
            for (let i = 0; i < respuesta.mensaje.Comentarios.length; i++) {
              mostrarComentario(false, respuesta.mensaje.Comentarios[i].Titulo, respuesta.mensaje.Comentarios[i].Contenido, respuesta.mensaje.Comentarios[i].Puntuacion, respuesta.mensaje.Comentarios[i].NombreUsuario, respuesta.mensaje.Comentarios[i].FechaCreacion);
            }

            //Si el modo es cotización, deshabilitar el input de cantidad y el botón de carrito
            if (respuesta.mensaje.Modo == 1) {
              $('#campoCantidad').prop('disabled', true);
              $('#btnCarrito').prop('disabled', true);
            }
            //De lo contrario, deshabilitar el botón de cotización
            else {
              $('#btnCotizacion').prop('disabled', true);
            }

            //Si el rol del usuario no es cliente, deshabilitar toda la sección de interacción con el producto
            if (rolUsuarioActual != 1) {
              $('#btnCotizacion').prop('disabled', true);
              $('#campoCantidad').prop('disabled', true);
              $('#btnCarrito').prop('disabled', true);
              $('#opcionesListas').prop('disabled', true);
              $('#btnAgregarLista').prop('disabled', true);
            }
          }
          //Respuesta negativa
          else { 
            let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
            for (let i = 0; i < respuesta.mensaje.length; i++) {
                mensajeFinal += "● " + respuesta.mensaje[i] + "\n\n";
            }
            alert(mensajeFinal);
        }    
      },
      //Error
      error: function() {
        alert("Ocurrió un error inesperado al comunicarse con el servidor.");
      }
  });
}

//Función para obtener las listas del usuario actual
function obtenerListasUsuario() {

  //Mandar una petición AJAX al servidor para obtener las listas del usuario
  $.ajax({
      type: "GET",
      url: "API/api.php/api/usuario/listas",
      success: function(response) {
          
            let respuesta = JSON.parse(response);
  
            //Respuesta exitosa
            if (respuesta.exito) {
  
              //Agregar las listas al dropdown
              for (let i = 0; i < respuesta.mensaje.length; i++) {
                $('#opcionesListas').append(`<option value="${respuesta.mensaje[i].Id}">${respuesta.mensaje[i].Nombre}</option>`);
              }
            }
            //Respuesta negativa
            else { 
              let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
              for (let i = 0; i < respuesta.mensaje.length; i++) {
                  mensajeFinal += "● " + respuesta.mensaje[i] + "\n\n";
              }
              alert(mensajeFinal);
          }    
      },
      //Error
      error: function() {
        alert("Ocurrió un error inesperado al comunicarse con el servidor.");
      }
  });
}

function gestionarBotonAgregarLista() {

  //Si se seleccionó una lista valida, habilitar el botón de agregar a lista
  if ($('#opcionesListas').prop('selectedIndex') > 0) {
      $('#btnAgregarLista').prop('disabled', false);
  } 
  //De lo contrario, deshabilitar el botón de agregar a lista
  else {
      $('#btnAgregarLista').prop('disabled', true);
  }
}

//Sistema visual de las estrellas
document.querySelectorAll('.rating i').forEach((star, index) => {
    let rating = parseInt(star.getAttribute('data-rating'));
    star.addEventListener('click', function() {
        document.getElementById('commentRatingInput').value = rating;
        
        // Cambia el color de las estrellas según la calificación seleccionada
        document.querySelectorAll('.rating i').forEach((s, i) => {
            if (i >= index) {
                s.style.color = "#ffcc00"; // Estrellas seleccionadas en color amarillo
            } else {
                s.style.color = "#e2dfd6"; // Estrellas no seleccionadas en color gris
            }
        });
    });
});

// Envía el formulario de comentario
function publicarComentario(event) {

    event.preventDefault();
    let mensajesError = [];

    let titulo = document.getElementById('commentTitle').value;
    let contenido = document.getElementById('commentContent').value;
    let puntuacion = document.getElementById('commentRatingInput').value;

    //Validar que se haya elegido una puntuación
    if (puntuacion == 0)
        mensajesError.push("No se ha seleccionado una puntuación.");

    // Si se detectaron errores, mostrarlos
    if(mensajesError.length > 0) {
        let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
        for (let i = 0; i < mensajesError.length; i++)
            mensajeFinal += "● " + mensajesError[i] + "\n\n";
        alert(mensajeFinal);
        return;
    }
    else {

      //Recopilar los datos del formulario
      let datosFormulario = new FormData();
      datosFormulario.append('idProducto', IdProductoActual);
      datosFormulario.append('titulo', titulo);
      datosFormulario.append('contenido', contenido);
      datosFormulario.append('puntuacion', puntuacion);

      //Enviar una petición AJAX al servidor para guardar el comentario en la base de datos
      $.ajax({
          type: "POST",
          url: "API/api.php/api/comentarios/creacion",
          data: datosFormulario,
          contentType: false,
          processData: false,
          success: function(response) {

              let respuesta = JSON.parse(response);

              //Respuesta exitosa
              if (respuesta.exito) {
                mostrarComentario(true, titulo, contenido, puntuacion, respuesta.mensaje.NombreUsuario, respuesta.mensaje.FechaCreacion);
              }
              //Respuesta negativa
              else { 
                let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
                for (let i = 0; i < respuesta.mensaje.length; i++) {
                    mensajeFinal += "● " + respuesta.mensaje[i] + "\n\n";
                }
                alert(mensajeFinal);
            }    
          },
          //Error
          error: function() {
            alert("Ocurrió un error inesperado al comunicarse con el servidor.");
          }
      });
    } 
}

function mostrarComentario(nuevo, titulo, contenido, puntuacion, autor, fecha) {
  
  //Crear un nuevo comentario y mostrarlo en el área de comentarios
  let comentario = document.createElement('div');
  comentario.innerHTML = `
    <div class="card mb-3">
      <div class="card-body">
        <h4 class="card-title"><b>${titulo}</b></h4>
        <p class="card-text">${contenido}</p>
        <p class="card-text"><strong>Puntuación:</strong> ${puntuacion} estrella${puntuacion == 1 ? '' : 's'}</p>
        <p class="card-text"><strong>Escrito por:</strong> ${autor}</p>
        <p class="card-text"><strong>Fecha:</strong> ${fecha}</p>
      </div>
    </div>
  `;
  document.getElementById('commentsArea').appendChild(comentario);
  comentarios.push(comentario);

  //Si es un comentario nuevo:
  if (nuevo) {

    //Limpiar los campos del formulario
    document.getElementById('commentTitle').value = '';
    document.getElementById('commentContent').value = '';
    document.getElementById('commentRatingInput').value = '0';

    //Bajar el scroll hasta el nuevo comentario
    document.getElementById('commentsArea').scrollIntoView({ behavior: 'smooth' });

    // Reinicia el resaltado de las estrellas
    document.querySelectorAll('.rating i').forEach(star => {
        star.style.color = "#e2dfd6"; // Establece todas las estrellas en color gris
    });
  }
  
  //Ocultar mensaje de sin comentarios
  sinComentarios.style.display = 'none';
}

function mostrarMensajeSinComentarios() {
  sinComentarios.style.display = 'block';
}
