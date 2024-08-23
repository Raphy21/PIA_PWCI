const imagenesCargadas = [];

// Función para ajustar la posición del footer hacia abajo
function ajustarFooter() {
    var footer = document.getElementById('footer');
    var contenidoCarrusel = document.getElementById('contenidoCarrusel');
    // Verificar si hay imágenes cargadas
    if (contenidoCarrusel && contenidoCarrusel.children.length > 0) {
        // Ajustar la posición del footer hacia abajo
        footer.style.position = 'relative';
        footer.style.bottom = '0';
    } else {
        // Restablecer la posición del footer si no hay imágenes cargadas
        footer.style.position = '';
        footer.style.bottom = '';
    }
}

$(document).ready(function () {

    $('#btnCargaImagen').change(function (event) { cargarImagenesManualmente(event); });
    $('#btnEliminarImagen').click(eliminarImagenActual);
    $('#formLista').submit(function (event) { validarFormulario(event); });

    // Llamar a la función para ajustar el footer al cargar la página
    ajustarFooter();
});

function cargarImagenesManualmente(event) {
    let archivo = event.target.files[0];
    let urlImagen = URL.createObjectURL(archivo);
    let htmlImagen = $('<div class="carousel-item"><img class="d-block w-100" src="' + urlImagen + '" alt="Imagen del carrusel"></div>');

    imagenesCargadas.push(archivo);

    $('#contenidoCarrusel').append(htmlImagen);
    if ($('#contenidoCarrusel').children().length === 1)
        htmlImagen.addClass('active');

    $('#carrusel').carousel($('#contenidoCarrusel').children().length - 1);
    $('#btnCargaImagen').val('');

    // Llamar a la función para ajustar el footer después de cargar imágenes
    ajustarFooter();
}

function eliminarImagenActual() {
    let imagenActual = $('.carousel-item.active');
    let imagenAnterior = imagenActual.prev();

    if (imagenAnterior.length === 0)
        imagenAnterior = imagenActual.next();
    
    imagenActual.remove();
    imagenAnterior.addClass('active');

    imagenesCargadas.splice($('#contenidoCarrusel .carousel-item').index(imagenActual), 1);

    // Llamar a la función para ajustar el footer después de eliminar imágenes
    ajustarFooter();
}

function validarFormulario(event) {

    event.preventDefault();

    //Obtener los datos del formulario
    let nombreLista = $('#campoNombreLista').val();
    let descripcionLista = $('#campoDescripcionLista').val();

    let opcionVisibilidad = 0;
    if ($('#opcionPrivada').is(':checked'))
        opcionVisibilidad = 1;

    //Reunir los datos en un FormData
    let datosFormulario = new FormData();
    datosFormulario.append('nombre', nombreLista);
    datosFormulario.append('descripcion', descripcionLista);
    datosFormulario.append('visibilidad', opcionVisibilidad);

    //Agregar las imágenes al FormData
    imagenesCargadas.forEach(function(imagen) {
        datosFormulario.append('imagenes[]', imagen);
    });

    //Enviar los datos al servidor meidante AJAX
    $.ajax({
        type: 'POST',
        url: "API/api.php/api/listas/creacion",
        data: datosFormulario,
        contentType: false,
        processData: false,
        success: function (response) {
            
            let respuesta = JSON.parse(response);

            //Si la lista se creó con éxito
            if (respuesta.exito) {
                alert(respuesta.mensaje);
                window.location.href = "dashboard.html";
            }
            //Si hubo un error al crear la lista, mostrar mensaje de error
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
