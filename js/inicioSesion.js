//Evento de carga de la página
$(document).ready(function() {

    //Verificar el parámetro "accion" de la URL
    let parametrosURL = new URLSearchParams(window.location.search);
    if (parametrosURL.has('accion')) {

        //Accion 2: Cerrar sesión, 
        //se elimina el id del usuario del local storage para que ya no se inicie sesión en automático
        let accion = parametrosURL.get('accion');
        if (accion == 2)
            localStorage.removeItem("IdUsuario");
    }
    //Si no se encontró el parámetro:
    else {

        //Verificar si el id de algún usuario está almacenado en el local storage,
        //de ser así, iniciar sesión automáticamente
        let idEncontrado = localStorage.getItem("IdUsuario");
        if (idEncontrado != null)
            iniciarSesionAutomaticamente(idEncontrado);
    }
    
    //Configuración de eventos
    $('#formLogin').submit(function(event) { validarFormulario(event); });
});

//Función para validar el formulario
function validarFormulario(event) {

    event.preventDefault();

    //Recopilar los datos del formulario
    let datosFormulario = new FormData();
    datosFormulario.append('correo', $('#campoCorreo').val());
    datosFormulario.append('contrasena', $('#campoContrasena').val());

    //Acción 0: Iniciar sesión por credenciales (manualmente)
    datosFormulario.append('accion', 0);

    //Mandar una petición AJAX al servidor con los datos del formulario
    $.ajax({
        type: "POST",
        url: "https://pia-pwci-bf9e4b77cf71.herokuapp.com/API/api.php/api/usuarios/login",
        data: datosFormulario,
        contentType: false,
        processData: false,
        success: function(response) {

            let respuesta = JSON.parse(response);
    
            //Respuesta exitosa
            if (respuesta.exito) {

                //Si el checker de mantener la sesión está activado, guardar el id del usuario en el local storage
                if ($('#checkMantenerSesion').is(':checked'))
                    localStorage.setItem("IdUsuario", respuesta.mensaje);

                //Guardar el id del usuario en la sesión actual (este sirve para carritos)
                sessionStorage.setItem("IdUsuario", respuesta.mensaje);

                //Redirigir al dashboard
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

function iniciarSesionAutomaticamente(idUsuario) {

    //Recopilar los datos que se enviarán al servidor
    let datos = new FormData();
    datos.append('idUsuario', idUsuario);

    //Acción 1: Iniciar sesión por ID de usuario (automáticamente)
    datos.append('accion', 1);

    //Mandar una petición AJAX al servidor para iniciar la sesión automáticamente
    $.ajax({
        type: "POST",
        url: "https://pia-pwci-bf9e4b77cf71.herokuapp.com/API/api.php/api/usuarios/login",
        data: datos,
        contentType: false,
        processData: false,
        success: function(response) {

            let respuesta = JSON.parse(response);
    
            //Respuesta exitosa
            if (respuesta.exito) {

                //Guardar el id del usuario en la sesión actual (este sirve para carritos)
                sessionStorage.setItem("IdUsuario", respuesta.mensaje);

                //Redirigir al dashboard
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