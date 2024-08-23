<?php
require_once '../Models/Categoria.php';
require_once '../Models/Producto.php';

class AprobacionController {

    private $usuarioLogueado;

    //Obtener una referencia al usuario logueado al instanciar el controller
    public function __construct() {
        session_start();
        $this->usuarioLogueado = isset($_SESSION['usuarioLogueado']) ? $_SESSION['usuarioLogueado'] : null;
    }

    public function traerProductosSinAprobacion() {

        //Obtener la lista de productos sin aprobar
        $productosSinAprobacion = Producto::obtenerProductosSinAprobar();

        //Si se encontraron productos sin aprobación, retornarlos
        if ($productosSinAprobacion != null)
            return ['valido' => true, 'respuesta' => $productosSinAprobacion];

        //Si no se encontraron productos por inexistencia, hacer valida la respuesta pero retornar un arreglo vacío
        else if ($productosSinAprobacion === 0)
            return ['valido' => true, 'respuesta' => []];
        
        //Si no se encontraron productos por un error, retornar un mensaje de error
        else {
            $errores[] = 'Ocurrió un error al intentar obtener los productos sin aprobación.';
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

    public function aprobacionRechazoProducto($idProducto, $aprobado) {

        //Aprobar o rechazar el producto
        $resultado = Producto::aprobarRechazarProducto($idProducto, $aprobado, $this->usuarioLogueado->Id);

        if ($resultado) {
            return ['valido' => true, 'respuesta' => 'El producto fue ' . ($aprobado ? 'aprobado' : 'rechazado') . ' exitosamente.'];
        } 
        else {
            $errores[] = 'Ocurrió un error al intentar ' . ($aprobado ? 'aprobar' : 'rechazar') . ' el producto.';
            return ['valido' => false, 'errores' => $errores];
        }
    }
}

?>