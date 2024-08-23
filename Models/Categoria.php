<?php
require_once 'Conexion.php';

class Categoria {

    public $id;
    public $idCreador;
    public $nombre;
    public $descripcion;

    //Constructor
    private function __construct($id, $idCreador, $nombre, $descripcion) {
        $this->id = $id;
        $this->idCreador = $idCreador;
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
    }

    //Métodos
    public static function crearCategoria($idCreador, $nombre, $descripcion) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL CrearCategoria(?, ?, ?, @idNuevaCategoria)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("iss", $idCreador, $nombre, $descripcion);
        $preparacion->execute();
        $preparacion->close();

        //Obtener el valor de idNuevoUsuario
        $preparacion = $conexion->prepararConsulta("SELECT @idNuevaCategoria");
        $preparacion->bind_result($idNuevaCategoria);
        $preparacion->execute();
        $preparacion->fetch();
        $preparacion->close();
    
        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar la nueva categoría creada
        return new Categoria($idNuevaCategoria, $idCreador, $nombre, $descripcion);
    }

    public static function categoriaExistente($nombreCategoria) {

        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la consulta para llamar a la función MySQL
        $preparacion = $conexion->prepararConsulta("SELECT CategoriaExistente(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar el parámetro a la función y ejecutarla
        $preparacion->bind_param("s", $nombreCategoria);
        $preparacion->execute();

        //Obtener el resultado
        $preparacion->bind_result($resultado);
        $preparacion->fetch();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar el resultado
        return $resultado;
    }

    public static function obtenerTodasCategorias() {

        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerTodasCategorias()");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Ejecutar la consulta
        $preparacion->execute();

        //Obtener los resultados
        $resultado = $preparacion->get_result();
        $preparacion->close();

        //Si se encontraron categorías, crear una lista con ellas
        $categorias = array();
        while ($datos = $resultado->fetch_assoc()) {
            $instanciaCategoria = new Categoria($datos['Id'], $datos['IdCreador'], $datos['Nombre'], $datos['Descripcion']);
            array_push($categorias, $instanciaCategoria);
        }

        //Si no hubo errores pero no se encontraron categorías debido a inexistencia, retornar 0 para indicarlo
        if (empty($categorias)) {
            $conexion->cerrarConexion();
            return 0;
        }

        //Cerrar la conexión y retornar todas las categorías
        $conexion->cerrarConexion();
        return $categorias;
    }

    public static function categoriaEnUso($nombreCategoria) {

        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la consulta para llamar a la función MySQL
        $preparacion = $conexion->prepararConsulta("SELECT CategoriaEnUso(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar el parámetro a la función y ejecutarla
        $preparacion->bind_param("s", $nombreCategoria);
        $preparacion->execute();

        //Obtener el resultado
        $preparacion->bind_result($resultado);
        $preparacion->fetch();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar el resultado
        return $resultado;
    }

    public static function eliminarCategoriaPorNombre($nombreCategoria) {

        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL EliminarCategoriaPorNombre(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        //Pasar el parámetro al procedure y ejecutarlo
        $preparacion->bind_param("s", $nombreCategoria);
        $preparacion->execute();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar true cuando la eliminación sea exitosa
        return true;
    }
}
?>
