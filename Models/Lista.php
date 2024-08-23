<?php
require_once 'Conexion.php';
require_once 'Producto.php';

class Lista {

    //Atributos
    public $Id;
    public $IdPropietario;
    public $Nombre;
    public $Descripcion;
    public $Visibilidad;

    public $Imagenes = array();
    public $Productos = array();

    //Constructor
    private function __construct($Id, $IdPropietario, $Nombre, $Descripcion, $Visibilidad) {
        $this->Id = $Id;
        $this->IdPropietario = $IdPropietario;
        $this->Nombre = $Nombre;
        $this->Descripcion = $Descripcion;
        $this->Visibilidad = $Visibilidad;
    }

    //Métodos
    public static function crearLista($IdPropietario, $Nombre, $Descripcion, $Visibilidad) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL CrearLista(?, ?, ?, ?, @idNuevaLista)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param('issi', $IdPropietario, $Nombre, $Descripcion, $Visibilidad);
        $preparacion->execute();
        $preparacion->close();

        //Obtener el valor de idNuevaLista
        $resultado = $conexion->prepararConsulta("SELECT @idNuevaLista");
        $resultado->execute();
        $resultado->bind_result($idNuevaLista);
        $resultado->fetch();
        $resultado->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar la nueva lista creada
        return new Lista($idNuevaLista, $IdPropietario, $Nombre, $Descripcion, $Visibilidad);
    }

    public static function nombreListaEnExistencia($nombreLista, $idPropietario) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la consulta para llamar a la función MySQL
        $preparacion = $conexion->prepararConsulta("SELECT NombreListaEnExistencia(?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar el parámetro a la función y ejecutarla
        $preparacion->bind_param("si", $nombreLista, $idPropietario);
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

    public static function nombreListaEnExistenciaExcepto($nombreLista, $idPropietario, $idLista) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la consulta para llamar a la función MySQL
        $preparacion = $conexion->prepararConsulta("SELECT NombreListaEnExistenciaExcepto(?, ?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros a la función y ejecutarla
        $preparacion->bind_param("sii", $nombreLista, $idPropietario, $idLista);
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

    public function insertarImagenLista($imagen) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL InsertarImagenLista(?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        //Comprobar si la imagen ya esta en base64
        if (is_string($imagen))
            $imagenBase64 = $imagen;

        //Si no lo está, convertir la imagen a dicho formato para guardarla en la base de datos
        else if ($imagen != null && $imagen['tmp_name'] && $imagen['size'] > 0)
            $imagenBase64 = base64_encode(file_get_contents($imagen['tmp_name']));

        //Si no se detectó una imagen, dejar este campo como null
        else
            $imagenBase64 = null;

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param('is', $this->Id, $imagenBase64);
        $resultado = $preparacion->execute();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar el resultado
        return $resultado;
    }

    public static function obtenerListaPorId($idLista) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerListaPorId(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar el parámetro al procedure y ejecutarlo
        $preparacion->bind_param('i', $idLista);
        $preparacion->execute();

        //Obtener los resultados
        $resultado = $preparacion->get_result();
        $preparacion->close();

        //Si se encontró la lista, crear una instancia de ella
        $datos = $resultado->fetch_assoc();
        $lista = new Lista($datos['Id'], $datos['IdPropietario'], $datos['Nombre'], $datos['Descripcion'], $datos['Visibilidad']);

        //Cerrar la conexión y retornar la lista
        $conexion->cerrarConexion();
        return $lista;
    }

    public function obtenerImagenesLista() {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerImagenesLista(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar el parámetro al procedure y ejecutarlo
        $preparacion->bind_param("i", $this->Id);
        $preparacion->execute();

        //Obtener los resultados
        $resultado = $preparacion->get_result();
        $preparacion->close();

        //Si se encontraron imágenes, crear un array con ellas
        $imagenes = array();
        while ($datos = $resultado->fetch_assoc()) {
            array_push($imagenes, base64_encode($datos['Imagen']));
        }

        //Si no hubo errores pero no se encontraron imágenes debido a inexistencia, retornar 0 para indicarlo
        if (empty($imagenes)) {
            $conexion->cerrarConexion();
            return 0;
        }

        //Cerrar la conexión y retornar todas las imágenes
        $conexion->cerrarConexion();
        return $imagenes;
    }

    public function eliminarTodasImagenesLista() {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL EliminarTodasImagenesLista(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        //Pasar el parámetro al procedure y ejecutarlo
        $preparacion->bind_param('i', $this->Id);
        $resultado = $preparacion->execute();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar el resultado
        return $resultado;
    }

    public static function obtenerTodasListasUsuario($idUsuario) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerTodasListasUsuario(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param('i', $idUsuario);
        $preparacion->execute();

        //Obtener los resultados
        $resultado = $preparacion->get_result();
        $preparacion->close();

        //Si se encontraron listas, crear una lista con ellas
        $listas = array();
        while ($datos = $resultado->fetch_assoc()) {
            $instanciaLista = new Lista($datos['Id'], $datos['IdPropietario'], $datos['Nombre'], $datos['Descripcion'], $datos['Visibilidad']);
            array_push($listas, $instanciaLista);
        }

        //Si no hubo errores pero no se encontraron listas debido a inexistencia, retornar 0 para indicarlo
        if (empty($listas)) {
            $conexion->cerrarConexion();
            return 0;
        }

        //Cerrar la conexión y retornar todas las listas
        $conexion->cerrarConexion();
        return $listas;
    }

    public static function productoExisteEnLista($idProducto, $idLista) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la consulta para llamar a la función MySQL
        $preparacion = $conexion->prepararConsulta("SELECT ProductoExisteEnLista(?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros a la función y ejecutarla
        $preparacion->bind_param("ii", $idProducto, $idLista);
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

    public static function agregarProductoALista($idProducto, $idLista) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL AgregarProductoALista(?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param('ii', $idProducto, $idLista);
        $resultado = $preparacion->execute();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar el resultado
        return $resultado;
    }

    public static function eliminarProductoDeLista($idProducto, $idLista) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL EliminarProductoDeLista(?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param('ii', $idProducto, $idLista);
        $resultado = $preparacion->execute();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar el resultado
        return $resultado;
    }

    public function obtenerProductosLista() {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerProductosLista(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param('i', $this->Id);
        $preparacion->execute();

        //Obtener los resultados
        $resultado = $preparacion->get_result();
        $preparacion->close();

        //Si se encontraron productos, crear un producto con ellos
        $productos = array();
        while ($fila = $resultado->fetch_assoc()) {
            $instanciaProducto = new Producto($fila['Id'], $fila['IdVendedor'], $fila['Nombre'], $fila['Descripcion'], $fila['Modo'], $fila['Precio'], $fila['Existencia'], $fila['Calificacion'], $fila['Aprobado'], $fila['IdAdminAprobador']);
            array_push($productos, $instanciaProducto);
        }

        //Si no hubo errores pero no se encontraron productos debido a inexistencia, retornar 0 para indicarlo
        if (empty($productos)) {
            $conexion->cerrarConexion();
            return 0;
        }

        //Cerrar la conexión y retornar todos los productos
        $conexion->cerrarConexion();
        return $productos;
    }

    public static function editarLista($idLista, $nombre, $descripcion, $visibilidad) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL EditarLista(?, ?, ?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param('issi', $idLista, $nombre, $descripcion, $visibilidad);
        $resultado = $preparacion->execute();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar el resultado
        return $resultado;
    }

    public static function eliminarLista($idLista) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL EliminarLista(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        //Pasar el parámetro al procedure y ejecutarlo
        $preparacion->bind_param('i', $idLista);
        $preparacion->execute();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar true si se eliminó la lista
        return true;
    }
}
?>