var productos = [];
const contenedor = document.getElementById('card-container');
const sinResultados = document.getElementById('no-results');
const productosPorPagina = 4;
let paginaActual = 0;

//Evento de carga de la página
$(document).ready(function() {

    //Comprobar que se tenga autorización para acceder a esta página
    Autorizar();

    //Configuración de otros eventos
    $('#btnAtras').click(function() { previousPage(); });
    $('#btnSiguiente').click(function() { nextPage(); });

    //Cargar los productos que no han sido aprobados en el contenedor
    cargarProductosSinAprobar();

    //Evento de clic en el botón de aprobar producto
    $(document).on('click', '#aprobarProducto', function() {
        let idProducto = productos[paginaActual * productosPorPagina + $(this).closest('.card').index()].Id;
        aprobarRechazarProducto(idProducto, true);
    });

    //Evento de clic en el botón de rechazar producto
    $(document).on('click', '#rechazarProducto', function() {
        let idProducto = productos[paginaActual * productosPorPagina + $(this).closest('.card').index()].Id;
        aprobarRechazarProducto(idProducto, false);
    });
});

function Autorizar() {
    
    //Mandar una petición AJAX al middleware para saber si el usuario actual tiene autorización de acceder a la página dada
    $.ajax({
        url: 'Middleware/Middleware.php',
        type: 'GET',
        data: { pagina: 'AprobacionProductos' },
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

function cargarProductosSinAprobar() {
    
    //Mandar una petición AJAX al servidor para obtener los productos que no han sido aprobados
    $.ajax({
        type: 'GET',
        url: 'API/api.php/api/productos/pendientes',
        success: function(response) {

            let respuesta = JSON.parse(response);
    
            //Cargar los productos en el contenedor si la respuesta tuvo éxito
            if (respuesta.exito) {

                //Limpiar la lista de productos
                productos = respuesta.mensaje;

                //Mostrar mensaje de sin resultados si no hay productos
                if (productos.length === 0) {
                    showNoResultsMessage();
                } 
                //De lo contrario, mostrar los productos en el contenedor
                else {
                    showCards(0);
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

function aprobarRechazarProducto(idProducto, aprobacion) {

    let datos = new FormData();
    datos.append('idProducto', idProducto);
    datos.append('aprobacion', aprobacion);

    $.ajax({
        type: 'POST',
        url: 'API/api.php/api/productos/aprobacion',
        data: datos,
        contentType: false,
        processData: false,
        success: function(response) {

            let respuesta = JSON.parse(response);
    
            //Si la respuesta es positiva:
            if (respuesta.exito) {
                alert(respuesta.mensaje);

                //Recargar la página
                window.location.reload();
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

function showCards(startIndex) {
    contenedor.innerHTML = '';
    for (let i = startIndex; i < Math.min(startIndex + productosPorPagina, productos.length); i++) {
        const { Nombre, Descripcion, Imagenes } = productos[i];
        const card = document.createElement('div');
        card.classList.add('card');
        card.innerHTML = `
            <img src="data:image/png;base64,${Imagenes[0]}" alt="${Nombre}" style="width: 100%; height: 100%;">
            <h2>${Nombre}</h2>
            <p>${Descripcion}</p> 
            <br>
            <div class="action-buttons">
                <button id="aprobarProducto">✓</button>
                <button id="rechazarProducto">X</button>
            </div>
        `;
        contenedor.appendChild(card);
    }
}

function showNoResultsMessage() {
    sinResultados.style.display = 'block';

    //Ocultar los botones de navegación
    $('#btnAtras').hide();
    $('#btnSiguiente').hide();
}

function previousPage() {
    paginaActual = Math.max(0, paginaActual - 1);
    showCards(paginaActual * productosPorPagina);
}

function nextPage() {
    paginaActual = Math.min(Math.ceil(productos.length / productosPorPagina) - 1, paginaActual + 1);
    showCards(paginaActual * productosPorPagina);
}