var actualizarPrecio = 1;
$(document).ready(function() { 
  recargarPagina();
  obtenerMensajes();
  formularioObtenerRecargar();
  cargarInfoArticulo();
  //Comprobar que se tenga autorización para acceder a esta página
  $('#formularioChat').submit(function(event) { enviarMensajes(event); });
  $('#formularioCotizacion').submit(function(event) { formularioCotizacion(event); });
  //event.preventDefault();
  //Autorizar();
});

function recargarPagina(){
  $('#title').val(localStorage.getItem('NombreArticulo'));
  $('#description1').val(localStorage.getItem('DescripcionArticulo'));
  $('#cantidad').val("0");
  localStorage.setItem("receptor", '');
}

function agregarCarrito() {
  
  //Recopilar los datos del formulario
  let datosFormulario = new FormData();
  datosFormulario.append('idPropietario', sessionStorage.getItem("IdUsuario"));
  datosFormulario.append('terminado', 0);
  datosFormulario.append('idProducto', localStorage.getItem('productoId'));
  datosFormulario.append('Cantidad', $('#cantidad').val()); 
  datosFormulario.append('Precio', $('#cost').val());

  //Mandar una petición AJAX al servidor con los datos del formulario
  $.ajax({
      type: "POST",
      url: "API/api.php/api/carrito/agregarPrecio",
      data: datosFormulario,
      contentType: false,
      processData: false,
      success: function(response) {

          let respuesta = JSON.parse(response);
  
          //Respuesta exitosa
          if (respuesta.exito) {
            alert("Sesion de chat terminada, gracias por cotizar con nosotros"); 
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

function formularioCotizacion(){

  if(localStorage.getItem('esCliente') === "1"){ //TERMINAR CONVERSACION Y AGREGAR AL CARRITO CLIENTE

    var valorCampo = $('#cost').val();
    var cantidad = $('#cantidad').val();
    if(valorCampo === "0.00" || cantidad === "0"){
        alert('CLIENTE: La cantidad y el precio no pueden ser 0, espera a que el vendedor actualice los datos');
    }else{
      let datosFormulario = new FormData();
          datosFormulario.append('IdProducto', localStorage.getItem('productoId'));

        //Mandar una petición AJAX al para traer el historial de mensajes
        $.ajax({
          url: 'API/api.php/api/cotizacion/terminar',
          type: 'POST',
          data: datosFormulario,
          contentType: false,
          processData: false,
          success: function(response) {

              let respuesta = JSON.parse(response);        

              //Respuesta exitosa
              if (respuesta.exito) {         

                agregarCarrito();                                
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

  }else{

      var valorCampo = $('#cost').val();
      var cantidad = $('#cantidad').val();
      if(valorCampo === "0.00" || cantidad === "0"){
          alert('VENDEDOR: La cantidad y el precio no pueden ser 0, por favor aumenta el valor en ambos casos');
      }else{
        let datosFormulario = new FormData();
            datosFormulario.append('IdProducto', localStorage.getItem('productoId'));
            datosFormulario.append('Precio', valorCampo);
            datosFormulario.append('Cantidad', cantidad);

          //Mandar una petición AJAX al para traer el historial de mensajes
          $.ajax({
            url: 'API/api.php/api/cotizacion/cambiarPrecio',
            type: 'POST',
            data: datosFormulario,
            contentType: false,
            processData: false,
            success: function(response) {

                let respuesta = JSON.parse(response);        

                //Respuesta exitosa
                if (respuesta.exito) {           
                    alert("Sesion de chat terminada, gracias por ayudar al cliente"); 
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
  
}

function formularioObtenerRecargar(){

  if(localStorage.getItem('Id') !== '' && localStorage.getItem('IdVendedor') !== '' &&  localStorage.getItem('NombreArticulo') !== '' && localStorage.getItem('DescripcionArticulo') !== ''){

      let datosFormulario = new FormData();

      datosFormulario.append('IdProducto', localStorage.getItem('productoId'));
      datosFormulario.append('Nombre', localStorage.getItem('NombreArticulo'));
      datosFormulario.append('Descripcion', localStorage.getItem('DescripcionArticulo'));
      datosFormulario.append('Precio', 0);
      datosFormulario.append('Estado', 0);
      datosFormulario.append('Cantidad', 0);

      //Mandar una petición AJAX al para traer el historial de mensajes
      $.ajax({
          url: 'API/api.php/api/cotizacion/crear',
          type: 'POST',
          data: datosFormulario,
          contentType: false,
          processData: false,
          success: function(response) {

              let respuesta = JSON.parse(response);        

              //Respuesta exitosa
              if (respuesta.exito) {           

                  //cargarInfoArticulo();
                  console.log(respuesta);                       
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

function cargarInfoArticulo() {

  let datosFormulario = new FormData();

  datosFormulario.append('IdProducto', localStorage.getItem("productoId"));
  
  $.ajax({
      type: "POST",
      url: "API/api.php/api/cotizacion/obtener",
      data: datosFormulario,
      contentType: false,
      processData: false,
      success: function(response) {

        //alert(response);

          let respuesta = JSON.parse(response);

          //Respuesta exitosa
          if (respuesta.exito) {

            $('#title').val(respuesta.mensaje.Nombre);
            $('#description1').val(respuesta.mensaje.Descripcion);
           
            var valorCampo = $('#cost').val();
            if(localStorage.getItem('esCliente') !== "1"){
              if(actualizarPrecio === 1){
                $('#cantidad').val(respuesta.mensaje.Cantidad);
                $('#cost').val(respuesta.mensaje.Precio);
                actualizarPrecio = 0;
              }
            }else{
              $('#cantidad').val(respuesta.mensaje.Cantidad);
              $('#cost').val(respuesta.mensaje.Precio);
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



function enviarMensajes(){
  let datosFormulario = new FormData();
    datosFormulario.append('IdArticulo', localStorage.getItem("IdArticulo")); 
    datosFormulario.append('IdEmisor', sessionStorage.getItem("IdUsuario"));
    datosFormulario.append('IdReceptor', localStorage.getItem("IdVendedor"));
    datosFormulario.append('Contenido', $('#messageInput').val());

  //Mandar una petición AJAX al para traer el historial de mensajes
  $.ajax({
    url: 'API/api.php/api/mensajes/enviar',
    type: 'POST',
    data: datosFormulario,
    contentType: false,
    processData: false,
    success: function(response) {

        let respuesta = JSON.parse(response);        

        //Respuesta exitosa
        if (respuesta.exito) {           

            
            console.log(respuesta);                       
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

function obtenerMensajes(){
  let datosFormulario = new FormData();
    datosFormulario.append('IdEmisor', sessionStorage.getItem("IdUsuario"));     

  //Mandar una petición AJAX al para traer el historial de mensajes
  $.ajax({
    url: 'API/api.php/api/mensajes/buscar',
    type: 'POST',
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

                  if(parseInt(mensaje.IdEmisor) !== parseInt(sessionStorage.getItem("IdUsuario"))){
                    localStorage.setItem("IdVendedor", mensaje.IdEmisor);
                  }
                  localStorage.setItem("IdArticulo", mensaje.IdArticulo);
                    respuestasArray.push({
                        Id: mensaje.Id,
                        IdArticulo: mensaje.IdArticulo,
                        IdEmisor: mensaje.IdEmisor,
                        IdReceptor: mensaje.IdReceptor,
                        Contenido: mensaje.Contenido,
                        NombreArticulo: mensaje.IdArticulo,
                        FechaHora: mensaje.FechaHora,
                        Finalizado: mensaje.Finalizado,
                        
                    });
                });
            }

            localStorage.setItem("mensajesArray", JSON.stringify(respuestasArray));
            console.log(respuesta); 
            renderizarMensajes();                      
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

function obtenerDatosCotizacion(){

    let datosFormulario = new FormData();
      datosFormulario.append('IdEmisor', sessionStorage.getItem("IdUsuario"));     

    //Mandar una petición AJAX al para traer el historial de mensajes
    $.ajax({
      url: 'API/api.php/api/mensajes/buscar',
      type: 'POST',
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

                    if(parseInt(mensaje.IdEmisor) !== parseInt(sessionStorage.getItem("IdUsuario"))){
                      localStorage.setItem("receptor", mensaje.IdEmisor);
                    }
                    localStorage.setItem("IdArticulo", mensaje.IdArticulo);
                      respuestasArray.push({
                          Id: mensaje.Id,
                          IdArticulo: mensaje.IdArticulo,
                          IdEmisor: mensaje.IdEmisor,
                          IdReceptor: mensaje.IdReceptor,
                          Contenido: mensaje.Contenido,
                          NombreArticulo: mensaje.IdArticulo,
                          FechaHora: mensaje.FechaHora,
                          Finalizado: mensaje.Finalizado,
                          
                      });
                  });
              }

              localStorage.setItem("mensajesArray", JSON.stringify(respuestasArray));
              console.log(respuesta); 
              renderizarMensajes();                      
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

function Autorizar() {
    
  //Mandar una petición AJAX al middleware para saber si el usuario actual tiene autorización de acceder a la página dada
  $.ajax({
      url: 'Middleware/Middleware.php',
      type: 'GET',
      data: { pagina: 'Cotizacion' },
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

function renderizarMensajes() {
  const chatContainer = document.getElementById('chat-container');
  const mensajesArray = JSON.parse(localStorage.getItem("mensajesArray")); // Convertir la cadena JSON a un array
  const usuarioActual = parseInt(sessionStorage.getItem("IdUsuario")); // Convertir a número entero

  // Limpiar el contenedor
  chatContainer.innerHTML = '';

  // Recorrer los mensajes en orden ascendente para que los mensajes más recientes estén en la parte inferior
  for (let i = 0; i < mensajesArray.length; i++) {
      const mensaje = mensajesArray[i];
      const mensajeElemento = document.createElement('div');
      mensajeElemento.classList.add('mensaje');

      // Convertir mensaje.IdEmisor a número entero para comparación
      const idEmisor = parseInt(mensaje.IdEmisor);

      if (idEmisor === usuarioActual) {
          mensajeElemento.classList.add('mensaje-emisor');
      } else {
          mensajeElemento.classList.add('mensaje-receptor');
      }

      const contenidoMensaje = `
          <div class="mensaje-contenido">
              <p>${mensaje.Contenido}</p>
              <small>${mensaje.FechaHora}</small>
          </div>
      `;
      mensajeElemento.innerHTML = contenidoMensaje;

      // Agregar el mensaje al inicio del contenedor
      chatContainer.appendChild(mensajeElemento);
  }

  // Desplazar automáticamente hacia abajo para mostrar los mensajes más recientes
  chatContainer.scrollTop = chatContainer.scrollHeight;
}
  
  document.getElementById('formularioCotizacion').addEventListener('submit', function(event) {
    event.preventDefault();
    var title = document.getElementById('title').value;
    var description1 = document.getElementById('description1').value;
    var description2 = document.getElementById('description2').value;
    var cost = document.getElementById('cost').value;

    //Si el costo no es un número float, se muestra un mensaje de error
    if (isNaN(parseFloat(cost))) {
      alert('El costo debe ser un número válido');
      return;
    }
    //Si el costo es menor a 0, se muestra un mensaje de error
    if (parseFloat(cost) < 0) {
      alert('El costo debe ser mayor a 0');
      return;
    }
  
    var quoteMessage = `
      Título: ${title}
      Descripción 1: ${description1}
      Descripción 2: ${description2}
      Costo: $${cost}
    `;
  
    appendMessageToChat(quoteMessage, false);

    //limpiar los campos del formulario
    document.getElementById('title').value = '';
    document.getElementById('description1').value = '';
    document.getElementById('description2').value = '';
    document.getElementById('cost').value = '';
  });

  // Enviar datos del formulario al servidor usando AJAX
function enviarDatosFormulario() {
    const datos = {
        campo1: document.getElementById('campo1').value,
        campo2: document.getElementById('campo2').value,
    };

    $.ajax({
        url: 'http://localhost:3000/enviar', // Ajusta la URL según sea necesario
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(datos),
        success: function(response) {
            console.log('Datos enviados:', response);
        },
        error: function(error) {
            console.error('Error al enviar los datos:', error);
        }
    });
}

// Configurar eventos para recibir notificaciones en tiempo real utilizando Server-Sent Events (SSE)
const eventos = new EventSource('http://localhost:3000/eventos'); // Ajusta la URL según sea necesario
eventos.onmessage = function(evento) {
    const datos = JSON.parse(evento.data);
    actualizarFormulario(datos);
};

// Función para actualizar el formulario con los datos recibidos del servidor
function actualizarFormulario(datos) {
    document.getElementById('campo1').value = datos.campo1;
    document.getElementById('campo2').value = datos.campo2;
    // Actualiza más campos según sea necesario
}


  setInterval(cargarInfoArticulo, 10000);
  setInterval(obtenerMensajes, 10000);