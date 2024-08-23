$(document).ready(function() {

    //Comprobar que se tenga autorización para acceder a esta página
    Autorizar();
    obtenerCarrito();
});

function Autorizar() {
    
    //Mandar una petición AJAX al middleware para saber si el usuario actual tiene autorización de acceder a la página dada
    $.ajax({
        url: 'Middleware/Middleware.php',
        type: 'GET',
        data: { pagina: 'PagoCarrito' },
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

function obtenerCarrito() {
  
    //Recopilar los datos del formulario
    let datosFormulario = new FormData();
    datosFormulario.append('idPropietario', sessionStorage.getItem("IdUsuario"));
  
    //Mandar una petición AJAX al servidor con los datos del formulario
    $.ajax({
        type: "POST",
        url: "API/api.php/api/carrito/obtener",
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
                            IdProducto: mensaje.IdProducto,
                            NombreArticulo: mensaje.NombreArticulo,
                            cantidad: parseInt(mensaje.Cantidad, 10),
                            precio: parseFloat(mensaje.Precio)                      
                        });
                    });
                }
  
                localStorage.setItem("carritoArray", JSON.stringify(respuestasArray));
                console.log(respuesta);  
                renderCarrito();            
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

paypal.Buttons({
  createOrder: function(data, actions) {
      return actions.order.create({
          purchase_units: [{
              amount: {
                  value: localStorage.getItem('totalPaypal'), // Monto del pago
                  currency_code: 'MXN' // Moneda del pago
              }
          }]
      });
  },
  onApprove: function(data, actions) {
      return actions.order.capture().then(function(details) {
          // Mostrar un mensaje de éxito
          alert('Pago completado por ' + details.payer.name.given_name);
          comprarProducto();
          document.getElementById('result-message').innerText = 'Pago completado por ' + details.payer.name.given_name;
      });
  }
}).render('#paypal-button-container');

function renderCarrito() {
    const carritoBody = document.getElementById('carrito-body');
    const totalElement = document.getElementById('total');
    const carritoArray = JSON.parse(localStorage.getItem('carritoArray')) || [];
    
    carritoBody.innerHTML = ''; // Limpiar el contenido previo

    let total = 0;

    carritoArray.forEach(item => {
        const subtotal = item.precio * item.cantidad;
        total += subtotal;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${item.NombreArticulo}</td>
            <td>$${item.precio.toFixed(2)}</td>
            <td>${item.cantidad}</td>
            <td>$${subtotal.toFixed(2)}</td>
            <td><button class="btn btn-danger borrar" data-id="${item.IdProducto}">Borrar</button></td>
        `;

        carritoBody.appendChild(row);
    });

    totalElement.textContent = `$${total.toFixed(2)}`;

    localStorage.setItem('totalPaypal', total.toFixed(2));

    console.log(localStorage.getItem('totalPaypal'))

    document.querySelectorAll('.borrar').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            borrarProducto(id);
        });
    });
}

function borrarProducto(id) {
    //Recopilar los datos del formulario
    let datosFormulario = new FormData();
    datosFormulario.append('idPropietario', sessionStorage.getItem("IdUsuario"));
    datosFormulario.append('idArticulo', id);
  
    //Mandar una petición AJAX al servidor con los datos del formulario
    $.ajax({
        type: "POST",
        url: "API/api.php/api/carrito/eliminar",
        data: datosFormulario,
        contentType: false,
        processData: false,
        success: function(response) {
  
            let respuesta = JSON.parse(response);
    
            //Respuesta exitosa
            if (respuesta.exito) {                
                console.log(respuesta);  
                renderCarrito();            
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

function comprarProducto() {
    //Recopilar los datos del formulario
    let datosFormulario = new FormData();
    datosFormulario.append('idPropietario', sessionStorage.getItem("IdUsuario"));
  
    //Mandar una petición AJAX al servidor con los datos del formulario
    $.ajax({
        type: "POST",
        url: "API/api.php/api/carrito/comprar",
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