const imagenesCargadas = [];
const videosCargados = [];

$(document).ready(function () {

    //Comprobar que se tenga autorización para acceder a esta página
    Autorizar();

    //Cargar el listado de categorías disponibles
    cargarListadoCategorias();

    //Otros eventos
    $('#btnCargaImagen').change(function (event) { cargarImagenes(event); });
    $('#btnEliminarImagen').click(eliminarImagenActual);
    $('#btnCargaVideo').change(function (event) { cargarVideo(event); });
    $('#btnEliminarVideo').click(eliminarVideoActual);
    $('#btnCrearCategoria').click(crearNuevaCategoria);
    $('#btnEliminarCategoria').click(eliminarCategoria);
    $('input[name="opcionesModo"]').change(function () { gestionarCamposPrecioCantidad(); });
    $('#formNuevaPublicacion').submit(function (event) { validarFormulario(event); });

    // Llamar a la función para ajustar el footer al cargar la página
    ajustarFooter();
});

function Autorizar() {

    //Mandar una petición AJAX al middleware para saber si el usuario actual tiene autorización de acceder a la página dada
    $.ajax({
        url: 'Middleware/Middleware.php',
        type: 'GET',
        data: { pagina: 'CrearPublicacion' },
        success: function(response) {

            let respuesta = JSON.parse(response);

            //Si la respuesta es negativa, mostrar error de autorización
            if (!respuesta.exito) {
                let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
                for (let i = 0; i < respuesta.mensaje.length; i++) {
                    mensajeFinal += "● " + respuesta.mensaje[i] + "\n\n";
                }
                alert(mensajeFinal);

                //Redirigir a la página de inicio
                window.location.href = 'dashboard.html';
            }
        },
        //Error
        error: function() {
            alert("Ocurrió un error inesperado al comunicarse con el servidor.");
        }
    });
}

function cargarImagenes(event) {
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
    let imagenActual = $('#contenidoCarrusel .carousel-item.active');
    let imagenAnterior = imagenActual.prev();

    if (imagenAnterior.length === 0)
      imagenAnterior = imagenActual.next();

    imagenActual.remove();
    imagenAnterior.addClass('active');

    imagenesCargadas.splice($('#contenidoCarrusel .carousel-item').index(imagenActual), 1);

    //Llamar a la función para ajustar el footer después de eliminar imágenes
    ajustarFooter();
}

function cargarVideo(event) {
    let archivo = event.target.files[0];
    let urlVideo = URL.createObjectURL(archivo);
    let htmlVideo = $('<div class="carousel-item"><video class="d-block w-100" controls><source src="' + urlVideo + '" type="video/mp4"></video></div>');

    videosCargados.push(archivo);

    $('#contenidoCarruselVideo').append(htmlVideo);
    if ($('#contenidoCarruselVideo').children().length === 1)
        htmlVideo.addClass('active');

    $('#carruselVideo').carousel($('#contenidoCarruselVideo').children().length - 1);
    $('#btnCargaVideo').val('');

    // Llamar a la función para ajustar el footer después de cargar videos
    ajustarFooter();
}

function eliminarVideoActual() {
    let videoActual = $('#contenidoCarruselVideo .carousel-item.active');
    let videoAnterior = videoActual.prev('.carousel-item');

    if (videoAnterior.length === 0)
      videoAnterior = videoActual.next('.carousel-item');

    videoActual.remove();
    videoAnterior.addClass('active');

    videosCargados.splice($('#contenidoCarruselVideo .carousel-item').index(videoActual), 1);

    //Llamar a la función para ajustar el footer después de eliminar videos
    ajustarFooter();
}

