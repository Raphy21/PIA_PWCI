<?php
require_once '../Models/Usuario.php';

class Middleware {

    private $usuarioLogueado;

    //Obtener una referencia al usuario logueado al instanciar el middleware
    public function __construct() {
        session_start();
        $this->usuarioLogueado = isset($_SESSION['usuarioLogueado']) ? $_SESSION['usuarioLogueado'] : null;
    }

    public function revisarAutorizacion($paginaPorAcceder) {

        //Si no se encontró un usuario logueado, rechazar la solicitud
        if ($this->usuarioLogueado == null) {
            return ['exito' => false, 'mensaje' => ['Ocurrió un error al obtener la información del usuario logueado']];
        }
        else {

            //Ejecutar la lógica de autorización según la página solicitada
            switch ($paginaPorAcceder) {

                case 'CrearPublicacion':

                    //Si el rol del usuario es distinto de vendedor (Rol = 0), rechazar el acceso
                    if ($this->usuarioLogueado->Rol != 0) {
                        return ['exito' => false, 'mensaje' => ['Está página solo está disponible para vendedores']];
                    }
                    else {
                        return ['exito' => true];
                    }
                    break;

                case 'PagoCarrito':

                    //Si el rol del usuario es distinto de cliente (Rol = 1), rechazar el acceso
                    if ($this->usuarioLogueado->Rol != 1) {
                        return ['exito' => false, 'mensaje' => ['Está página solo está disponible para clientes']];
                    }
                    else {
                        return ['exito' => true];
                    }
                    break;

                case 'Cotizacion':
                        
                    //Si el rol del usuario es administrador (Rol = 2), rechazar el acceso
                    if ($this->usuarioLogueado->Rol === 2) {
                        return ['exito' => false, 'mensaje' => ['Está página solo está disponible para vendedores y clientes']];
                    }
                    else {
                        return ['exito' => true];
                    }
                    break;

                case 'AprobacionProductos':
                        
                    //Si el rol del usuario es distinto de administrador (Rol = 2), rechazar el acceso
                    if ($this->usuarioLogueado->Rol != 2) {
                        return ['exito' => false, 'mensaje' => ['Está página solo está disponible para administradores']];
                    }
                    else {
                        return ['exito' => true];
                    }
                    break;

                case 'Pedidos':
                        
                    //Si el rol del usuario es distinto de cliente (Rol = 1), rechazar el acceso
                    if ($this->usuarioLogueado->Rol != 1) {
                        return ['exito' => false, 'mensaje' => ['Está página solo está disponible para clientes']];
                    }
                    else {
                        return ['exito' => true];
                    }
                    break;

                case 'Ventas':
                        
                    //Si el rol del usuario es distinto de vendedor (Rol = 0), rechazar el acceso
                    if ($this->usuarioLogueado->Rol != 0) {
                        return ['exito' => false, 'mensaje' => ['Está página solo está disponible para vendedores']];
                    }
                    else {
                        return ['exito' => true];
                    }
                    break;

                //Páginas no reconocidas
                default:
                    return ['exito' => false, 'mensaje' => ['Página no reconocida']];
                    break;
            }

        }
    }
}

//------EJECUCIÓN DEL MIDDLEWARE------
//Validar el método de la solicitud
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    //Crear una instancia del middleware, ejecutar la lógica de autorización y desechar la instancia
    $middleware = new Middleware();
    $respuesta = $middleware->revisarAutorizacion($_GET['pagina']);
    unset($middleware);

    //Responder con la respuesta del middleware
    echo json_encode($respuesta);
}
else {
    //Método no permitido
    $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
    echo json_encode($respuesta);
}
?>
