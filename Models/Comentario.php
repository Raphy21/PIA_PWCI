<?php
require_once 'Conexion.php';

class Comentario {

    public $IdUsuario;
    public $IdProducto;
    public $Puntuacion;
    public $Titulo;
    public $Contenido;
    public $FechaCreacion;

    public $NombreUsuario;

    //Constructor
    private function __construct($IdUsuario, $IdProducto, $Puntuacion, $Titulo, $Contenido, $FechaCreacion) {
        $this->IdUsuario = $IdUsuario;
        $this->IdProducto = $IdProducto;
        $this->Puntuacion = $Puntuacion;
        $this->Titulo = $Titulo;
        $this->Contenido = $Contenido;
        $this->FechaCreacion = $FechaCreacion;
    }

    //Métodos
    public static function crearComentario($idAutor, $idProducto, $puntuacion, $titulo, $contenido) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL CrearValoracion(?, ?, ?, ?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("iiiss", $idAutor, $idProducto, $puntuacion, $titulo, $contenido);
        $resultado = $preparacion->execute();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Si el comentario se creó correctamente, retornar una instancia del Comentario
        if ($resultado) {
            date_default_timezone_set('America/Monterrey');
            return new Comentario($idAutor, $idProducto, $puntuacion, $titulo, $contenido, date('Y-m-d H:i:s'));
        }
        else
            return null;
    }

    public static function obtenerComentariosProducto($idProducto) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerTodasValoracionesProducto(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("i", $idProducto);
        $preparacion->execute();

        //Obtener los resultados
        $resultado = $preparacion->get_result();
        $preparacion->close();

        //Si se encontraron comentarios, crear una lista con ellos
        $comentarios = array();
        while ($datos = $resultado->fetch_assoc()) {
            $instanciaComentario = new Comentario($datos['IdUsuario'], $datos['IdProducto'], $datos['Puntuacion'], $datos['Titulo'], $datos['Comentario'], $datos['FechaHora']);
            $instanciaComentario->NombreUsuario = $datos['NombreUsuario'];

            array_push($comentarios, $instanciaComentario);
        }

        //Si no hubo errores pero no se encontraron comentarios debido a inexistencia, retornar 0 para indicarlo
        if (empty($comentarios)) {
            $conexion->cerrarConexion();
            return 0;
        }

        //Cerrar la conexión y retornar todos los comentarios
        $conexion->cerrarConexion();
        return $comentarios;
    }
}
?>