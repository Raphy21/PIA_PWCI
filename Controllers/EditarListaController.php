<?php
require_once '../Models/Lista.php';

class EditarListaController {

    private $usuarioLogueado;

    //Obtener una referencia al usuario logueado al instanciar el controller
    public function __construct() {
        session_start();
        $this->usuarioLogueado = isset($_SESSION['usuarioLogueado']) ? $_SESSION['usuarioLogueado'] : null;
    }

    public function traerLista($idLista) {

        //Obtener la lista
        $lista = Lista::obtenerListaPorId($idLista);

        //Si se obtuvo una lista
        if ($lista != null) {
            return ['valido' => true, 'respuesta' => $lista];
        } 
        else {
            //Si no se pudo obtener la lista
            $errores[] = 'Se produjo un error inesperado al obtener la lista.';
            return ['valido' => false, 'errores' => $errores];
        }
    }

    public function traerImagenesLista($lista) {

        //Obtener las imágenes de la lista
        $imagenesEncontradas = $lista->obtenerImagenesLista();

        //Si se obtuvieron imágenes
        if ($imagenesEncontradas != null)
            return ['valido' => true, 'respuesta' => $imagenesEncontradas];
        
        //Si no se encontraron imágenes por inexistencia, hacer valida la respuesta pero retornar un arreglo vacío
        else if ($imagenesEncontradas === 0)
            return ['valido' => true, 'respuesta' => []];

        else {
            //Si no se pudieron obtener las imágenes
            $errores[] = 'Se produjo un error inesperado al obtener las imágenes de la lista.';
            return ['valido' => false, 'errores' => $errores];
        }
    }

    public function eliminarImagenesLista($lista) {

        //Eliminar las imágenes de la lista
        $imagenesEliminadas = $lista->eliminarTodasImagenesLista();

        //Si se eliminaron las imágenes
        if ($imagenesEliminadas) {
            return ['valido' => true, 'respuesta' => 'Las imágenes se eliminaron correctamente.'];
        } 
        else {
            //Si no se pudieron eliminar las imágenes
            $errores[] = 'Se produjo un error inesperado al eliminar las imágenes de la lista.';
            return ['valido' => false, 'errores' => $errores];
        }
    }

    public function traerProductosLista($lista) {

        //Obtener los productos de la lista
        $productosLista = $lista->obtenerProductosLista();

        //Si se encontraron productos, retornarlos
        if ($productosLista != null)
            return ['valido' => true, 'respuesta' => $productosLista];
        
        //Si no se encontraron productos por inexistencia, hacer valida la respuesta pero retornar un arreglo vacío
        else if ($productosLista === 0)
            return ['valido' => true, 'respuesta' => []];

        //Si no se encontraron productos por un error, retornar un mensaje de error
        else {
            $errores[] = 'Ocurrió un error al intentar obtener los productos de la lista.';
            return ['valido' => false, 'errores' => $errores];
        }
    }

    public function validarDatosLista($nombre, $descripcion, $visibilidad) {

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
            //Actualizar la lista en la base de datos
            $nuevaLista = Lista::crearLista($this->usuarioLogueado->Id, $nombre, $descripcion, $visibilidad);

            //Si la lista se actualizó correctamente
            if ($nuevaLista != null) {
                return ['valido' => true, 'respuesta' => $nuevaLista];
            } 
            else {
                //Si no se pudo actualizar la lista
                $errores[] = 'Se produjo un error inesperado al actualizar la lista.';
                return ['valido' => false, 'errores' => $errores];
            }
        }
    }

    public function validarDatosEditarLista($idLista, $nombre, $descripcion, $visibilidad) {

        //Comprobación de errores
        $errores = [];

        //Validar que el nombre de la lista no esté en uso (excepto para la lista actual)
        $listaExistente = Lista::nombreListaEnExistenciaExcepto($nombre, $this->usuarioLogueado->Id, $idLista);
        if ($listaExistente) {
            $errores[] = 'Ya existe una lista de este usuario con el nombre especificado.';
        }

        //Si se detectaron errores
        if (!empty($errores)) {
            return ['valido' => false, 'errores' => $errores];
        } 
        else {
            //Actualizar la lista en la base de datos
            $actualizacionCorrecta= Lista::editarLista($idLista, $nombre, $descripcion, $visibilidad);

            //Si la lista se actualizó correctamente
            if ($actualizacionCorrecta) {
                return ['valido' => true, 'respuesta' => 'La lista se actualizó correctamente.'];
            } 
            else {
                //Si no se pudo actualizar la lista
                $errores[] = 'Se produjo un error inesperado al actualizar la lista.';
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

    public function agregarProductoLista($idLista, $idProducto) {

        //Comprobacion de errores
        $errores = [];

        //Validar que el producto no esté ya en la lista previamente
        $productoEnLista = Lista::productoExisteEnLista($idProducto, $idLista);
        if ($productoEnLista) {
            $errores[] = 'El producto ya se encuentra en la lista elegida.';
        }

        //Si se detectaron errores
        if (!empty($errores)) {
            return ['valido' => false, 'errores' => $errores];
        } 
        else {
            //Agregar el producto a la lista en la base de datos
            $productoAgregado = Lista::agregarProductoALista($idProducto, $idLista);

            //Si el producto se agregó correctamente
            if ($productoAgregado) {
                return ['valido' => true, 'respuesta' => 'El producto se agregó correctamente.'];
            } 
            else {
                //Si no se pudo agregar el producto
                $errores[] = 'Se produjo un error inesperado al agregar el producto a la lista.';
                return ['valido' => false, 'errores' => $errores];
            }
        }
    }

    public function eliminarProductoLista($idLista, $idProducto) {

        //Eliminar el producto de la lista en la base de datos
        $productoEliminado = Lista::eliminarProductoDeLista($idProducto, $idLista);

        //Si el producto se eliminó correctamente
        if ($productoEliminado) {
            return ['valido' => true, 'respuesta' => 'El producto se eliminó correctamente.'];
        } 
        else {
            //Si no se pudo eliminar el producto
            $errores[] = 'Se produjo un error inesperado al eliminar el producto de la lista.';
            return ['valido' => false, 'errores' => $errores];
        }
    }
}
?>