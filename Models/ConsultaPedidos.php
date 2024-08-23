<?php
require_once 'Conexion.php';

class ConsultaPedidos{

    public $FechaHoraFin;
    public $Precio;
    public $NombreProducto;
    public $Calificacion;
    public $IdProducto;
    public $IdCategoria;
    public $NombreCategoria;
    public $Existencia;

    private function __construct($FechaHoraFin, $Precio, $NombreProducto, $Calificacion, $IdProducto, $IdCategoria, $NombreCategoria, $Existencia = null) {
        $this->FechaHoraFin = $FechaHoraFin;
        $this->Precio = $Precio;
        $this->NombreProducto = $NombreProducto;
        $this->Calificacion = $Calificacion;
        $this->IdProducto = $IdProducto;
        $this->IdCategoria = $IdCategoria;
        $this->NombreCategoria = $NombreCategoria; 
        $this->Existencia = $Existencia;           
    }

    public static function obtenerPedidos($usuario, $fechaMin, $fechaMax, $categoria){

        $fechaMin = empty($fechaMin) ? null : $fechaMin;
        $fechaMax = empty($fechaMax) ? null : $fechaMax;
        $categoria = ($categoria === "null") ? null : $categoria;

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerDetalleCarritoMejorado(?, ?, ?, ?);");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("isss", $usuario, $fechaMin, $fechaMax, $categoria);
        $preparacion->execute();

        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();

        $busquedas = array();
        while ($datos = $resultado->fetch_assoc()) {
            $buscar = new ConsultaPedidos(
                $datos['FechaHoraFin'],
                $datos['Precio'],
                $datos['NombreProducto'],
                $datos['Calificacion'],
                $datos['IdProducto'],
                $datos['IdCategoria'],
                $datos['NombreCategoria']                    
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

    public static function obtenerVentas($usuario, $fechaMin, $fechaMax, $categoria){

        $fechaMin = empty($fechaMin) ? null : $fechaMin;
        $fechaMax = empty($fechaMax) ? null : $fechaMax;
        $categoria = ($categoria === "null") ? null : $categoria;

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerVentas(?, ?, ?, ?);");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("isss", $usuario, $fechaMin, $fechaMax, $categoria);
        $preparacion->execute();

        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();

        $busquedas = array();
        while ($datos = $resultado->fetch_assoc()) {
            $buscar = new ConsultaPedidos(
                $datos['FechaHoraFin'],
                $datos['Precio'],
                $datos['NombreProducto'],
                $datos['Calificacion'],
                $datos['IdProducto'],
                $datos['IdCategoria'],
                $datos['NombreCategoria'],
                $datos['Existencia']                    
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
}

?>