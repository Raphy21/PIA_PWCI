//Evento de carga de la página
$(document).ready(function() {    

    cargarCategorias();
    //Configuración de otros eventos    
    $('#formularioBusqueda').submit(function(event) { validarFormularioBusqueda(event); });
});

function cargarCategorias() {
    //Mandar una petición AJAX para obtener las categorías disponibles
    $.ajax({
        type: 'GET',
        url: "API/api.php/api/categorias/obtener",
        success: function(response) {

            let respuesta = JSON.parse(response);

            //Si se obtuvieron las categorías con éxito:
            if (respuesta.exito) {

                //Agregar los nombres de las categorías al select de categorías
                for (let i = 0; i < respuesta.mensaje.length; i++) {
                    $('#campoCategoria').append('<option>' + respuesta.mensaje[i].nombre + '</option>');
                    $('#categoria').append('<option>' + respuesta.mensaje[i].nombre + '</option>');
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

function validarFormularioBusqueda(event) {

    event.preventDefault();

    //Recopilar los datos del formulario
    let datosFormulario = new FormData();
    datosFormulario.append('busqueda', $('#inputBuscador').val());
    datosFormulario.append('precioMin', $('#precioMin').val());
    datosFormulario.append('precioMax', $('#precioMax').val());
    datosFormulario.append('categoria', $('#campoCategoria').val());
    datosFormulario.append('estrellas', $('#campoEstrellas').val());
    datosFormulario.append('popular', $('#campoPopular').val());

    //Mandar una petición AJAX al servidor con los datos del formulario
    $.ajax({
        type: "POST",
        url: "API/api.php/api/busqueda/buscar",
        data: datosFormulario,
        contentType: false,
        processData: false,
        success: function(response) {

            let respuesta = JSON.parse(response);

            let numeroDeFilas = respuesta.mensaje.length;

            let respuestasArray = [];
    
            //Respuesta exitosa
            if (respuesta.exito) {

                if (Array.isArray(respuesta.mensaje)) {
                    respuesta.mensaje.forEach(mensaje => {

                        respuestasArray.push({
                            id: mensaje.Id,
                            idVendedor: mensaje.idVendedor,
                            nombre: mensaje.nombreProducto,
                            descripcion: mensaje.descripcionProducto,
                            modo: mensaje.modo,
                            precio: mensaje.precio,
                            existencia: mensaje.existencia,
                            calificacion: mensaje.calificacion,
                            aprobado: mensaje.aprobado,
                            IdAdminAprobador: mensaje.IdAdminAprobador,
                            categoria: mensaje.categoria,
                            imagen: mensaje.imagenesProducto,
                        });
                    });
                }

                localStorage.setItem("respuestasArray", JSON.stringify(respuestasArray));
                console.log(respuesta);
                //Redirigir al dashboard
                window.location.href = "busquedas.html";
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