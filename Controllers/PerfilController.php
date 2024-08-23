<?php
require_once '../Models/Usuario.php';
require_once '../Models/Producto.php';
require_once '../Models/Lista.php';

class PerfilController {

    private $usuarioLogueado;

    //Obtener una referencia al usuario logueado al instanciar el controller
    public function __construct() {
        session_start();
        $this->usuarioLogueado = isset($_SESSION['usuarioLogueado']) ? $_SESSION['usuarioLogueado'] : null;
    }

    //Métodos
    public function obtenerInfoPerfil() {

        //Si el usuario logueado no se encontró, agregar un mensaje de error a la respuesta
        if ($this->usuarioLogueado == null) {
            $errores[] = 'No se pudo obtener la información del usuario logueado.';
            return ['valido' => false, 'errores' => $errores];
        }
        //De lo contrario, construir una respuesta con la información del perfil del usuario logueado
        else {
            $infoPerfil = array(
                'id' => $this->usuarioLogueado->Id,
                'usuario' => $this->usuarioLogueado->NombreUsuario,
                'email' => $this->usuarioLogueado->Correo,
                'contrasena' => $this->usuarioLogueado->Contrasena,
                'visibilidad' => $this->usuarioLogueado->Visibilidad,
                'rol' => $this->usuarioLogueado->Rol,
                'nombres' => $this->usuarioLogueado->Nombres,
                'apellidoPat' => $this->usuarioLogueado->ApellidoPaterno,
                'apellidoMat' => $this->usuarioLogueado->ApellidoMaterno,
                'fechaNacimiento' => $this->usuarioLogueado->FechaNacimiento,
                'imagenPerfil' => $this->usuarioLogueado->ImagenPerfil,
                'sexo' => $this->usuarioLogueado->Sexo,
                'fechaIngreso' => $this->usuarioLogueado->FechaIngreso
            );

            return ['valido' => true, 'respuesta' => $infoPerfil];
        }
    }

    public function validarDatos($imagenPerfil, $nombre, $apellidoPat, $apellidoMat, $sexo, $fechaNacimiento, $visibilidad, $rol, $email, $usuario, $contrasena) {

        //Comprobación de errores
        $errores = [];

        //Si el usuario logueado no se encontró, agregar un mensaje de error a la respuesta
        if ($this->usuarioLogueado == null) {
            $errores[] = 'Ocurrió un error al obtener la información del usuario logueado para hacer validaciones.';
            return ['valido' => false, 'errores' => $errores];
        }

        //Comprobar que el correo no esté en uso (si es distinto al actual)
        if ($email != $this->usuarioLogueado->Correo) {
            $correoEnUso = Usuario::correoEnExistencia($email);
            if ($correoEnUso)
                $errores[] = 'El correo electrónico ya está en uso.';
        }

        //Comprobar que el nombre de usuario no esté en uso (si es distinto al actual)
        if ($usuario != $this->usuarioLogueado->NombreUsuario) {
            $usuarioEnExistencia = Usuario::nombreUsuarioEnExistencia($usuario);
            if ($usuarioEnExistencia)
                $errores[] = 'El nombre de usuario ya está en uso.';
        }
        
        //Si se detectaron errores, agregarlos a la respuesta
        if (!empty($errores))
           return ['valido' => false, 'errores' => $errores];
        
        //Si no hay errores:
        else {

            //Si no se pasó una imagen de perfil, asignar la misma que ya tenía el usuario
            if ($imagenPerfil == null)
                $imagenPerfil = $this->usuarioLogueado->ImagenPerfil;
          
            //Modificar la información del usuario logueado en la base de datos
            $modificacionExitosa = $this->usuarioLogueado->modificarUsuario($usuario, $email, $contrasena, $visibilidad, $rol, $nombre, $apellidoPat, $apellidoMat, $fechaNacimiento, $imagenPerfil, $sexo);
            
            //Si el usuario se editó correctamente, clasificar la respuesta como válida
            if ($modificacionExitosa)
                return ['valido' => true, 'respuesta' => ['']];

            //Si no se pudo hacer la modificación, agregar un mensaje de error a la respuesta y clasificarla como negativa
            else {
                $errores[] = 'Se produjo un error inesperado al modificar el usuario.';
                return ['valido' => false, 'errores' => $errores];
            }
        }
    }

