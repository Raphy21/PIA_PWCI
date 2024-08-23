var productosDeLaLista = [];
let contenedorProductos = $('#card-container')[0];
let mensajeSinProductos = document.getElementById('no-results');
let mensajeSinListas = document.getElementById('no-lists');
let productosPorPagina = 2;
let paginaActual = 0;
var listas = [];

//Evento de carga de la página
$(document).ready(function() {
    
    //Configuración de otros eventos
    $('#toggleEditar').change(function() { mostrarOcultarCampos($('#toggleEditar').prop('checked')); });
    mostrarOcultarCampos($('#toggleEditar').prop('checked'));
    $('#foto').change(function(event) { mostrarFoto(event); });
    $('input[name="opcionesVisibilidad"]').change(function () { gestionarRadioButtons(); });
    $('#formRegistro').submit(function(event) { validarFormulario(event); });

    $('#btnNuevaLista').click(function() { window.location.href = "crearLista.html"; } );
    $('.btnEditarLista').click(function() { window.location.href = "crearLista.html"; } );
    $('.btnEliminarLista').click(function() { alert("Eliminando la lista...") } );

    $('#btnAtras').click(function() { paginaProductosAnterior(); });
    $('#btnSiguiente').click(function() { paginaProductosSiguiente(); });

    //Obtener la información del usuario automáticamente al cargar la página
    obtenerInfoUsuario();
});

//Función para mostrar/ocultar los campos editables del formulario
function mostrarOcultarCampos(estadoToggle) {
    
    //Si el toggle está activado, mostrar el contenedor con los campos editables
    if (estadoToggle) {
        $('#foto').show();
        $('#contenedorDescripcion').hide();
        $('#contenedorCampos').show();

        //Si se es usuario de tipo cliente, mostrar las listas editables
        if ($('#opcionCliente').prop('checked')) {
            $('#idDivListas').show();
            $('#tablaListasMostrable').hide();
            $('#tablaListasEditable').show();
            $('#btnNuevaLista').show();
            mostrarTablaEditableListas();
        }
    } 
    //Si el toggle está desactivado, ocultar el contenedor y solo mostrar la descripción del perfil
    else {
        $('#foto').hide();
        $('#contenedorDescripcion').show();
        $('#contenedorCampos').hide();

        //Si la visibilidad es publica:
        if ($('#opcionPublica').prop('checked')) {

            //Si se es usuario de tipo cliente, mostrar las listas no editables
            if ($('#opcionCliente').prop('checked')) {
                $('#idDivListas').show();
                $('#tablaListasMostrable').show();
                $('#tablaListasEditable').hide();
                $('#btnNuevaLista').hide();
                mostrarTablaMostrableListas();
            }
            //De lo contrario no mostrar nada relacionado a las listas
            else
                $('#idDivListas').hide();
        }
        //Si la visibilidad es privada, no mostrar nada relacionado a las listas
        else {
            $('#idDivListas').hide();
        }
    }       
}

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

//Funciones para mostrar los productos (o un mensaje si no hay productos)
function mostrarProductos(startIndex) {
    contenedorProductos.innerHTML = '';
    for (let i = startIndex; i < Math.min(startIndex + productosPorPagina, productosDeLaLista.length); i++) {
        const { Id, Nombre, Descripcion, Imagenes } = productosDeLaLista[i];
        const card = document.createElement('div');
        card.classList.add('card');
        card.setAttribute('data-id', Id);
        card.innerHTML = `
            <img src="data:image/png;base64,${Imagenes[0]}" alt="${Nombre}" style="width: 100%; height: 100%;">
            <h2>${Nombre}</h2>
            <p>${Descripcion}</p>
            <button id="enlaceProducto" href="paginaProducto.html">Ver publicación</button>
        `;
        contenedorProductos.appendChild(card);
    }

    //Asignar evento de clic a los botones de "Ver publicación"
    document.querySelectorAll('#enlaceProducto').forEach(button => {
        button.addEventListener('click', function() {
            verProducto.call(this);
        });
    });
}