function cargarListadoCategorias() {

    //Mandar una petición AJAX para obtener las categorías disponibles
    $.ajax({
        type: 'GET',
        url: "API/api.php/api/categorias/obtener",
        success: function(response) {

            let respuesta = JSON.parse(response);

            //Si se obtuvieron las categorías con éxito:
            if (respuesta.exito) {

                //Agregar los nombres de las categorías al listado de categorías
                for (let i = 0; i < respuesta.mensaje.length; i++) {
                    $('#ListadoCategorias').append('<option>' + respuesta.mensaje[i].nombre + '</option>');
                }
            }
            //Si hubo un error al obtener las categorías, mostrar mensaje de error
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

function crearNuevaCategoria() {

    let nuevaCategoria = $('#inputNuevaCategoria').val();
    let descripcionCategoria = $('#inputDescripcionCategoria').val();
    let mensajeError = "Se han detectado los siguientes errores:\n\n";

    //Validar que el campo de la nueva categoría no esté vacío
    if (nuevaCategoria.trim() === '') {
        mensajeError += "● El nombre de la nueva categoría no puede estar vacío\n\n";
    }

    //Validar que el campo de la descripción no esté vacío
    if (descripcionCategoria.trim() === '') {
        mensajeError += "● La descripción de la nueva categoría no puede estar vacía\n\n";
    }

    //Si se detectaron errores, mostrar el mensaje de error
    if (mensajeError !== "Se han detectado los siguientes errores:\n\n") {
        alert(mensajeError);
    }
    else {

        //Obtener la información de la nueva categoría
        let datosNuevaCategoria = new FormData();
        datosNuevaCategoria.append('nombreCategoria', nuevaCategoria);
        datosNuevaCategoria.append('descripcionCategoria', descripcionCategoria);

        //Mandar una petición AJAX para crear la nueva categoría
        $.ajax({
            type: 'POST',
            url: "API/api.php/api/categorias/creacion",
            data: datosNuevaCategoria,
            contentType: false,
            processData: false,
            success: function(response) {

                let respuesta = JSON.parse(response);

                //Si la categoría se creó con éxito, agregarla al select de categorías
                if (respuesta.exito) {
                    alert(respuesta.mensaje);
                    $('#ListadoCategorias').append('<option>' + nuevaCategoria + '</option>');
                    $('#inputNuevaCategoria').val('');
                    $('#inputDescripcionCategoria').val('');
                }
                //Si hubo un error al crear la categoría, mostrar el mensaje de error
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

function eliminarCategoria() {

    let errores = [];

    //Validar que se tenga una categoría seleccionada en el listado
    if ($('#ListadoCategorias').val() === '' || $('#ListadoCategorias').val() === null) {
        errores.push('No se ha seleccionado ninguna categoría para eliminar');
    }

    //Si se detectaron errores, mostrar el mensaje de error
    if (errores.length > 0) {
        let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
        for (let i = 0; i < errores.length; i++) {
            mensajeFinal += "● " + errores[i] + "\n\n";
        }
        alert(mensajeFinal);
    }
    else {

        //Mandar una petición AJAX para eliminar la categoría seleccionada
        $.ajax({
            type: 'DELETE',
            url: "API/api.php/api/categorias/eliminacion",
            data: JSON.stringify({ nombreCategoria: $('#ListadoCategorias').val() }),
            success: function(response) {

                let respuesta = JSON.parse(response);

                //Si la categoría se eliminó con éxito, quitarla del select de categorías y resetear el select
                if (respuesta.exito) {
                    alert(respuesta.mensaje);

                    //Quitar la categoría del listado de categorías
                    $('#ListadoCategorias option:selected').remove();
                    $('#ListadoCategorias').val('');
                }
                //Si hubo un error al eliminar la categoría, mostrar el mensaje de error
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

function gestionarCamposPrecioCantidad() {

    //Si se seleccionó la opción de cotización, deshabilitar los campos de precio y cantidad
    if ($('#opcionCotizacion').prop('checked')) {
        $('#campoPrecio, #campoCantidad').prop('disabled', true);
        $('#campoPrecio, #campoCantidad').val('');
        $('#campoPrecio').attr('placeholder', 'Sobre pedido');
        $('#campoCantidad').attr('placeholder', 'Sobre pedido');
    }
    //Si se eligió la opción de venta, habilitar los campos de precio y cantidad
    else {
        $('#campoPrecio, #campoCantidad').prop('disabled', false);
        $('#campoPrecio').attr('placeholder', 'Escribe un precio para el producto');
        $('#campoCantidad').attr('placeholder', 'Escribe el número de unidades disponibles');
    }
}

function ajustarFooter() {
    // Aquí va el código para ajustar el footer
}

function validarFormulario(event) {

    event.preventDefault();
    let mensajesError = [];

    //Validar que se hayan cargado al menos 3 imagenes
    if (imagenesCargadas.length < 3) {
        mensajesError.push("Se deben cargar al menos 3 imagenes para la publicación.");
    }

    //Validar que se haya cargado al menos un video
    if (videosCargados.length < 1) {
        mensajesError.push("Se debe cargar al menos un video para la publicación.");
    }

    //De lo contrario, si se está en el modo cotización establecer el precio y la cantidad en 0 automáticamente
    else {
        precio = 0;
        cantidad = 0;
    }

    //Si se detectaron errores, mostrarlos
    if (mensajesError.length > 0) {

        let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
        for (let i = 0; i < mensajesError.length; i++)
            mensajeFinal += "● " + mensajesError[i] + "\n\n";

        alert(mensajeFinal);
    }
    else {

        //Recopilar los datos del formulario
        let datosFormulario = new FormData();

        imagenesCargadas.forEach(function(imagen) {
            datosFormulario.append('imagenes[]', imagen);
        });

        datosFormulario.append('nombre', $('#campoNombre').val());
        datosFormulario.append('descripcion', $('#campoDescripcion').val());

        videosCargados.forEach(function(video) {
            datosFormulario.append('videos[]', video);
        });

        datosFormulario.append('categoria', $('#ListadoCategorias').val());
        datosFormulario.append('modo', $('#opcionVenta').prop('checked') ? 0 : 1);
        datosFormulario.append('precio', $('#campoPrecio').val());
        datosFormulario.append('cantidad', $('#campoCantidad').val());

        //Mandar una petición AJAX para crear la publicación
        $.ajax({
            type: 'POST',
            url: "API/api.php/api/productos/creacion",
            data: datosFormulario,
            contentType: false,
            processData: false,
            success: function(response) {

                let respuesta = JSON.parse(response);

                //Si la publicación se creó con éxito, mostrar mensaje de éxito y redirigir a la página principal
                if (respuesta.exito) {
                    alert(respuesta.mensaje);
                    window.location.href = 'dashboard.html';
                }
                //Si hubo un error al crear la publicación, mostrar mensaje de error
                else {
                    let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
                    for (let i = 0; i < respuesta.mensaje.length; i++) {
                        mensajeFinal += "● " + respuesta.mensaje[i] + "\n\n";
                    }
                    alert(mensajeFinal);

                    //Si ocurrieron errores pero se puede continuar, redirigir a la página principal
                    if (respuesta.continuar)
                        window.location.href = 'dashboard.html';
                }
            },
            //Error
            error: function() {
                alert("Ocurrió un error inesperado al comunicarse con el servidor.");
            }
        });
    }
}