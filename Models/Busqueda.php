<?php
require_once 'Conexion.php';

class Busqueda{

    public $Id;
    public $idVendedor;
    public $nombreProducto;
    public $descripcionProducto;
    public $modo;
    public $precio;
    public $existencia;
    public $calificacion;
    public $aprobado;
    public $IdAdminAprobador;
    public $imagenesProducto = array();
    public $videosProducto = array();
    public $categoria;

    private function __construct($Id, $idVendedor, $nombreProducto, $descripcionProducto, $modo, $precio, $existencia, $calificacion, $aprobado, $IdAdminAprobador, $categoria, $imagenesProducto, $videosProducto) {
        $this->Id = $Id;
        $this->idVendedor = $idVendedor;
        $this->nombreProducto = $nombreProducto;
        $this->descripcionProducto = $descripcionProducto;
        $this->modo = $modo;
        $this->precio = $precio;
        $this->existencia = $existencia;
        $this->calificacion = $calificacion;
        $this->aprobado = $aprobado;
        $this->IdAdminAprobador = $IdAdminAprobador;
        $this->categoria = $categoria;
        $this->imagenesProducto = $imagenesProducto;
        $this->videosProducto = $videosProducto;        
    }

    public static function obtenerBusqueda($busqueda, $precioMin, $precioMax, $categoria, $estrellas, $popular){

        $busqueda = empty($busqueda) ? null : $busqueda;
        $precioMin = empty($precioMin) ? null : $precioMin;
        $precioMax = empty($precioMax) ? null : $precioMax;
        $categoria = ($categoria === "null") ? null : $categoria;
        $estrellas = empty($estrellas) ? null : $estrellas;
        $popular = empty($popular) ? null : $popular;

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL FiltrarProductos(?, ?, ?, ?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("siisi", $busqueda, $precioMin, $precioMax, $categoria, $estrellas);
        $preparacion->execute();

        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();

        $busquedas = array();
        while ($datos = $resultado->fetch_assoc()) {
            $buscar = new Busqueda(
                $datos['id'],
                $datos['idvendedor'],
                $datos['nombre'],
                $datos['descripcion'],
                $datos['modo'],
                $datos['precio'],
                $datos['existencia'],
                $datos['calificacion'],
                $datos['aprobado'],
                $datos['idadminaprobador'],
                $datos['categoria'],
                base64_encode($datos['imagenes']),
                base64_encode($datos['videos'])              
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

    public static function obtenerBusquedaId($busqueda){

        // Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        // Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL FiltrarProductosId(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        // Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("i", $busqueda);
        $preparacion->execute();
        // Ejecutar la consulta
        $resultado = $preparacion->get_result();
        $preparacion->close();

        $datos = $resultado->fetch_assoc();

        //Crear una instancia de Usuario con los datos obtenidos del procedure
        $buscar = new Busqueda(
            $datos['id'],
            $datos['idvendedor'],
            $datos['nombre'],
            $datos['descripcion'],
            $datos['modo'],
            $datos['precio'],
            $datos['existencia'],
            $datos['calificacion'],
            $datos['aprobado'],
            $datos['idadminaprobador'],
            $datos['categoria'],
            base64_encode($datos['imagenes']),
            base64_encode($datos['videos'])
        );
        $conexion->cerrarConexion();
        return $buscar;
    }
}
?>
