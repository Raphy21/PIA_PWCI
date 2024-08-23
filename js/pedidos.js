$(document).ready(function() {

    //Comprobar que se tenga autorización para acceder a esta página
    Autorizar();

    $('#consulta-form').submit(function(event) { 
        obtenerPedidos(event); 
    });
});

function Autorizar() {
    
    //Mandar una petición AJAX al middleware para saber si el usuario actual tiene autorización de acceder a la página dada
    $.ajax({
        url: 'Middleware/Middleware.php',
        type: 'GET',
        data: { pagina: 'Pedidos' },
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

function obtenerPedidos(event) {
    event.preventDefault();
    //Recopilar los datos del formulario
    let datosFormulario = new FormData();
    datosFormulario.append('usuario', sessionStorage.getItem("IdUsuario"));
    datosFormulario.append('fechaMin', $('#fecha-inicio').val());  
    datosFormulario.append('fechaMax', $('#fecha-fin').val());  
    datosFormulario.append('categoria', $('#categoria').val());
  
  
    //Mandar una petición AJAX al servidor con los datos del formulario
    $.ajax({
        type: "POST",
        url: "API/api.php/api/listas/pedidos",
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
                            FechaHoraFin: mensaje.FechaHoraFin,
                            Precio: mensaje.Precio,
                            NombreProducto: mensaje.NombreProducto,
                            Calificacion: mensaje.Calificacion,                            
                            IdProducto: mensaje.IdProducto,
                            IdCategoria: mensaje.IdCategoria,                            
                            NombreCategoria: mensaje.NombreCategoria                                             
                        });                                             
                    });
                }
  
                localStorage.setItem("carritoArray", JSON.stringify(respuestasArray));
                console.log(respuesta);   
                renderLista(respuestasArray);    
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

  function renderLista(datos) {
    // Obtener el contenedor de la tabla
    let contenedor = document.getElementById('tabla-container');
    
    // Crear la tabla
    let tabla = document.createElement('table');
    tabla.className = 'styled-table';  // Asignar clase para el estilo
    
    // Crear el encabezado de la tabla
    let thead = document.createElement('thead');
    let filaEncabezado = document.createElement('tr');
    
    let encabezados = ['FechaHoraFin', 'Precio', 'NombreProducto', 'Calificacion', 'NombreCategoria'];
    
    encabezados.forEach(encabezado => {
        let th = document.createElement('th');
        th.textContent = encabezado;
        filaEncabezado.appendChild(th);
    });
    
    thead.appendChild(filaEncabezado);
    tabla.appendChild(thead);
    
    // Crear el cuerpo de la tabla
    let tbody = document.createElement('tbody');
    
    datos.forEach(dato => {
        let fila = document.createElement('tr');
        
        encabezados.forEach(encabezado => {
            let td = document.createElement('td');
            td.textContent = dato[encabezado];
            fila.appendChild(td);
        });
        
        tbody.appendChild(fila);
    });
    
    tabla.appendChild(tbody);
    
    // Limpiar el contenedor y agregar la nueva tabla
    contenedor.innerHTML = '';
    contenedor.appendChild(tabla);
}
