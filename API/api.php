<?php
define('BaseAPI', '/API/api.php');

include_once '../Controllers/RegistroController.php';
include_once '../Controllers/LoginController.php';

//Obtener el endpoint de la solicitud
$endpoint = $_SERVER['REQUEST_URI'];

//Ejecutar la acción correspondiente según el endpoint:
switch ($endpoint) {

    //Registro de usuarios
    case BaseAPI.'/api/usuarios/registro':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Obtener los datos del formulario
            $imagenPerfil = isset($_FILES['imagenPerfil']) ? $_FILES['imagenPerfil'] : null;
            $nombre = $_POST['nombre'];
            $apellidoPat = $_POST['apellidoPat'];
            $apellidoMat = $_POST['apellidoMat'];
            $sexo = $_POST['sexo'];
            $fechaNacimiento = $_POST['fechaNacimiento'];
            $visibilidad = $_POST['visibilidad'];
            $rol = $_POST['rol'];
            $email = $_POST['email'];
            $usuario = $_POST['usuario'];
            $contrasena = $_POST['contrasena'];

            //Crear una instancia del controlador y validar los datos
            $controlador = new RegistroController();
            $resultado = $controlador->validarDatos($imagenPerfil, $nombre, $apellidoPat, $apellidoMat, $sexo, $fechaNacimiento, $visibilidad, $rol, $email, $usuario, $contrasena);
            unset($controlador);
            
            //Responder con exito o errores según el resultado
            if ($resultado['valido']) {
                $respuesta = ['exito' => true, 'mensaje' => ['Usuario registrado correctamente']];
                echo json_encode($respuesta);
            } 
            else {
                $respuesta = ['exito' => false, 'mensaje' => $resultado['errores']];
                echo json_encode($respuesta);
            }
        } 
        else {
            //Método no permitido
            $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
            echo json_encode($respuesta);
        }
        break;

    //Inicio de sesión
    case BaseAPI.'/api/usuarios/login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Obtener los datos del formulario
            $accion = $_POST['accion'];
            $idUsuario = isset($_POST['idUsuario']) ? $_POST['idUsuario'] : null;
            $correo = isset($_POST['correo']) ? $_POST['correo'] : null;
            $contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : null;

            //Crear una instancia del controlador y validar los datos
            $controlador = new LoginController();
            $resultado = $controlador->validarDatos($accion, $idUsuario, $correo, $contrasena);
            unset($controlador);

            //Responder con exito o errores según el resultado
            if ($resultado['exito']) {
                $respuesta = ['exito' => true, 'mensaje' => isset($resultado['mensaje']) ? $resultado['mensaje'] : ""];
                echo json_encode($respuesta);
            } 
            else {
                $respuesta = ['exito' => false, 'mensaje' => $resultado['errores']];
                echo json_encode($respuesta);
            }
        } 
        else {
            //Método no permitido
            $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
            echo json_encode($respuesta);
        }
        break;

    //Perfil de usuario
    case BaseAPI.'/api/usuarios/perfil':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            //Obtener la información del perfil del usuario
            $controlador = new PerfilController();
            $resultado = $controlador->obtenerInfoPerfil();
            unset($controlador);

            //Responder con exito o errores según el resultado
            if ($resultado['valido']) {
                $respuesta = ['exito' => true, 'mensaje' => $resultado['respuesta']];
                echo json_encode($respuesta);
            } 
            else {
                $respuesta = ['exito' => false, 'mensaje' => $resultado['errores']];
                echo json_encode($respuesta);
            }
        } 
        else if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Obtener los datos del formulario
            $imagenPerfil = isset($_FILES['imagenPerfil']) ? $_FILES['imagenPerfil'] : null;
            $nombre = $_POST['nombre'];
            $apellidoPat = $_POST['apellidoPat'];
            $apellidoMat = $_POST['apellidoMat'];
            $sexo = $_POST['sexo'];
            $fechaNacimiento = $_POST['fechaNacimiento'];
            $visibilidad = $_POST['visibilidad'];
            $rol = $_POST['rol'];
            $email = $_POST['email'];
            $usuario = $_POST['usuario'];
            $contrasena = $_POST['contrasena'];

            //Crear una instancia del controlador y validar los datos
            $controlador = new PerfilController();
            $resultado = $controlador->validarDatos($imagenPerfil, $nombre, $apellidoPat, $apellidoMat, $sexo, $fechaNacimiento, $visibilidad, $rol, $email, $usuario, $contrasena);
            unset($controlador);

            //Responder con exito o errores según el resultado
            if ($resultado['valido']) {
                $respuesta = ['exito' => true, 'mensaje' => ['Perfil actualizado correctamente']];
                echo json_encode($respuesta);
            } 
            else {
                $respuesta = ['exito' => false, 'mensaje' => $resultado['errores']];
                echo json_encode($respuesta);
            }
        }
        else {
            //Método no permitido
            $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
            echo json_encode($respuesta);
        }
        break;

    default:
        //Recurso no encontrado
        $respuesta = ['exito' => false, 'mensaje' => ['Endpoint no reconocido']];
        echo json_encode($respuesta);
        break;
}
?>