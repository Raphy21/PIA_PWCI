<?php
require_once 'Conexion.php';

class Producto {
    
    //Atributos
    public $Id;
    public $IdVendedor;
    public $Nombre;
    public $Descripcion;
    public $Modo;
    public $Precio;
    public $Existencia;
    public $Calificacion;
    public $Aprobado;
    public $IdAdminAprobador;

    public $Categoria;
    public $Imagenes = array();
    public $Videos = array();
    public $Comentarios = array();

    //Constructor
    public function __construct($Id, $IdVendedor, $Nombre, $Descripcion, $Modo, $Precio, $Existencia, $Calificacion, $Aprobado, $IdAdminAprobador) {
        $this->Id = $Id;
        $this->IdVendedor = $IdVendedor;
        $this->Nombre = $Nombre;
        $this->Descripcion = $Descripcion;
        $this->Modo = $Modo;
        $this->Precio = $Precio;
        $this->Existencia = $Existencia;
        $this->Calificacion = $Calificacion;
        $this->Aprobado = $Aprobado;
        $this->IdAdminAprobador = $IdAdminAprobador;
    }

    //Métodos
    public static function crearProducto($idVendedor, $nombre, $descripcion, $modo, $precio, $existencia) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL CrearProducto(?, ?, ?, ?, ?, ?, @idNuevoProducto)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("issidi", $idVendedor, $nombre, $descripcion, $modo, $precio, $existencia);
        $preparacion->execute();
        $preparacion->close();

