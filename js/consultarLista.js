var idLista = localStorage.getItem('idLista');
const imagenesCargadas = [];
var productosDeLaLista = [];
let mensajeSinProductos = document.getElementById('no-products');

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

    //Cargar la información de la lista
    cargarInfoLista();

    // Llamar a la función para ajustar el footer al cargar la página
    ajustarFooter();
});

function base64ToFile(base64Image, filename) {

    // Decodificar la cadena base64
    const byteString = atob(base64Image.split(',')[1]);
    const mimeString = base64Image.split(',')[0].split(':')[1].split(';')[0];

    // Crear un ArrayBuffer y una vista de Uint8Array para los datos binarios
    const arrayBuffer = new ArrayBuffer(byteString.length);
    const uint8Array = new Uint8Array(arrayBuffer);

    // Asignar los valores binarios a la vista de Uint8Array
    for (let i = 0; i < byteString.length; i++) {
        uint8Array[i] = byteString.charCodeAt(i);
    }

    // Crear el objeto File
    const file = new File([uint8Array], filename, { type: mimeString });

    return file;
}

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

function cargarImagenAutomaticamente(imagen) {

    //Crear el html de la imagen para el carrusel
    let htmlImagen = $('<div class="carousel-item"><img class="d-block w-100" src="data:image/png;base64,' + imagen + '" alt="Imagen del carrusel"></div>');

    //Convertir la imagen base64 a un objeto de tipo archivo, para que pueda ser detectado por el servidor de vuelta
    let archivo = base64ToFile("data:image/png;base64," + imagen, 'imagen.png');

    //Añadir el archivo al arreglo de imágenes cargadas
    imagenesCargadas.push(archivo);

    //Añadir el html de la imagen al carrusel
    $('#contenidoCarrusel').append(htmlImagen);
    if ($('#contenidoCarrusel').children().length === 1)
        htmlImagen.addClass('active');

    // Llamar a la función para ajustar el footer después de cargar imágenes
    ajustarFooter();
}

function cargarTablaProductos() {

    let contenedorProductos = document.getElementById('tablaProductos');
    contenedorProductos.innerHTML = '';
    
    //Crear el encabezado de la tabla
    let thead = document.createElement('thead');
    let trHead = document.createElement('tr');

    let thNombre = document.createElement('th');
    thNombre.scope = 'col';
    thNombre.textContent = 'Nombre';
    trHead.appendChild(thNombre);

    let thDescripcion = document.createElement('th');
    thDescripcion.scope = 'col';
    thDescripcion.textContent = 'Descripción';
    trHead.appendChild(thDescripcion);

    let thPrecio = document.createElement('th');
    thPrecio.scope = 'col';
    thPrecio.textContent = 'Precio';
    trHead.appendChild(thPrecio);

    let thEliminar = document.createElement('th');
    thEliminar.scope = 'col';
    thEliminar.textContent = 'Eliminar';
    trHead.appendChild(thEliminar);

    thead.appendChild(trHead);
    contenedorProductos.appendChild(thead);

    //Crear el cuerpo de la tabla
    let tbody = document.createElement('tbody');

    //Iterar sobre los productos de la lista y crear las filas correspondientes
    productosDeLaLista.forEach(producto => {
        let tr = document.createElement('tr');

        let tdNombre = document.createElement('td');
        tdNombre.textContent = producto.Nombre;
        tr.appendChild(tdNombre);

        let tdDescripcion = document.createElement('td');
        tdDescripcion.textContent = producto.Descripcion;
        tr.appendChild(tdDescripcion);

        let tdPrecio = document.createElement('td');
        tdPrecio.textContent = producto.Precio;
        tr.appendChild(tdPrecio);

        let tdEliminar = document.createElement('td');
        let btnEliminar = document.createElement('button');
        btnEliminar.type = 'button';
        btnEliminar.className = 'btn btn-danger';
        btnEliminar.textContent = 'Eliminar';
        btnEliminar.addEventListener('click', () => eliminarProducto(producto.Id));
        tdEliminar.appendChild(btnEliminar);
        tr.appendChild(tdEliminar);

        tbody.appendChild(tr);
    });

    contenedorProductos.appendChild(tbody);
}

function mostrarMensajeSinProductos() {
    mensajeSinProductos.style.display = 'block';
}

function cargarInfoLista() {

    //Obtener la información de la lista mediante AJAX
    $.ajax({
        type: 'POST',
        url: 'API/api.php/api/listas/obtener',
        data: { id: idLista },
        success: function (response) {

            let respuesta = JSON.parse(response);

            //Si la lista no existe, redirigir a dashboard
            if (!respuesta.exito) {
                alert(respuesta.mensaje);
                window.location.href = 'dashboard.html';
            }
            //Si la lista existe, cargar la información
            else {

                let lista = respuesta.mensaje;

                $('#campoNombreLista').val(lista.Nombre);
                $('#campoDescripcionLista').val(lista.Descripcion);

                if (lista.Visibilidad === 1)
                    $('#opcionPrivada').prop('checked', true);
                else
                    $('#opcionPublica').prop('checked', true);

                //Cargar las imágenes de la lista en el carrusel
                lista.Imagenes.forEach(function (imagen) {
                    cargarImagenAutomaticamente(imagen);
                });

                //Obtener los productos y cargarlos en la tabla (si hay productos)
                productosDeLaLista = lista.Productos;
                if (productosDeLaLista.length > 0)
                    cargarTablaProductos();
                else
                    mostrarMensajeSinProductos();

                // Llamar a la función para ajustar el footer después de cargar imágenes
                ajustarFooter();
            }
        },
        error: function () {
            alert('Ocurrió un error inesperado al comunicarse con el servidor.');
            window.location.href = 'dashboard.html';
        }
    });
}

function validarFormulario(event) {

    event.preventDefault();

    //Obtener los datos del formulario
    let nombreLista = $('#campoNombreLista').val();
    let descripcionLista = $('#campoDescripcionLista').val();
    let opcionVisibilidad = $('#opcionPrivada').is(':checked') ? 1 : 0;

    //Reunir los datos en un FormData
    let datosFormulario = new FormData();
    datosFormulario.append('id', idLista);
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
        url: "API/api.php/api/listas/actualizar",
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

function eliminarProducto(idProducto) {

    //Mandar una petición AJAX para eliminar el producto
    $.ajax({
        type: 'DELETE',
        url: 'API/api.php/api/listas/eliminarProducto',
        data: JSON.stringify({ idLista: idLista, idProducto: idProducto }),
        success: function (response) {

            let respuesta = JSON.parse(response);

            //Si el producto se eliminó con éxito
            if (respuesta.exito) {
                alert(respuesta.mensaje);

                //Eliminar el producto de la lista de productos y recargar la tabla
                productosDeLaLista = productosDeLaLista.filter(producto => producto.Id !== idProducto);
                if (productosDeLaLista.length != 0)
                    cargarTablaProductos();
                else{
                    $('#tablaProductos').empty();
                    mostrarMensajeSinProductos();
                }
            }
            //Si hubo un error al eliminar el producto, mostrar mensaje de error
            else {
                alert(respuesta.mensaje);
            }
        },
        //Error
        error: function() {
            alert("Ocurrió un error inesperado al comunicarse con el servidor.");
        }
    });
}
