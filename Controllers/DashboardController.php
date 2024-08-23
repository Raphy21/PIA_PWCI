<?php
require_once '../Models/Producto.php';

class DashboardController {

    //Métodos
    public function obtenerProductosInteresantes() {

        //Obtener los productos interesantes
        $productos = Producto::obtenerProductosInteresantes();
       
        //Si se encontraron productos interesantes, retornarlos
        if ($productos != null) {
            return ['valido' => true, 'respuesta' => $productos];
        } 
        //Si no se encontraron productos por inexistencia, hacer valida la respuesta pero retornar un arreglo vacío
        else if ($productos === 0) {
            return ['valido' => true, 'respuesta' => []];
        }
        //Si no se encontraron productos por un error, retornar un mensaje de error
        else {
            $errores[] = 'Ocurrió un error al intentar obtener los productos interesantes.';
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
}
?>