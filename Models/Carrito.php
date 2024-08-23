<?php
require_once 'Conexion.php';

class Carrito{

    public $IdProducto;
    public $NombreArticulo;
    public $Cantidad;
    public $Precio;

    private function __construct($IdProducto, $NombreArticulo, $Cantidad, $Precio)
    {
        $this->IdProducto = $IdProducto;
        $this->NombreArticulo = $NombreArticulo;
        $this->Cantidad = $Cantidad;
        $this->Precio = $Precio;
    }

    public static function agregarCarrito($idPropietario, $terminado, $idProducto, $CantidadProducto){

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL InsertarCarritoContenido(?, ?, ?, ?);");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("iiii", $idPropietario, $terminado, $idProducto, $CantidadProducto);
        $preparacion->execute();
        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();
        $conexion->cerrarConexion();
        return $resultado;
    }

    public static function agregarCarritoCotizacion($idPropietario, $terminado, $idProducto, $CantidadProducto, $Precio){

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL InsertarEnCarritoConPrecio(?, ?, ?, ?, ?);");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("iiiii", $idPropietario, $terminado, $idProducto, $CantidadProducto, $Precio);
        $preparacion->execute();
        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();
        $conexion->cerrarConexion();
        return $resultado;
    }

    public static function eliminarItemCarrito($idPropietario, $idArticulo){

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL EliminarCarritoContenido(?, ?);");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("ii", $idPropietario, $idArticulo);
        $preparacion->execute();
        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();
        $conexion->cerrarConexion();
        return $resultado;

    }

    public static function obtenerCarrito($idPropietario){

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerArticulosPendientes(?);");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("i", $idPropietario);
        $preparacion->execute();
        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();
        $conexion->cerrarConexion();
        $busquedas = array();

        while ($datos = $resultado->fetch_assoc()) {
            $buscar = new Carrito(
                $datos['IdProducto'],
                $datos['Nombre'],  
                $datos['Cantidad'],
                $datos['Precio']
                                     
            );
            // Agregar la busqueda al array
            array_push($busquedas, $buscar);
        }

        //Si no hubo errores pero no se encontraron productos debido a inexistencia, retornar 0 para indicarlo
        if (empty($busquedas)) {
            $conexion->cerrarConexion();
            return 0;
        }

        // Cerrar la conexión y retornar el array
        $conexion->cerrarConexion();
        return $busquedas;
    }

    public static function comprarCarrito($idPropietario){

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL MarcarCarritoTerminado(?);");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("i", $idPropietario);
        $preparacion->execute();
        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();
        $conexion->cerrarConexion();
        return 0;

    }

}


?>