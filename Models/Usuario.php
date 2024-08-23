<?php
require_once 'Conexion.php';

class Usuario {

    //Atributos
    public $Id;
    public $NombreUsuario;
    public $Correo;
    public $Contrasena;
    public $Visibilidad;
    public $Rol;
    public $Nombres;
    public $ApellidoPaterno;
    public $ApellidoMaterno;
    public $FechaNacimiento;
    public $ImagenPerfil;
    public $Sexo;
    public $FechaIngreso;

    //Constructor
    private function __construct($Id, $NombreUsuario, $Correo, $Contrasena, $Visibilidad, $Rol, $Nombres, $ApellidoPaterno, $ApellidoMaterno, $FechaNacimiento, $ImagenPerfil, $Sexo, $FechaIngreso) {
        $this->Id = $Id;
        $this->NombreUsuario = $NombreUsuario;
        $this->Correo = $Correo;
        $this->Contrasena = $Contrasena;
        $this->Visibilidad = $Visibilidad;
        $this->Rol = $Rol;
        $this->Nombres = $Nombres;
        $this->ApellidoPaterno = $ApellidoPaterno;
        $this->ApellidoMaterno = $ApellidoMaterno;
        $this->FechaNacimiento = $FechaNacimiento;
        $this->ImagenPerfil = $ImagenPerfil;
        $this->Sexo = $Sexo;
        $this->FechaIngreso = $FechaIngreso;
    }

