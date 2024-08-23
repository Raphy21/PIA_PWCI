//Evento de carga de la página
$(document).ready(function() {
    
    //Configuración de otros eventos
    $('#formularioBusqueda').submit(function(event) { validarFormularioBusqueda(event); });
});

//Función para validar el formulario
function validarFormularioBusqueda(event) {

    event.preventDefault();
    let mensajesError = [];
    let expresionRegularPrecio = /^\d+(\.\d{1,2})?$/;

    //Obtener el texto de búsqueda
    let textoBusqueda = document.getElementById('inputBuscador').value;

    //Validar el campo de precio mínimo
    let precioMin = document.getElementById('precioMin').value;
    if (precioMin != "") {
        if (!expresionRegularPrecio.test(precioMin) || parseFloat(precioMin) < 0) {
            mensajesError.push('Ingresa un valor válido para el precio mínimo');
        }
    }

    //Validar el campo de precio máximo
    let precioMax = document.getElementById('precioMax').value;
    if (precioMax != "") {
        if (!expresionRegularPrecio.test(precioMax) || parseFloat(precioMax) < 0) {
            mensajesError.push('Ingresa un valor válido para el precio máximo');
        }
    }

    //Validar que el precio máximo sea mayor que el precio mínimo
    if (precioMin != "" && precioMax != "") {
        if (parseFloat(precioMax) < parseFloat(precioMin)) {
            mensajesError.push('El precio máximo debe ser mayor que el precio mínimo');
        }
    }

    // Si se detectaron errores, mostrarlos
    if (mensajesError.length > 0) {
        let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
        for (let i = 0; i < mensajesError.length; i++)
            mensajeFinal += "● " + mensajesError[i] + "\n\n";
        alert(mensajeFinal);
    }
    // Si no se detectaron errores:
    else {
        // Redirigir al usuario a la página de resultados de búsqueda
        //window.location.href = "busquedas.html";
    }
}
