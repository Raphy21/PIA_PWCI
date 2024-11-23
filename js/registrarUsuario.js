//Evento de carga de la página
$(document).ready(function() {
    
    //Configuración de otros eventos
    $('#foto').change(function(event) { mostrarFoto(event); });
    $('input[name="opcionesVisibilidad"]').change(function () { gestionarRadioButtons(); });
    $('#formRegistro').submit(function(event) { validarFormulario(event); });
});

//Función para mostrar la foto seleccionada por el usuario
function mostrarFoto(event) {

    //Obtener referencias a los elementos necesarios para mostrar la imagen
    let elementoImg = document.getElementById('imagenSeleccionada');
    let archivoSeleccionado = event.target;

    //Si se seleccionó un archivo de imagen:
    if (archivoSeleccionado.files && archivoSeleccionado.files[0]) {

        //Leer la imagen seleccionada y mostrarla en el elemento img
        let lector = new FileReader();
        lector.onload = function (e) {
            elementoImg.src = e.target.result;
            elementoImg.style.display = 'block';
        };
        lector.readAsDataURL(archivoSeleccionado.files[0]);
    }
}

//Función para gestionar los radio buttons
function gestionarRadioButtons() {

    //Si se seleccionó la opción de ser un usuario privado, deshabilitar los radio buttons de Vendedor y Administrador
    if ($('#opcionPrivada').prop('checked')) {
        $('#opcionVendedor, #opcionAdministrador').prop('disabled', true).prop('checked', false);
        $('#opcionCliente').prop('checked', true);
    } 
    //Si se eligió ser un usuario público, habilitar los radio buttons de Vendedor y Administrador
    else {
        $('#opcionVendedor, #opcionAdministrador').prop('disabled', false);
    }
}

//Función para validar el formulario
function validarFormulario(event) {

    event.preventDefault();
    let mensajesError = [];

    //Validar que el nombre de usuario tenga al menos 3 caracteres
    let nombreUsuario = document.getElementById('campoNombreUsuario').value;
    if (nombreUsuario.length < 3)
        mensajesError.push("El nombre de usuario debe tener al menos 3 caracteres.");

    //Validar el formato de la contraseña mediante una expresión regular
    if(document.getElementById('campoContrasena1').value === document.getElementById('campoContrasena').value){
        let contrasena = document.getElementById('campoContrasena').value;
        let expresionRegular = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!\"#\$%&/='?¡¿:;,.\\-_+*{\[\]}]).{8,}$/;
        if (!expresionRegular.test(contrasena))
            mensajesError.push("La contraseña no cumple con el formato de al menos 8 caracteres, una mayúscula, una minúscula, un número y un caracter especial.");
    }else{
        mensajesError.push("Las contraseñas no coinciden");
    }
    //Si se detectaron errores, mostrarlos
    if (mensajesError.length > 0) {

        let mensajeFinal = "Se han detectado los siguientes errores:\n\n";
        for (let i = 0; i < mensajesError.length; i++)
            mensajeFinal += "● " + mensajesError[i] + "\n\n";

        alert(mensajeFinal);
    }
    //Si no se detectaron errores:
    else {

        //Formatear el campo de sexo a un int (0 = Masculino, 1 = Femenino)
        let opcionSexo = 0;
        if ($('#campoSexo').val() === 'Femenino')
            opcionSexo = 1;

        //Formatear las opciones de visibilidad a un int (0 = Público, 1 = Privado)
        let opcionVisibilidad = 0;
        if ($('#opcionPrivada').prop('checked'))
            opcionVisibilidad = 1;

        //Formatear las opciones de rol a un int (0 = Vendedor, 1 = Cliente, 2 = Administrador)
        let opcionRol = 0;
        if ($('#opcionCliente').prop('checked'))
            opcionRol = 1;
        else if ($('#opcionAdministrador').prop('checked'))
            opcionRol = 2;

        //Recopilar los datos del formulario
        let datosFormulario = new FormData();
        datosFormulario.append('imagenPerfil', $('#foto')[0].files[0]);
        datosFormulario.append('nombre', $('#campoNombre').val());
        datosFormulario.append('apellidoPat', $('#campoApePat').val());
        datosFormulario.append('apellidoMat', $('#campoApeMat').val());
        datosFormulario.append('sexo', opcionSexo);
        datosFormulario.append('fechaNacimiento', $('#campoFechaNacimiento').val());
        datosFormulario.append('visibilidad', opcionVisibilidad);
        datosFormulario.append('rol', opcionRol);
        datosFormulario.append('email', $('#campoCorreo').val());
        datosFormulario.append('usuario', $('#campoNombreUsuario').val());
        datosFormulario.append('contrasena', $('#campoContrasena').val());

        //Mandar una petición AJAX al servidor con los datos del formulario
        $.ajax({
            type: "POST",
            url: "https://pia-pwci.herokuapp.com/API/api.php/usuarios/registro",
            data: datosFormulario,
            contentType: false,
            processData: false,
            success: function(response) {

                let respuesta = JSON.parse(response);
        
                //Respuesta exitosa
                if (respuesta.exito) {
                    alert(respuesta.mensaje);
                    window.location.href = "index.html";
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