//Función para mostrar la tabla mostrable de listas
function mostrarTablaMostrableListas() {

    // Si no hay listas, cancelar la función
    if (listas.length == 0)
        return;

    let contenedorListas = document.getElementById('tablaListasMostrable');
    contenedorListas.innerHTML = '';

    // Crear el encabezado de la tabla
    let thead = document.createElement('thead');
    let trHead = document.createElement('tr');

    let thNombre = document.createElement('th');
    thNombre.scope = 'col';
    thNombre.textContent = 'Nombre';
    trHead.appendChild(thNombre);

    let thProductos = document.createElement('th');
    thProductos.scope = 'col';
    thProductos.textContent = 'Productos';
    thProductos.style.width = '60%';
    trHead.appendChild(thProductos);

    thead.appendChild(trHead);
    contenedorListas.appendChild(thead);

    // Crear el cuerpo de la tabla
    let tbody = document.createElement('tbody');

    // Iterar sobre las listas y crear las filas correspondientes
    listas.forEach(lista => {

        // Si la lista es pública, mostrar sus datos en la tabla
        if (lista.Visibilidad === 0) {
            let tr = document.createElement('tr');

            let tdNombre = document.createElement('td');
            tdNombre.textContent = lista.Nombre;
            tr.appendChild(tdNombre);

            let tdProductos = document.createElement('td');
            if (lista.Productos.length != 0) {
                // Concatenar los nombres de todos los productos
                let productosAMostrar = lista.Productos.map(producto => producto.Nombre).join(', ');
                tdProductos.textContent = productosAMostrar;
            } 
            else {
                tdProductos.textContent = '(Sin productos)';
            }
            tr.appendChild(tdProductos);

            tbody.appendChild(tr);
        } 
        else {

            // De lo contrario, mostrar un mensaje de que la lista es privada
            let tr = document.createElement('tr');

            let tdNombre = document.createElement('td');
            tdNombre.textContent = '(Esta lista es privada)';
            tr.appendChild(tdNombre);

            let tdProductos = document.createElement('td');
            tdProductos.textContent = '(Esta lista es privada)';
            tr.appendChild(tdProductos);

            tbody.appendChild(tr);
        }
    });

    contenedorListas.appendChild(tbody);
}

//Función para mostrar la tabla editable de listas
function mostrarTablaEditableListas() {

    //Si no hay listas, cancelar la función y mostrar un mensaje
    if(listas.length == 0)
        return;

    let contenedorListas = document.getElementById('tablaListasEditable');
    contenedorListas.innerHTML = '';

    // Crear el encabezado de la tabla
    let thead = document.createElement('thead');
    let trHead = document.createElement('tr');

    let thNombre = document.createElement('th');
    thNombre.scope = 'col';
    thNombre.textContent = 'Nombre';
    trHead.appendChild(thNombre);

    let thAcciones = document.createElement('th');
    thAcciones.scope = 'col';
    thAcciones.textContent = 'Acciones';
    trHead.appendChild(thAcciones);

    thead.appendChild(trHead);
    contenedorListas.appendChild(thead);

    // Crear el cuerpo de la tabla
    let tbody = document.createElement('tbody');

    // Iterar sobre las listas y crear las filas correspondientes
    listas.forEach(lista => {
        let tr = document.createElement('tr');

        let tdNombre = document.createElement('td');
        tdNombre.textContent = lista.Nombre;
        tr.appendChild(tdNombre);

        let tdAcciones = document.createElement('td');

        let btnConsultar = document.createElement('button');
        btnConsultar.type = 'button';
        btnConsultar.className = 'btn btn-primary btnEditarLista';
        btnConsultar.textContent = 'Consultar';
        tdAcciones.appendChild(btnConsultar);
        btnConsultar.addEventListener('click', () => consultarLista(lista));

        let btnEliminar = document.createElement('button');
        btnEliminar.type = 'button';
        btnEliminar.className = 'btn btn-danger btnEliminarLista';
        btnEliminar.textContent = 'Eliminar';
        btnEliminar.addEventListener('click', () => eliminarLista(lista.Id));
        tdAcciones.appendChild(btnEliminar);

        tr.appendChild(tdAcciones);
        tbody.appendChild(tr);
    });

    contenedorListas.appendChild(tbody);
}

function verProducto() {
    //Obtener el contenedor padre con el atributo data-id
    const card = this.closest('.card');
    const productoId = card.getAttribute('data-id'); //Obtener el id del atributo de datos del contenedor padre
    localStorage.setItem('productoId', productoId);
    window.location.href = "paginaProducto.html";
}

function consultarLista(lista) {
    localStorage.setItem('idLista', lista.Id);
    window.location.href = "consultarLista.html";
}


