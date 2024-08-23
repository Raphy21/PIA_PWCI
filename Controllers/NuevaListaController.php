<?php
require_once '../Models/Lista.php';

class NuevaListaController {

    private $usuarioLogueado;

    //Obtener una referencia al usuario logueado al instanciar el controller
    public function __construct() {
        session_start();
        $this->usuarioLogueado = isset($_SESSION['usuarioLogueado']) ? $_SESSION['usuarioLogueado'] : null;
    }

    public function validarDatosNuevaLista($nombre, $descripcion, $visibilidad) {

        //Comprobación de errores
        $errores = [];

        //Validar que el nombre de la lista no esté en uso
        $listaExistente = Lista::nombreListaEnExistencia($nombre, $this->usuarioLogueado->Id);
        if ($listaExistente) {
            $errores[] = 'Ya existe una lista de este usuario con el nombre especificado.';
        }

        //Si se detectaron errores
        if (!empty($errores)) {
            return ['valido' => false, 'errores' => $errores];
        } 
        else {
            //Crear la lista en la base de datos
            $nuevaLista = Lista::crearLista($this->usuarioLogueado->Id, $nombre, $descripcion, $visibilidad);

            //Si la lista se creó correctamente
            if ($nuevaLista != null) {
                return ['valido' => true, 'respuesta' => $nuevaLista];
            } 
            else {
                //Si no se pudo crear la lista
                $errores[] = 'Se produjo un error inesperado al crear la lista.';
                return ['valido' => false, 'errores' => $errores];
            }
        }
    }

    public function asignarImagenesLista($lista, $imagenes) {

        //Comprobación de errores
        $errores = [];

        //Asignar las imágenes a la lista en la base de datos
        $AsignacionesExitosas = 0;
        foreach ($imagenes['tmp_name'] as $indice => $archivo) {

            $imagen = file_get_contents($archivo);
            $AsignacionExitosa = $lista->insertarImagenLista($imagen);

            if ($AsignacionExitosa)
                $AsignacionesExitosas++;
        }

        //Si las asignaciones se realizaron correctamente
        if ($AsignacionesExitosas == count($imagenes['tmp_name'])) {
            return ['valido' => true];
        } 
        else {
            //Si no se pudieron asignar todas las imágenes
            $errores[] = 'Se produjo un error inesperado al asignar las imágenes a la lista.';
            return ['valido' => false, 'errores' => $errores];
        }
    }
}
?>