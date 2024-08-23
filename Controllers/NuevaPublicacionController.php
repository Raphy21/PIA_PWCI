<?php
require_once '../Models/Usuario.php';
require_once '../Models/Categoria.php';
require_once '../Models/Producto.php';

class NuevaPublicacionController {

    private $usuarioLogueado;

    //Obtener una referencia al usuario logueado al instanciar el controller
    public function __construct() {
        session_start();
        $this->usuarioLogueado = isset($_SESSION['usuarioLogueado']) ? $_SESSION['usuarioLogueado'] : null;
    }

    public function validarDatosNuevaCategoria($nombre, $descripcion) {

        //Comprobación de errores
        $errores = [];

        //Validar que el nombre de la categoría no esté en uso
        $categoriaExistente = Categoria::categoriaExistente($nombre);
        if ($categoriaExistente) {
            $errores[] = 'Ya existe una categoría con el nombre especificado.';
        }

        //Si se detectaron errores
        if (!empty($errores)) {
            return ['valido' => false, 'errores' => $errores];
        } 
        else {
            //Crear la categoría en la base de datos
            $nuevaCategoria = Categoria::crearCategoria($this->usuarioLogueado->Id, $nombre, $descripcion);

            //Si la categoría se creó correctamente
            if ($nuevaCategoria != null) {
                return ['valido' => true];
            } 
            else {
                //Si no se pudo crear la categoría
                $errores[] = 'Se produjo un error inesperado al crear la categoría.';
                return ['valido' => false, 'errores' => $errores];
            }
        }
    }

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

    public function eliminarCategoria($nombreCategoria) {

        //Comprobación de errores
        $errores = [];

        //Validar que la categoría no este en uso por algún producto
        $categoriaEnUso = Categoria::categoriaEnUso($nombreCategoria);
        if ($categoriaEnUso) {
            $errores[] = 'La categoría está en uso por uno o más productos.';
        }

        //Si se detectaron errores
        if (!empty($errores)) {
            return ['valido' => false, 'errores' => $errores];
        } 
        else {
            //Eliminar la categoría de la base de datos
            $EliminacionExitosa = Categoria::eliminarCategoriaPorNombre($nombreCategoria);

            //Si la categoría se eliminó correctamente
            if ($EliminacionExitosa) {
                return ['valido' => true];
            } 
            else {
                //Si no se pudo eliminar la categoría
                $errores[] = 'Se produjo un error inesperado al eliminar la categoría.';
                return ['valido' => false, 'errores' => $errores];
            }
        }
    }

    public function validarDatosNuevoProducto($nombreProducto, $descripcionProducto, $modoProducto, $precioProducto, $cantidadProducto) {

        //Comprobación de errores
        $errores = [];

        //Validar que el nombre del producto no esté en uso
        $productoExistente = Producto::nombreProductoEnExistencia($nombreProducto);
        if ($productoExistente) {
            $errores[] = 'Ya existe un producto con el nombre especificado.';
        }

        //Si se detectaron errores
        if (!empty($errores)) {
            return ['valido' => false, 'errores' => $errores];
        } 
        else {
            //Crear el producto en la base de datos
            $nuevoProducto = Producto::crearProducto($this->usuarioLogueado->Id, $nombreProducto, $descripcionProducto, $modoProducto, $precioProducto, $cantidadProducto);

            //Si el producto se creó correctamente
            if ($nuevoProducto != null) {
                return ['valido' => true, 'respuesta' => $nuevoProducto];
            } 
            else {
                //Si no se pudo crear el producto
                $errores[] = 'Se produjo un error inesperado al crear el producto.';
                return ['valido' => false, 'errores' => $errores];
            }
        }
    }

    public function asignarCategoriaProducto($producto, $nombreCategoria) {

        //Comprobación de errores
        $errores = [];

        //Categorizar el producto en la base de datos
        $CategorizacionExitosa = $producto->categorizarProducto($nombreCategoria);

        //Si la categorización se realizó correctamente
        if ($CategorizacionExitosa) {
            return ['valido' => true];
        } 
        else {
            //Si no se pudo categorizar el producto
            $errores[] = 'Se produjo un error inesperado al categorizar el producto.';
            return ['valido' => false, 'errores' => $errores];
        }
    }

    public function asignarImagenesProducto($producto, $imagenes) {

        //Comprobación de errores
        $errores = [];

        //Asignar las imágenes al producto en la base de datos
        $AsignacionesExitosas = 0;
        foreach ($imagenes['tmp_name'] as $indice => $archivo) {

            $imagen = file_get_contents($archivo);
            $AsignacionExitosa = $producto->insertarImagenProducto($imagen);

            if ($AsignacionExitosa)
                $AsignacionesExitosas++;
        }

        //Si las asignaciones se realizaron correctamente
        if ($AsignacionesExitosas == count($imagenes['tmp_name'])) {
            return ['valido' => true];
        } 
        else {
            //Si no se pudieron asignar todas las imágenes
            $errores[] = 'Se produjo un error inesperado al asignar las imágenes al producto.';
            return ['valido' => false, 'errores' => $errores];
        }
    }

    public function asignarVideosProducto($producto, $videos) {

        //Comprobación de errores
        $errores = [];

        //Asignar los videos al producto en la base de datos
        $AsignacionesExitosas = 0;
        foreach ($videos['tmp_name'] as $indice => $archivo) {

            $video = file_get_contents($archivo);
            $AsignacionExitosa = $producto->insertarVideoProducto($video);

            if ($AsignacionExitosa)
                $AsignacionesExitosas++;
        }

        //Si las asignaciones se realizaron correctamente
        if ($AsignacionesExitosas == count($videos['tmp_name'])) {
            return ['valido' => true];
        } 
        else {
            //Si no se pudieron asignar todos los videos
            $errores[] = 'Se produjo un error inesperado al asignar los videos al producto.';
            return ['valido' => false, 'errores' => $errores];
        }
    }
}
?>