    //Métodos
    public static function registrarUsuario($nombreUsuario, $correo, $contrasena, $visibilidad, $rol, $nombres, $apellidoPaterno, $apellidoMaterno, $fechaNacimiento, $imagenPerfil, $sexo) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();
    
        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL RegistrarUsuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, @idNuevoUsuario)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }

        //Comprobar si la imagen de perfil ya esta en base64
        if (is_string($imagenPerfil))
            $imagenBase64 = $imagenPerfil;

        //Si no lo está, convertir la imagen de perfil a dicho formato para guardarla en la base de datos
        else if ($imagenPerfil != null && $imagenPerfil['tmp_name'] && $imagenPerfil['size'] > 0)
            $imagenBase64 = base64_encode(file_get_contents($imagenPerfil['tmp_name']));

        //Si no se detectó una imagen, dejar este campo como null
        else
            $imagenBase64 = null;
    
        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("sssiisssssi", $nombreUsuario, $correo, $contrasena, $visibilidad, $rol, $nombres, $apellidoPaterno, $apellidoMaterno, $fechaNacimiento, $imagenBase64, $sexo);
        $preparacion->execute();
        $preparacion->close();
    
        //Obtener el valor de idNuevoUsuario
        $preparacion = $conexion->prepararConsulta("SELECT @idNuevoUsuario");
        $preparacion->bind_result($idNuevoUsuario);
        $preparacion->execute();
        $preparacion->fetch();
        $preparacion->close();
    
        //Cerrar la conexión
        $conexion->cerrarConexion();
    
        //Retornar el nuevo usuario creado
        date_default_timezone_set('America/Monterrey');
        return new Usuario($idNuevoUsuario, $nombreUsuario, $correo, $contrasena, $visibilidad, $rol, $nombres, $apellidoPaterno, $apellidoMaterno, $fechaNacimiento, $imagenBase64, $sexo, date("Y-m-d H:i:s"));
    }

    public static function correoEnExistencia($correo) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();
    
        //Preparar la consulta para llamar a la función MySQL
        $preparacion = $conexion->prepararConsulta("SELECT CorreoEnExistencia(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }
    
        //Pasar el parámetro a la función y ejecutarla
        $preparacion->bind_param("s", $correo);
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

    public static function nombreUsuarioEnExistencia($nombreUsuario) {

        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();
    
        //Preparar la consulta para llamar a la función MySQL
        $preparacion = $conexion->prepararConsulta("SELECT NombreUsuarioEnExistencia(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }
    
        //Pasar el parámetro a la función y ejecutarla
        $preparacion->bind_param("s", $nombreUsuario);
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

    public static function obtenerUsuarioPorCredenciales($correo, $contrasena) {

        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();
    
        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerUsuarioPorCredenciales(?, ?)");
        if (!$preparacion){
            $conexion->cerrarConexion();
            return null;
        }
    
        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("ss", $correo, $contrasena);
        $preparacion->execute();
    
        //Obtener el resultado
        $resultado = $preparacion->get_result();
        $preparacion->close();
    
        //Si se encontró un usuario:
        if ($resultado->num_rows == 1) {

            //Obtener los datos del usuario
            $datos = $resultado->fetch_assoc();

            //Crear una instancia de Usuario con los datos obtenidos del procedure
            $usuario = new Usuario(
                $datos['Id'], 
                $datos['NombreUsuario'], 
                $datos['Correo'], 
                $datos['Contrasena'], 
                $datos['Visibilidad'], 
                $datos['Rol'], 
                $datos['Nombres'], 
                $datos['ApellidoPaterno'], 
                $datos['ApellidoMaterno'], 
                $datos['FechaNacimiento'], 
                $datos['ImagenPerfil'], 
                $datos['Sexo'], 
                $datos['FechaIngreso']
            );
        
            //Cerrar la conexión y retornar el nuevo usuario creado
            $conexion->cerrarConexion();
            return $usuario;
        }
        //Si no se encontró un usuario:
         else {
            
            //Cerrar la conexión y retornar null
            $conexion->cerrarConexion();
            return null;
        }
    }

    public static function obtenerUsuarioPorId($id) {

        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();
    
        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ObtenerUsuarioPorId(?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return null;
        }
    
        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("i", $id);
        $preparacion->execute();
    
        //Obtener el resultado
        $resultado = $preparacion->get_result();
        $preparacion->close();
    
        //Si se encontró un usuario:
        if ($resultado->num_rows == 1) {
    
            //Obtener los datos del usuario
            $datos = $resultado->fetch_assoc();
    
            //Crear una instancia de Usuario con los datos obtenidos del procedure
            $usuario = new Usuario(
                $datos['Id'], 
                $datos['NombreUsuario'], 
                $datos['Correo'], 
                $datos['Contrasena'], 
                $datos['Visibilidad'], 
                $datos['Rol'], 
                $datos['Nombres'], 
                $datos['ApellidoPaterno'], 
                $datos['ApellidoMaterno'], 
                $datos['FechaNacimiento'], 
                $datos['ImagenPerfil'], 
                $datos['Sexo'], 
                $datos['FechaIngreso']
            );
    
            //Cerrar la conexión y retornar el nuevo usuario creado
            $conexion->cerrarConexion();
            return $usuario;
        }
        //Si no se encontró un usuario:
        else {
    
            //Cerrar la conexión y retornar null
            $conexion->cerrarConexion();
            return null;
        }
    }

    public function modificarUsuario($nombreUsuario, $correo, $contrasena, $visibilidad, $rol, $nombres, $apellidoPaterno, $apellidoMaterno, $fechaNacimiento, $imagenPerfil, $sexo) {
        
        //Obtener la conexión y abrirla
        $conexion = Conexion::instanciaConexion();
        $conexion->abrirConexion();
    
        //Preparar la llamada al procedure
        $preparacion = $conexion->prepararConsulta("CALL ModificarUsuario(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$preparacion) {
            $conexion->cerrarConexion();
            return false;
        }

        //Comprobar si la imagen de perfil ya esta en base64
        if (is_string($imagenPerfil))
            $imagenBase64 = $imagenPerfil;

        //Si no lo está, convertir la imagen de perfil a dicho formato para guardarla en la base de datos
        else if ($imagenPerfil != null && $imagenPerfil['tmp_name'] && $imagenPerfil['size'] > 0)
            $imagenBase64 = base64_encode(file_get_contents($imagenPerfil['tmp_name']));

        //Si no se detectó una imagen, dejar este campo como null
        else
            $imagenBase64 = null;
    
        //Pasar los parámetros al procedure y ejecutarlo
        $preparacion->bind_param("isssiisssssi", $this->Id, $nombreUsuario, $correo, $contrasena, $visibilidad, $rol, $nombres, $apellidoPaterno, $apellidoMaterno, $fechaNacimiento, $imagenBase64, $sexo);
        $resultado = $preparacion->execute();
        $preparacion->close();
    
        //Cerrar la conexión
        $conexion->cerrarConexion();

        //Actualizar los datos de la instancia actual
        if ($resultado) {
            $this->NombreUsuario = $nombreUsuario;
            $this->Correo = $correo;
            $this->Contrasena = $contrasena;
            $this->Visibilidad = $visibilidad;
            $this->Rol = $rol;
            $this->Nombres = $nombres;
            $this->ApellidoPaterno = $apellidoPaterno;
            $this->ApellidoMaterno = $apellidoMaterno;
            $this->FechaNacimiento = $fechaNacimiento;
            $this->ImagenPerfil = $imagenBase64;
            $this->Sexo = $sexo;
        }
    
        return $resultado;
    }
}
?>
