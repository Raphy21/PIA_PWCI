$(document).ready(function() {
    $('#formRegistro').submit(function(event) {
        event.preventDefault(); // Evita que el formulario se envíe automáticamente
        obtenerUsuariosYValidarFormulario();
    });
});

function obtenerUsuariosYValidarFormulario() {
    $.ajax({
        type: "GET",
        url: "1_avance/datosRegistroUE.php",
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
    let mensajesError = [];

    // Obtener los valores del formulario
    let nombreUsuario = document.getElementById('campoNombreUsuario').value;
    let email = document.getElementById('campoCorreo').value;
    let contrasena = document.getElementById('campoContrasena').value;

    // Validar que el nombre de usuario tenga al menos 3 caracteres
    if (nombreUsuario.length < 3)
        mensajesError.push("El nombre de usuario debe tener al menos 3 caracteres.");

    // Validar el formato de la contraseña mediante una expresión regular
    let expresionRegular = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!\"#\$%&/='?¡¿:;,.\\-_+*{\[\]}]).{8,}$/;
    if (!expresionRegular.test(contrasena))
        mensajesError.push("La contraseña no cumple con el formato de al menos 8 caracteres, una mayúscula, una minúscula, un número y un caracter especial.");

    // Validar si el nombre de usuario ya existe
    let usuarioExiste = usuariosExistentes.some(user => user.usuario === nombreUsuario);
    if (usuarioExiste) {
        mensajesError.push("El nombre de usuario ya está en uso.");
    }

    // Validar si el correo electrónico ya existe
    let correoExiste = usuariosExistentes.some(user => user.correo === email);
    if (correoExiste) {
        mensajesError.push("El correo electrónico ya está en uso.");
    }

    // Si se detectaron errores, mostrarlos
    if (mensajesError.length > 0) {
        let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
        for (let i = 0; i < mensajesError.length; i++)
            mensajeFinal += "● " + mensajesError[i] + "\n\n";
        alert(mensajeFinal);
    } 
    // Si no se detectaron errores, enviar el formulario
    else {
        // Recopilar los datos del formulario
        let datosFormulario = {
            usuario: $('#campoNombreUsuario').val(),
            correo: $('#campoCorreo').val(),
            contrasena: $('#campoContrasena').val()
        };

        // Enviar los datos mediante AJAX
        $.ajax({
            type: "POST",
            url: "1_avance/insertarUsuario.php", // Asegúrate de que la ruta sea correcta
            data: datosFormulario,
            dataType: "json",
            success: function(response) {
                if (response.exito) {
                    alert(response.mensaje);
                    // Aquí puedes limpiar el formulario o redirigir al usuario
                    window.location.href = "index.html";
                } else {
                    alert("Error: " + response.error);
                    window.location.href = "registro.html";
                }
            },
            error: function(xhr, status, error) {
                alert("Ocurrió un error al intentar registrar el usuario: " + error);
            }
        });
        
    }
}

