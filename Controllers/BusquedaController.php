<?php
    require_once '../Models/Usuario.php';
    require_once '../Models/Categoria.php';
    require_once '../Models/Producto.php';
    require_once '../Models/Busqueda.php';
    require_once '../Models/Comentario.php';
    require_once '../Models/Carrito.php';
    require_once '../Models/Mensajes.php';
    require_once '../Models/Cotizacion.php';
    require_once '../Models/ConsultaPedidos.php';

    class BusquedaController {

        public function __construct() {
            
        }

        public function TerminarCotizacion($IdProducto){

            $busquedasEncontradas = Cotizacion::TerminarCotizacion($IdProducto);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Compra realizada con exito.';
                return ['valido' => true, 'errores' => $errores];
            }
        }

        public function CambiarPrecioCotizacion($IdProducto,  $Precio, $Cantidad){

            $busquedasEncontradas = Cotizacion::CambiarPrecioCotizacion($IdProducto,  $Precio, $Cantidad);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Compra realizada con exito.';
                return ['valido' => true, 'errores' => $errores];
            }
        }

        public function obtenerPedidos($usuario, $fechaMin, $fechaMax, $categoria){

            $busquedasEncontradas = ConsultaPedidos::obtenerPedidos($usuario, $fechaMin, $fechaMax, $categoria);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Compra realizada con exito.';
                return ['valido' => true, 'errores' => $errores];
            }
        }

        public function obtenerVentas($usuario, $fechaMin, $fechaMax, $categoria){

            $busquedasEncontradas = ConsultaPedidos::obtenerVentas($usuario, $fechaMin, $fechaMax, $categoria);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Compra realizada con exito.';
                return ['valido' => true, 'errores' => $errores];
            }
        }

        public function ObtenerCotizacionId($IdProducto){

            $busquedasEncontradas = Cotizacion::ObtenerCotizacionId($IdProducto);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Compra realizada con exito.';
                return ['valido' => true, 'errores' => $errores];
            }
        }

        public function InsertarCotizacion($IdProducto, $Nombre, $Descripcion, $Precio, $Estado, $Cantidad){

            $busquedasEncontradas = Cotizacion::InsertarCotizacion($IdProducto, $Nombre, $Descripcion, $Precio, $Estado, $Cantidad);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Ocurrió un error al obtener las busquedas.';
                return ['valido' => false, 'errores' => $errores];
            }

        }

        public function crearMensaje($IdArticulo, $IdEmisor, $IdReceptor, $Contenido){

            $busquedasEncontradas = Mensajes::crearMensaje($IdArticulo, $IdEmisor, $IdReceptor, $Contenido);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Ocurrió un error al obtener las busquedas.';
                return ['valido' => false, 'errores' => $errores];
            }

        }

        public function obtenerMensajes($IdEmisor){

            $busquedasEncontradas = Mensajes::obtenerMensajes($IdEmisor);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Ocurrió un error al obtener las busquedas.';
                return ['valido' => false, 'errores' => $errores];
            }

        }

        public function agregarCarrito($idPropietario, $terminado, $idProducto, $Cantidad){

            $busquedasEncontradas = Carrito::agregarCarrito($idPropietario, $terminado, $idProducto, $Cantidad);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Producto Agregado con exito';
                return['valido' => true, 'respuesta' => []];
            }
        }

        public function agregarCarritoCotizacion($idPropietario, $terminado, $idProducto, $Cantidad, $Precio){

            $busquedasEncontradas = Carrito::agregarCarritoCotizacion($idPropietario, $terminado, $idProducto, $Cantidad, $Precio);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Producto Agregado con exito';
                return['valido' => true, 'respuesta' => []];
            }
        }

        public function obtenerCarrito($idPropietario){

            $busquedasEncontradas = Carrito::obtenerCarrito($idPropietario);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Ocurrió un error al obtener las busquedas.';
                return ['valido' => true, 'errores' => $errores];
            }
        }

        public function comprarCarrito($idPropietario){

            $busquedasEncontradas = Carrito::comprarCarrito($idPropietario);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Compra realizada con exito.';
                return ['valido' => true, 'errores' => $errores];
            }
        }

        public function eliminarItemCarrito($idPropietario, $idArticulo){

            $busquedasEncontradas = Carrito::eliminarItemCarrito($idPropietario, $idArticulo);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Articulo eliminado con exito.';
                return ['valido' => true, 'errores' => $errores];
            }
        }

        public function obtenerBusqueda($busqueda, $precioMin, $precioMax, $categoria, $estrellas, $popular){

            $busquedasEncontradas = Busqueda::obtenerBusqueda($busqueda, $precioMin, $precioMax, $categoria, $estrellas, $popular);

            if($busquedasEncontradas != null)
                return['valido' => true, 'respuesta' => $busquedasEncontradas];

            else if($busquedasEncontradas === 0)
                return['valido' => true, 'respuesta' => []];
            
            else {
                //Si no se encontraron categorías
                $errores[] = 'Ocurrió un error al obtener las busquedas.';
                return ['valido' => false, 'errores' => $errores];
            }
        }

        public function obtenerBusquedaId($busqueda){

            $busquedaEncontrada = Busqueda::obtenerBusquedaId($busqueda);

            //Convertir la busqueda a un objeto de tipo Producto
            $productoEncontrado = new Producto($busquedaEncontrada->Id, $busquedaEncontrada->idVendedor, $busquedaEncontrada->nombreProducto, $busquedaEncontrada->descripcionProducto, $busquedaEncontrada->modo, $busquedaEncontrada->precio, $busquedaEncontrada->existencia, $busquedaEncontrada->calificacion, $busquedaEncontrada->aprobado, $busquedaEncontrada->IdAdminAprobador);
            $productoEncontrado->Categoria = $busquedaEncontrada->categoria;

            if($busquedaEncontrada != null)
                return['valido' => true, 'respuesta' => $productoEncontrado];

            else if($busquedaEncontrada === 0)
                return['valido' => true, 'respuesta' => []];
            
                else {
                //Si no se encontraron categorías
                $errores[] = 'Ocurrió un error al obtener la información del producto.';
                return ['valido' => false, 'errores' => $errores];
            }
        }

        public function traerImagenesProducto($producto) {

            //Obtener las imágenes del producto
            $imagenesProducto = $producto->obtenerImagenesProducto();
    
            if ($imagenesProducto != null) {
                return ['valido' => true, 'respuesta' => $imagenesProducto];
            } 
            else {
                $errores[] = 'Ocurrió un error al intentar obtener las imágenes del producto.';
                return ['valido' => false, 'errores' => $errores];
            }
        }

        public function traerVideosProducto($producto) {

            //Obtener los videos del producto
            $videosProducto = $producto->obtenerVideosProducto();
    
            if ($videosProducto != null) {
                return ['valido' => true, 'respuesta' => $videosProducto];
            } 
            else {
                $errores[] = 'Ocurrió un error al intentar obtener los videos del producto.';
                return ['valido' => false, 'errores' => $errores];
            }
        }

        public function traerComentariosProducto($producto) {

            //Obtener la lista de comentarios del producto
            $comentariosEncontrados = Comentario::obtenerComentariosProducto($producto->Id);
    
            //Si se encontraron comentarios, retornarlos
            if ($comentariosEncontrados != null)
                return ['valido' => true, 'respuesta' => $comentariosEncontrados];
            
            //Si no se encontraron comentarios por inexistencia, hacer valida la respuesta pero retornar un arreglo vacío
            else if ($comentariosEncontrados === 0)
                return ['valido' => true, 'respuesta' => []];
    
            //Si no se encontraron comentarios por un error, retornar un mensaje de error
            else {
                $errores[] = 'Ocurrió un error al intentar obtener los comentarios del producto.';
                return ['valido' => false, 'errores' => $errores];
            }
        }
    }
?>