    public function traerProductosAprobados() {

        //Si el usuario logueado no se encontró, agregar un mensaje de error a la respuesta
        if ($this->usuarioLogueado == null) {
            $errores[] = 'No se pudo obtener la información del usuario logueado.';
            return ['valido' => false, 'errores' => $errores];
        }
        //De lo contrario:
        else {

            $productosAprobados = Producto::obtenerProductosAprobadosUsuario($this->usuarioLogueado->Id);

            //Si se encontraron productos aprobados, retornarlos
            if ($productosAprobados != null)
                return ['valido' => true, 'respuesta' => $productosAprobados];

            //Si no se encontraron productos por inexistencia, hacer valida la respuesta pero retornar un arreglo vacío
            else if ($productosAprobados === 0)
                return ['valido' => true, 'respuesta' => []];

            //Si no se encontraron productos por un error, retornar un mensaje de error
            else {
                $errores[] = 'Ocurrió un error al intentar obtener los productos aprobados por el usuario.';
                return ['valido' => false, 'errores' => $errores];
            }
        }
    }

    public function traerProductosPublicados() {

        //Si el usuario logueado no se encontró, agregar un mensaje de error a la respuesta
        if ($this->usuarioLogueado == null) {
            $errores[] = 'No se pudo obtener la información del usuario logueado.';
            return ['valido' => false, 'errores' => $errores];
        }
        //De lo contrario:
        else {

            $productosPublicados = Producto::obtenerProductosPublicadosUsuario($this->usuarioLogueado->Id);

            //Si se encontraron productos publicados, retornarlos
            if ($productosPublicados != null)
                return ['valido' => true, 'respuesta' => $productosPublicados];

            //Si no se encontraron productos por inexistencia, hacer valida la respuesta pero retornar un arreglo vacío
            else if ($productosPublicados === 0)
                return ['valido' => true, 'respuesta' => []];

            //Si no se encontraron productos por un error, retornar un mensaje de error
            else {
                $errores[] = 'Ocurrió un error al intentar obtener los productos publicados por el usuario.';
                return ['valido' => false, 'errores' => $errores];
            }
        }
    }

    public function traerListasUsuario() {

        //Si el usuario logueado no se encontró, agregar un mensaje de error a la respuesta
        if ($this->usuarioLogueado == null) {
            $errores[] = 'No se pudo obtener la información del usuario logueado.';
            return ['valido' => false, 'errores' => $errores];
        }
        //De lo contrario:
        else {

            $listasUsuario = Lista::obtenerTodasListasUsuario($this->usuarioLogueado->Id);

            //Si se encontraron listas, retornarlas
            if ($listasUsuario != null)
                return ['valido' => true, 'respuesta' => $listasUsuario];

            //Si no se encontraron listas por inexistencia, hacer valida la respuesta pero retornar un arreglo vacío
            else if ($listasUsuario === 0)
                return ['valido' => true, 'respuesta' => []];

            //Si no se encontraron listas por un error, retornar un mensaje de error
            else {
                $errores[] = 'Ocurrió un error al intentar obtener las listas del usuario.';
                return ['valido' => false, 'errores' => $errores];
            }
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

    public function traerImagenesProducto($producto) {

        //Obtener las imágenes del producto
        $imagenesProducto = $producto->obtenerImagenesProducto();

        if ($imagenesProducto != null) {
            return ['valido' => true, 'respuesta' => $imagenesProducto];

        } 
        else {
            $errores[] = 'Ocurrió un error al intentar obtener las imágenes del producto.';
            return ['valido' => false, 'errores' => $errores];
        }
    }

    public function eliminarListaEspecifica($idLista) {

        //Eliminar la lista
        $eliminacionExitosa = Lista::eliminarLista($idLista);

        //Si la lista se eliminó correctamente, hacer válida la respuesta
        if ($eliminacionExitosa)
            return ['valido' => true, 'respuesta' => ['']];

        //Si no se pudo eliminar la lista, agregar un mensaje de error a la respuesta y hacerla inválida
        else {
            $errores[] = 'Se produjo un error inesperado al eliminar la lista.';
            return ['valido' => false, 'errores' => $errores];
        }
    }
}
?>