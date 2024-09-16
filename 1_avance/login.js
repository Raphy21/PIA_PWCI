$(document).ready(function() {
    $('#formLogin').submit(function(event) {
        event.preventDefault(); // Evita que el formulario se envíe automáticamente
        obtenerUsuariosYValidarFormulario();
    });
});

function obtenerUsuariosYValidarFormulario() {
    $.ajax({
        type: "GET",
        url: "1_avance/datosLogin.php", // Ruta al PHP que obtiene la lista de usuarios
        dataType: "json", // Especifica que esperas JSON
        success: function(response) {
            let usuariosExistentes = response;
            validarFormulario(usuariosExistentes); // Llama a la función de validación después de obtener los datos
        },
        error: function() {
            alert("Ocurrió un error al obtener la lista de usuarios.");
        }
    });
}

function validarFormulario(usuariosExistentes) {
    let email = document.getElementById('campoCorreo').value;
    let contrasena = document.getElementById('campoContrasena').value;

    // Verificar si el correo y la contraseña coinciden en la lista de usuarios existentes
    let coincidencia = usuariosExistentes.some(user => user.correo === email && user.pass === contrasena);

    if (coincidencia) {
        // Redirigir a otra página si se encuentra una coincidencia
        window.location.href = "dashboard.html";
    } else {
        // Vaciar los campos si no se encuentra coincidencia
        document.getElementById('campoCorreo').value = '';
        document.getElementById('campoContrasena').value = '';
        alert("No se encontraron coincidencias. Los campos han sido vaciados.");
    }
}
