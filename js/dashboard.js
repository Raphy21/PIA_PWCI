var productos = [];
const contenedor = document.getElementById('contenedorProductosInteresantes');
const sinResultados = document.getElementById('no-results');

$(document).ready(function() {
    //Cargar los productos interesantes en su respectivo contenedor
    cargarProductosInteresantes();

    localStorage.setItem('Id', '');
    localStorage.setItem('IdVendedor', '');
    localStorage.setItem('NombreArticulo', '');
    localStorage.setItem('DescripcionArticulo', '');
    localStorage.setItem('esCliente', "0");
    localStorage.setItem('Id', respuesta.mensaje.Id);            
});

function cargarProductosInteresantes() {

    //Mandar una petición AJAX al servidor para obtener los productos interesantes (los últimos 3 productos más recientes)
    $.ajax({
        type: 'GET',
        url: 'API/api.php/api/productos/interesantes',
        success: function(response) {

            let respuesta = JSON.parse(response);
    
            //Cargar los productos en el contenedor si la respuesta tuvo éxito
            if (respuesta.exito) {

                //Obtener la lista de productos
                productos = respuesta.mensaje;

                //Mostrar mensaje de sin resultados si no hay productos
                if (productos.length === 0) {
                    mostrarMensajeSinResultados();
                } 
                //De lo contrario, mostrar los productos en el contenedor
                else {
                    mostrarTarjetas();
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

function mostrarTarjetas() {
    contenedor.innerHTML = '';
    for (let i = 0; i < productos.length; i++) {
        const { Id, Nombre, Descripcion, Imagenes } = productos[i];
        const tarjeta = document.createElement('div');
        tarjeta.classList.add('col');
        tarjeta.setAttribute('data-id', Id); // Almacenar el id en un atributo de datos
        tarjeta.innerHTML = `
            <article class="card">
                <div class="card-body">
                    <h3 class="card-title">${Nombre}</h3>
                    <p class="card-text">${Descripcion}</p>
                    <img src="data:image/png;base64,${Imagenes[0]}" alt="${Nombre}" style="width: 100%; height: auto;">
                    <br>
                    <div class="btn-conocer-mas">
                        <button>Conocer más</button>
                    </div>
                </div>
            </article>
        `;
        contenedor.appendChild(tarjeta);
    }

    //Asignar evento de clic a los botones de "Conocer más"
    document.querySelectorAll('.btn-conocer-mas button').forEach(button => {
        button.addEventListener('click', function() {
            verProducto.call(this); //Usar call para pasar el contexto correcto
        });
    });
}

function verProducto() {
    //Obtener el contenedor padre con el atributo data-id
    const tarjeta = this.closest('.col');
    const productoId = tarjeta.getAttribute('data-id'); //Obtener el id del atributo de datos del contenedor padre
    localStorage.setItem('productoId', productoId);
    window.location.href = "paginaProducto.html";    
}

function mostrarMensajeSinResultados() {
    sinResultados.style.display = 'block';
}
