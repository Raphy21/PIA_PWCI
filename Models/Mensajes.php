<?php
require_once 'Conexion.php';

class Mensajes{

    public $Id;
    public $IdArticulo;
    public $IdEmisor;
    public $IdReceptor;
    public $Contenido;
    public $FechaHora;
    public $Finalizado;    

    private function __construct($Id, $IdArticulo, $IdEmisor, $IdReceptor, $Contenido, $FechaHora, $Finalizado)
    {
        $this->Id = $Id;
        $this->IdArticulo = $IdArticulo;
        $this->IdEmisor = $IdEmisor;
        $this->IdReceptor = $IdReceptor;
        $this->Contenido = $Contenido;
        $this->FechaHora = $FechaHora;
        $this->Finalizado = $Finalizado;
    }

    public static function crearMensaje($IdArticulo, $IdEmisor, $IdReceptor, $Contenido){

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL InsertarMensaje(?, ?, ?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("iiis", $IdArticulo, $IdEmisor, $IdReceptor, $Contenido);
        $preparacion->execute();
        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();
        return 0;
    }

    public static function obtenerMensajes($IdEmisor){

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();
    
        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL MostrarMensajes(?);");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }
    
        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("i", $IdEmisor);
        $preparacion->execute();
        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();
        $conexion->cerrarConexion();
        $busquedas = array();
    
        while ($datos = $resultado->fetch_assoc()) {
            $buscar = new Mensajes(
                $datos['Id'],
                $datos['IdArticulo'],
                $datos['IdEmisor'],
                $datos['IdReceptor'],
                $datos['Contenido'],
                $datos['FechaHora'],
                $datos['Finalizado']   
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