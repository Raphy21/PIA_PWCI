<?php
require_once '../Models/Categoria.php';

class CategoriasController {

    private $usuarioLogueado;

    //Obtener una referencia al usuario logueado al instanciar el controller
    public function __construct() {
        session_start();
        $this->usuarioLogueado = isset($_SESSION['usuarioLogueado']) ? $_SESSION['usuarioLogueado'] : null;
    }

    //Métodos
    public function obtenerCategorias() {

        $categoriasEncontradas = Categoria::obtenerTodasCategorias();

        //Si se encontraron categorías, retornarlas en la respuesta
        if ($categoriasEncontradas != null)
            return ['valido' => true, 'respuesta' => $categoriasEncontradas];
        
        //Si no se encontraron categorías por inexistencia, hacer valida la respuesta pero retornar un arreglo vacío
        else if ($categoriasEncontradas === 0)
            return ['valido' => true, 'respuesta' => []];
        
        //Si no se encontraron categorías por un error, retornar un mensaje de error
        else {
            //Si no se encontraron categorías
            $errores[] = 'Ocurrió un error al obtener las categorías.';
            return ['valido' => false, 'errores' => $errores];
        }
    }
}
?>    