function mostrarMensajeSinProductos() {
    mensajeSinProductos.style.display = 'block';

    //Ocultar los botones de navegación
    $('#btnAtras').hide();
    $('#btnSiguiente').hide();
}
function mostrarMensajeSinListas() {
    mensajeSinListas.style.display = 'block';
}

//Funciones para cambiar de página de productos
function paginaProductosAnterior() {
    paginaActual = Math.max(0, paginaActual - 1);
    mostrarProductos(paginaActual * productosPorPagina);
}
function paginaProductosSiguiente() {
    paginaActual = Math.min(Math.ceil(productosDeLaLista.length / productosPorPagina) - 1, paginaActual + 1);
    mostrarProductos(paginaActual * productosPorPagina);
}

function obtenerInfoUsuario() {

    //Mandar una petición AJAX al servidor para obtener la información del usuario
    $.ajax({
        type: "GET",
        url: "API/api.php/api/usuarios/perfil",
        success: function(response) {

            //Llenar los campos del formulario con la información extraída
            let respuesta = JSON.parse(response);
            if (respuesta.exito) {
                $('#campoNombre').val(respuesta.mensaje.nombres);
                $('#campoApePat').val(respuesta.mensaje.apellidoPat);
                $('#campoApeMat').val(respuesta.mensaje.apellidoMat);
                $('#campoSexo').val(respuesta.mensaje.sexo == 0 ? 'Masculino' : 'Femenino');
                $('#campoFechaNacimiento').val(respuesta.mensaje.fechaNacimiento);
                $('#campoCorreo').val(respuesta.mensaje.email);
                $('#campoNombreUsuario').val(respuesta.mensaje.usuario);
                $('#campoContrasena').val(respuesta.mensaje.contrasena);
                $('#opcionPublica').prop('checked', respuesta.mensaje.visibilidad == 0);
                $('#opcionPrivada').prop('checked', respuesta.mensaje.visibilidad == 1);
                $('#opcionCliente').prop('checked', respuesta.mensaje.rol == 1);
                $('#opcionVendedor').prop('checked', respuesta.mensaje.rol == 0);
                $('#opcionAdministrador').prop('checked', respuesta.mensaje.rol == 2);
                gestionarRadioButtons();

                //Mostrar el nombre del usuario en el título del perfil
                $('#tituloPerfil').text(respuesta.mensaje.usuario);

                //Si la imagen de perfil obtenida es nula, mostrar una imagen por defecto
                let imagenPerfilObtenida = respuesta.mensaje.imagenPerfil;
                if (imagenPerfilObtenida == null)
                    $('#imagenSeleccionada').attr('src', 'recursos/icon-default.png');
                else
                    $('#imagenSeleccionada').attr('src', "data:image/png;base64," + imagenPerfilObtenida);

                //Si la visibilidad es publica, crear una descripción del perfil con los datos del usuario
                if (respuesta.mensaje.visibilidad == 0) {
                    $('#listaDescripcion').append('<li><strong>Nombre completo:</strong> ' + respuesta.mensaje.nombres + ' ' + respuesta.mensaje.apellidoPat + ' ' + respuesta.mensaje.apellidoMat + '</li>');
                    $('#listaDescripcion').append('<li><strong>Sexo:</strong> ' + (respuesta.mensaje.sexo == 0 ? 'Masculino' : 'Femenino') + '</li>');
                    $('#listaDescripcion').append('<li><strong>Fecha de nacimiento:</strong> ' + respuesta.mensaje.fechaNacimiento + '</li>');
                    $('#listaDescripcion').append('<li><strong>Rol:</strong> ' + (respuesta.mensaje.rol == 0 ? 'Vendedor' : (respuesta.mensaje.rol == 1 ? 'Cliente' : 'Administrador')) + '</li>');
                    $('#listaDescripcion').append('<li><strong>Correo:</strong> ' + respuesta.mensaje.email + '</li>');
                    $('#advertenciaDescripcion').hide();
                }
                //Si la visibilidad es privada, mostrar un mensaje de advertencia
                else{
                    $('#advertenciaDescripcion').show();
                    $('#idDivListas').hide();
                    $('#tablaListasMostrable').hide();
                }

                //Si el rol del usuario es vendedor, mostrar la lista de productos publicados
                if (respuesta.mensaje.rol == 0) {
                    $('#seccionProductos').show();
                    $('#tituloSeccionProductos').text('Productos publicados');
                    cargaProductosPublicados();
                }
                //Si el rol del usuario es administrador, mostrar la lista de productos aprobados
                else if (respuesta.mensaje.rol == 2) {
                    $('#seccionProductos').show();
                    $('#tituloSeccionProductos').text('Productos aprobados');
                    cargarProductosAprobados();
                }
                //De lo contrario si el rol es cliente, ocultar la sección de productos
                else {
                    $('#seccionProductos').hide();
                    cargarListas();

                    //Si la visibilidad es pública mostrar las listas mostrables (las no editables)
                    if (respuesta.mensaje.visibilidad == 0) {
                        $('#idDivListas').show();
                        $('#tablaListasMostrable').show();
                        $('#tablaListasEditable').hide();
                        $('#btnNuevaLista').hide();
                    }
                    //De lo contrario no mostrar la sección de listas
                    else {
                        $('#idDivListas').hide();
                    }
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

function cargarProductosAprobados() {

    //Mandar una petición AJAX al servidor para obtener los productos aprobados por el usuario actual
    $.ajax({
        type: 'GET',
        url: 'API/api.php/api/usuario/aprobados',
        success: function(response) {

            let respuesta = JSON.parse(response);
    
            //Cargar los productos en el contenedor si la respuesta tuvo éxito
            if (respuesta.exito) {

                //Limpiar la lista de productos
                productosDeLaLista = respuesta.mensaje;

                //Mostrar mensaje de sin resultados si no hay productos
                if (productosDeLaLista.length === 0) {
                    mostrarMensajeSinProductos();
                } 
                //De lo contrario, mostrar los productos en el contenedor
                else {
                    mostrarProductos(0);
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

function cargaProductosPublicados() {
    
    //Mandar una petición AJAX al servidor para obtener los productos publicados por el usuario actual
    $.ajax({
        type: 'GET',
        url: 'API/api.php/api/usuario/publicados',
        success: function(response) {

            let respuesta = JSON.parse(response);
    
            //Cargar los productos en el contenedor si la respuesta tuvo éxito
            if (respuesta.exito) {

                //Limpiar la lista de productos
                productosDeLaLista = respuesta.mensaje;

                //Mostrar mensaje de sin resultados si no hay productos
                if (productosDeLaLista.length === 0) {
                    mostrarMensajeSinProductos();
                } 
                //De lo contrario, mostrar los productos en el contenedor
                else {
                    mostrarProductos(0);
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

function cargarListas() {
    
    //Mandar una petición AJAX al servidor para obtener las listas del usuario actual
    $.ajax({
        type: 'GET',
        url: 'API/api.php/api/usuario/listas',
        success: function(response) {

            let respuesta = JSON.parse(response);
    
            //Cargar las listas en el contenedor si la respuesta tuvo éxito
            if (respuesta.exito) {

                listas = respuesta.mensaje;

                //Mostrar mensaje de sin resultados si no hay listas
                if (listas.length === 0) {
                    mostrarMensajeSinListas();
                } 
                //De lo contrario, mostrar las listas en el contenedor
                else {

                    if ($('#toggleEditar').prop('checked')) {
                        mostrarTablaEditableListas();
                    }
                    else {
                        mostrarTablaMostrableListas();
                    }
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

//Función para validar el formulario
function validarFormulario(event) {

    event.preventDefault();
    let mensajesError = [];

    //Validar que el nombre de usuario tenga al menos 3 caracteres
    let nombreUsuario = document.getElementById('campoNombreUsuario').value;
    if (nombreUsuario.length < 3)
        mensajesError.push("El nombre de usuario debe tener al menos 3 caracteres.");

    //Validar el formato de la contraseña mediante una expresión regular
    let contrasena = document.getElementById('campoContrasena').value;
    let expresionRegular = /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[!\"#\$%&/='?¡¿:;,.\\-_+*{\[\]}]).{8,}$/;
    if (!expresionRegular.test(contrasena))
        mensajesError.push("La contraseña no cumple con el formato de al menos 8 caracteres, una mayúscula, una minúscula, un número y un caracter especial.");

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
            url: "API/api.php/api/usuarios/perfil",
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

//Función para eliminar una lista
function eliminarLista(idLista) {
    
    //Mandar una petición AJAX al servidor para eliminar la lista con el id especificado
    $.ajax({
        type: "DELETE",
        url: "API/api.php/api/listas/eliminar",
        data: JSON.stringify({ idLista: idLista }),
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