        //Obtener el valor de idNuevoUsuario
        $resultado = $conexion->prepararConsulta("SELECT @idNuevoProducto");
        $resultado->execute();
        $resultado->bind_result($idNuevoProducto);
        $resultado->fetch();
        $resultado->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar el nuevo producto creado
        return new Producto($idNuevoProducto, $idVendedor, $nombre, $descripcion, $modo, $precio, $existencia, 0, false, null);
    }

    public static function nombreProductoEnExistencia($nombreProducto) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la consulta para llamar a la función MySQL
        $preparacion = $conexion->prepararConsulta("SELECT NombreProductoEnExistencia(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar el parámetro a la función y ejecutarla
        $preparacion->bind_param("s", $nombreProducto);
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

    public function categorizarProducto($nombreCategoria) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL CategorizarProducto(?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("is", $this->Id, $nombreCategoria);
        $resultado = $preparacion->execute();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar el resultado de la operación
        return $resultado;
    }

    public function insertarImagenProducto($imagen) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL InsertarImagenProducto(?, ?)");
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
        $preparacion->bind_param("is", $this->Id, $imagenBase64);
        $resultado = $preparacion->execute();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar el resultado de la operación
        return $resultado;
    }

    public function insertarVideoProducto($video) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL InsertarVideoProducto(?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        //Comprobar si el video ya esta en base64
        if (is_string($video))
            $videoBase64 = $video;

        //Si no lo está, convertir el video a dicho formato para guardarla en la base de datos
        else if ($video != null && $video['tmp_name'] && $video['size'] > 0)
            $videoBase64 = base64_encode(file_get_contents($video['tmp_name']));

        //Si no se detectó un video, dejar este campo como null
        else
            $videoBase64 = null;

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("is", $this->Id, $videoBase64);
        $resultado = $preparacion->execute();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar el resultado de la operación
        return $resultado;
    }

    public static function obtenerProductosSinAprobar() {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerProductosSinAprobar()");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Ejecutar la consulta
        $preparacion->execute();

        //Obtener los resultados
        $resultado = $preparacion->get_result();
        $preparacion->close();

        //Si se encontraron productos sin aprobación, crear una lista con ellos
        $productosSinAprobar = array();
        while ($fila = $resultado->fetch_assoc()) {
            $instanciaProducto = new Producto($fila['Id'], $fila['IdVendedor'], $fila['Nombre'], $fila['Descripcion'], $fila['Modo'], $fila['Precio'], $fila['Existencia'], $fila['Calificacion'], $fila['Aprobado'], $fila['IdAdminAprobador']);
            array_push($productosSinAprobar, $instanciaProducto);
        }

        //Si no hubo errores pero no se encontraron productos sin aprobar debido a inexistencia, retornar 0 para indicarlo
        if (empty($productosSinAprobar)) {
            $conexion->cerrarConexion();
            return 0;
        }

        //Cerrar la conexión y retornar todos los productos sin aprobación
        $conexion->cerrarConexion();
        return $productosSinAprobar;
    }

    public function obtenerImagenesProducto() {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerImagenesProducto(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("i", $this->Id);
        $preparacion->execute();

        //Obtener los resultados
        $resultado = $preparacion->get_result();
        $preparacion->close();

        //Si se encontraron imágenes, crear una lista con ellas
        $imagenesProducto = array();
        while ($fila = $resultado->fetch_assoc()) {
            array_push($imagenesProducto, base64_encode($fila['Imagen']));
        }

        //Cerrar la conexión y retornar todas las imágenes
        $conexion->cerrarConexion();
        return $imagenesProducto;
    }

    public function obtenerVideosProducto() {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerVideosProducto(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("i", $this->Id);
        $preparacion->execute();

        //Obtener los resultados
        $resultado = $preparacion->get_result();
        $preparacion->close();

        //Si se encontraron videos, crear una lista con ellos
        $videosProducto = array();
        while ($fila = $resultado->fetch_assoc()) {
            array_push($videosProducto, base64_encode($fila['Video']));
        }

        //Cerrar la conexión y retornar todos los videos
        $conexion->cerrarConexion();
        return $videosProducto;
    }

    public static function aprobarRechazarProducto($idProducto, $aprobado, $idAdminAprobador) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL AprobarRechazarProducto(?, ?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("iii", $idProducto, $aprobado, $idAdminAprobador);
        $resultado = $preparacion->execute();
        $preparacion->close();

        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Retornar el resultado de la operación
        return $resultado;
    }

    public static function obtenerProductosAprobadosUsuario($idAdministrador) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerProductosAprobadosUsuario(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("i", $idAdministrador);
        $preparacion->execute();

        //Obtener los resultados
        $resultado = $preparacion->get_result();
        $preparacion->close();

        //Si se encontraron productos aprobados, crear una lista con ellos
        $productosAprobados = array();
        while ($fila = $resultado->fetch_assoc()) {
            $instanciaProducto = new Producto($fila['Id'], $fila['IdVendedor'], $fila['Nombre'], $fila['Descripcion'], $fila['Modo'], $fila['Precio'], $fila['Existencia'], $fila['Calificacion'], $fila['Aprobado'], $fila['IdAdminAprobador']);
            array_push($productosAprobados, $instanciaProducto);
        }

        //Si no hubo errores pero no se encontraron productos aprobados debido a inexistencia, retornar 0 para indicarlo
        if (empty($productosAprobados)) {
            $conexion->cerrarConexion();
            return 0;
        }

        //Cerrar la conexión y retornar todos los productos aprobados
        $conexion->cerrarConexion();
        return $productosAprobados;
    }

    public static function obtenerProductosPublicadosUsuario($idVendedor) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerProductosPublicadosUsuario(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("i", $idVendedor);
        $preparacion->execute();

        //Obtener los resultados
        $resultado = $preparacion->get_result();
        $preparacion->close();

        //Si se encontraron productos publicados, crear una lista con ellos
        $productosPublicados = array();
        while ($fila = $resultado->fetch_assoc()) {
            $instanciaProducto = new Producto($fila['Id'], $fila['IdVendedor'], $fila['Nombre'], $fila['Descripcion'], $fila['Modo'], $fila['Precio'], $fila['Existencia'], $fila['Calificacion'], $fila['Aprobado'], $fila['IdAdminAprobador']);
            array_push($productosPublicados, $instanciaProducto);
        }

        //Si no hubo errores pero no se encontraron productos publicados debido a inexistencia, retornar 0 para indicarlo
        if (empty($productosPublicados)) {
            $conexion->cerrarConexion();
            return 0;
        }

        //Cerrar la conexión y retornar todos los productos publicados
        $conexion->cerrarConexion();
        return $productosPublicados;
    }

    public static function obtenerProductosInteresantes() {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();

        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerProductosInteresantes()");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Ejecutar la consulta
        $preparacion->execute();

        //Obtener los resultados
        $resultado = $preparacion->get_result();
        $preparacion->close();

        //Si se encontraron productos interesantes, crear una lista con ellos
        $productosInteresantes = array();
        while ($fila = $resultado->fetch_assoc()) {
            $instanciaProducto = new Producto($fila['Id'], $fila['IdVendedor'], $fila['Nombre'], $fila['Descripcion'], $fila['Modo'], $fila['Precio'], $fila['Existencia'], $fila['Calificacion'], $fila['Aprobado'], $fila['IdAdminAprobador']);
            array_push($productosInteresantes, $instanciaProducto);
        }

        //Si no hubo errores pero no se encontraron productos interesantes debido a inexistencia, retornar 0 para indicarlo
        if (empty($productosInteresantes)) {
            $conexion->cerrarConexion();
            return 0;
        }

        //Cerrar la conexión y retornar todos los productos interesantes
        $conexion->cerrarConexion();
        return $productosInteresantes;
    }
}
?>
