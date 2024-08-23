<?php
require_once '../Models/Comentario.php';

class PaginaProductoController {

    private $usuarioLogueado;

    //Obtener una referencia al usuario logueado al instanciar el controller
    public function __construct() {
        session_start();
        $this->usuarioLogueado = isset($_SESSION['usuarioLogueado']) ? $_SESSION['usuarioLogueado'] : null;
    }

    public function crearComentarioProducto($idProducto, $titulo, $contenido, $puntuacion) {

        //Comprobación de errores
        $errores = [];

        //Crear el comentario en la base de datos
        $nuevoComentario = Comentario::crearComentario($this->usuarioLogueado->Id, $idProducto, $puntuacion, $titulo, $contenido);
        $nuevoComentario->NombreUsuario = $this->usuarioLogueado->NombreUsuario;

        //Si el comentario se creó correctamente
        if ($nuevoComentario != null) {
            return ['valido' => true, 'respuesta' => $nuevoComentario];
        } 
        else {
            //Si no se pudo crear el comentario
            $errores[] = 'Se produjo un error inesperado al crear el comentario.';
            return ['valido' => false, 'errores' => $errores];
        }
    }

    public function traerComentariosProducto($idProducto) {

        //Obtener la lista de comentarios del producto
        $comentariosEncontrados = Comentario::obtenerComentariosProducto($idProducto);

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