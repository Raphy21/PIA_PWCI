<?php
require_once 'Conexion.php';

class Cotizacion {

    //Atributos
    public $Id;
    public $IdProducto;
    public $Nombre;
    public $Descripcion;
    public $Precio;
    public $Estado;
    public $Cantidad;

    //Constructor
    private function __construct($Id, $IdProducto, $Nombre, $Descripcion, $Precio, $Estado, $Cantidad) {
        $this->Id = $Id;
        $this->IdProducto = $IdProducto;
        $this->Nombre = $Nombre;
        $this->Descripcion = $Descripcion;
        $this->Precio = $Precio;
        $this->Estado = $Estado;  
        $this->Cantidad = $Cantidad;      
    }

    public static function InsertarCotizacion($IdProducto, $Nombre, $Descripcion, $Precio, $Estado, $Cantidad){

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL InsertarCotizacion(?, ?, ?, ?, ?, ?);");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("issiii", $IdProducto, $Nombre, $Descripcion, $Precio, $Estado, $Cantidad);
        $preparacion->execute();
        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();
        $conexion->cerrarConexion();
        return 0;
    }

    public static function CambiarPrecioCotizacion($IdProducto,  $Precio, $Cantidad){

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ActualizarPrecioCotizacion(?, ?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("iii", $IdProducto, $Precio, $Cantidad);
        $preparacion->execute();
        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();
        $conexion->cerrarConexion();
        return 0;
    }

    public static function TerminarCotizacion($IdProducto){

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ActualizarEstadoCotizacion(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("i", $IdProducto);
        $preparacion->execute();
        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();
        $conexion->cerrarConexion();
        return 0;
    }

    public static function ObtenerCotizacionId($IdProducto){

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerCotizacionesPorIdProducto(?);");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("i", $IdProducto);
        $preparacion->execute();
        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();

        $datos = $resultado->fetch_assoc();

        //Crear una instancia de Usuario con los datos obtenidos del procedure
        $buscar = new Cotizacion(
            $datos['Id'],
            $datos['IdProducto'],
            $datos['Nombre'],
            $datos['Descripcion'],
            $datos['Precio'],
            $datos['Estado'],
            $datos['Cantidad'],           
        );
        $conexion->cerrarConexion();
        return $buscar;
    }
}
?>