<?php
require_once '../Models/Usuario.php';

class LoginController {

    //Métodos
    public function validarDatos($accion, $idUsuario, $correo, $contrasena) {

        //Filtrar según la acción solicitada
        switch ($accion) {

            //Acción 0: Logueo por credenciales
            case 0:
                //Comprobación de errores
                $errores = [];
    
                //Comprobar que el correo ingresado corresponda a un usuario registrado
                $correoEnUso = Usuario::correoEnExistencia($correo);
                if (!$correoEnUso) {
                    $errores[] = 'No se encontraron usuarios con el correo especificado.';
                    return ['exito' => false, 'errores' => $errores];
                } 
                else {
                    //Intentar obtener el usuario con las credenciales ingresadas
                    $usuarioEncontrado = Usuario::obtenerUsuarioPorCredenciales($correo, $contrasena);
    
                    //Si se encontró el usuario, clasificar la respuesta como exitosa y guardarlo como el usuario logueado
                    if ($usuarioEncontrado != null) {
                        
                        //Crear una nueva sesión y guardar el usuario
                        session_start();
                        $_SESSION['usuarioLogueado'] = $usuarioEncontrado;
                        return ['exito' => true, 'mensaje' => $usuarioEncontrado->Id];
                    } 
                    else {
                        
                        //Si no se pudo hacer el logueo, agregar un mensaje de error a la respuesta y clasificarla como negativa
                        $errores[] = 'La contraseña es incorrecta.';
                        return ['exito' => false, 'errores' => $errores];
                    }
                }
                break;
    
            //Acción 1: Logueo automático por ID
            case 1:
                //Intentar obtener el usuario con el ID especificado
                $usuarioEncontrado = Usuario::obtenerUsuarioPorId($idUsuario);
    
                //Si se encontró el usuario, clasificar la respuesta como exitosa y guardarlo como el usuario logueado
                if ($usuarioEncontrado != null) {
                    
                    //Crear una nueva sesión y guardar el usuario
                    session_start();
                    $_SESSION['usuarioLogueado'] = $usuarioEncontrado;
                    return ['exito' => true, 'mensaje' => $usuarioEncontrado->Id];
                } else {

                    //Si no se pudo hacer el logueo automático, agregar un mensaje de error a la respuesta y clasificarla como negativa
                    $errores[] = 'Ocurrió un error al tratar de iniciar la sesión automáticamente.';
                    return ['exito' => false, 'errores' => $errores];
                }
                break;
        }
    }    
}
?>