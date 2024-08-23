<?php
require_once '../Models/Usuario.php';

class RegistroController {

    //Métodos
    public function validarDatos($imagenPerfil, $nombre, $apellidoPat, $apellidoMat, $sexo, $fechaNacimiento, $visibilidad, $rol, $email, $usuario, $contrasena) {
        
        //Comprobación de errores
        $errores = [];
    
        //Comprobar que el correo no esté en uso
        $correoEnUso = Usuario::correoEnExistencia($email);
        if ($correoEnUso) {
            $errores[] = 'El correo electrónico ya está en uso.';
        }
    
        //Comprobar que el nombre de usuario no esté en uso
        $usuarioEnExistencia = Usuario::nombreUsuarioEnExistencia($usuario);
        if ($usuarioEnExistencia) {
            $errores[] = 'El nombre de usuario ya está en uso.';
        }
    
        //Si se detectaron errores
        if (!empty($errores)) {
            return ['valido' => false, 'errores' => $errores];
        } else {
            // Registrar al usuario en la base de datos
            $nuevoUsuario = Usuario::registrarUsuario($usuario, $email, $contrasena, $visibilidad, $rol, $nombre, $apellidoPat, $apellidoMat, $fechaNacimiento, $imagenPerfil, $sexo);
    
            //Si el usuario se registró correctamente
            if ($nuevoUsuario != null) {
                return ['valido' => true];
            } else {
                //Si no se pudo hacer el registro
                $errores[] = 'Se produjo un error inesperado al registrar el usuario.';
                return ['valido' => false, 'errores' => $errores];
            }
        }
    }
}
?>
