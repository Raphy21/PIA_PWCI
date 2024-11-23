<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

define('BaseAPI', '/API/api.php');

include_once '../Controllers/RegistroController.php';
include_once '../Controllers/LoginController.php';
include_once '../Controllers/PerfilController.php';
include_once '../Controllers/NuevaPublicacionController.php';
include_once '../Controllers/AprobacionController.php';
include_once '../Controllers/BusquedaController.php';
include_once '../Controllers/DashboardController.php';
include_once '../Controllers/PaginaProductoController.php';
include_once '../Controllers/NuevaListaController.php';
include_once '../Controllers/EditarListaController.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 204 No Content");
    exit(0); // Salir para no procesar más lógica
}

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

    //Obtener productos aprobados para mostrar en el perfil de usuario
    case BaseAPI.'/api/usuario/aprobados':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            //Obtener la información de los productos aprobados
            $controlador = new PerfilController();
            $resultado = $controlador->traerProductosAprobados();

            //Si el resultado es valido:
            if ($resultado['valido']) {

                //Si se encontraron productos aprobados:
                if (count($resultado['respuesta']) > 0) {

                    //Obtener las imágenes de cada producto
                    foreach ($resultado['respuesta'] as $producto) {

                        $imagenesProducto = $controlador->traerImagenesProducto($producto);
                        if ($imagenesProducto['valido'])
                            $producto->Imagenes = $imagenesProducto['respuesta'];
                    }

                    //Desechar la instancia del controlador
                    unset($controlador);

                    //Si se pudieron obtener las imágenes de los productos, responder con exito
                    if ($imagenesProducto['valido']) {
                        $respuesta = ['exito' => true, 'mensaje' => $resultado['respuesta']];
                        echo json_encode($respuesta);
                    } 
                    //De lo contrario, responder con errores
                    else {
                        $respuesta = ['exito' => false, 'mensaje' => $imagenesProducto['errores']];
                        echo json_encode($respuesta);
                    }
                }
                //Si no se encontraron productos aprobados, responder con exito pero con un mensaje vacío
                else {
                    unset($controlador);
                    $respuesta = ['exito' => true, 'mensaje' => []];
                    echo json_encode($respuesta);
                } 
            } 
            else {
                unset($controlador);
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
    
    //Obtener los productos publicados para mostrar en el perfil de usuario
    case BaseAPI.'/api/usuario/publicados':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            //Obtener la información de los productos publicados
            $controlador = new PerfilController();
            $resultado = $controlador->traerProductosPublicados();

            //Si el resultado es valido:
            if ($resultado['valido']) {

                //Si se encontraron productos publicados:
                if (count($resultado['respuesta']) > 0) {

                    //Obtener las imágenes de cada producto
                    foreach ($resultado['respuesta'] as $producto) {

                        $imagenesProducto = $controlador->traerImagenesProducto($producto);
                        if ($imagenesProducto['valido'])
                            $producto->Imagenes = $imagenesProducto['respuesta'];
                    }

                    //Desechar la instancia del controlador
                    unset($controlador);

                    //Si se pudieron obtener las imágenes de los productos, responder con exito
                    if ($imagenesProducto['valido']) {
                        $respuesta = ['exito' => true, 'mensaje' => $resultado['respuesta']];
                        echo json_encode($respuesta);
                    } 
                    //De lo contrario, responder con errores
                    else {
                        $respuesta = ['exito' => false, 'mensaje' => $imagenesProducto['errores']];
                        echo json_encode($respuesta);
                    }
                }
                //Si no se encontraron productos publicados, responder con exito pero con un mensaje vacío
                else {
                    unset($controlador);
                    $respuesta = ['exito' => true, 'mensaje' => []];
                    echo json_encode($respuesta);
                } 
            } 
            else {
                unset($controlador);
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

    //Obtener las listas del usuario para mostrar en el perfil de usuario
    case BaseAPI.'/api/usuario/listas':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            //Obtener la información de las listas del usuario
            $controlador = new PerfilController();
            $resultado = $controlador->traerListasUsuario();

            //Si el resultado es valido:
            if ($resultado['valido']) {

                //Si se encontraron listas:
                if (count($resultado['respuesta']) > 0) {

                    //Obtener los productos de cada lista
                    foreach ($resultado['respuesta'] as $lista) {

                        $productosLista = $controlador->traerProductosLista($lista);
                        if ($productosLista['valido'])
                            $lista->Productos = $productosLista['respuesta'];
                    }

                    //Desechar la instancia del controlador
                    unset($controlador);

                    //Si se pudieron obtener los productos de las listas, responder con exito
                    if ($productosLista['valido']) {
                        $respuesta = ['exito' => true, 'mensaje' => $resultado['respuesta']];
                        echo json_encode($respuesta);
                    }
                    //De lo contrario, responder con errores
                    else {
                        $respuesta = ['exito' => false, 'mensaje' => $productosLista['errores']];
                        echo json_encode($respuesta);
                    }
                }
                //Si no se encontraron listas, responder con exito pero con un mensaje vacío
                else {
                    unset($controlador);
                    $respuesta = ['exito' => true, 'mensaje' => []];
                    echo json_encode($respuesta);
                } 
            } 
            else {
                unset($controlador);
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

    //Creación de categorías
    case BaseAPI.'/api/categorias/creacion':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Obtener los datos del formulario
            $nombreCategoria = $_POST['nombreCategoria'];
            $descripcionCategoria = $_POST['descripcionCategoria'];

            //Crear una instancia del controlador y validar los datos
            $controlador = new NuevaPublicacionController();
            $resultado = $controlador->validarDatosNuevaCategoria($nombreCategoria, $descripcionCategoria);
            unset($controlador);

            //Responder con exito o errores según el resultado
            if ($resultado['valido']) {
                $respuesta = ['exito' => true, 'mensaje' => ['Categoría creada correctamente']];
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

    //Obtener categorías
    case BaseAPI.'/api/categorias/obtener':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            //Obtener la información de las categorías
            $controlador = new NuevaPublicacionController();
            $resultado = $controlador->obtenerCategorias();
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
        else {
            //Método no permitido
            $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
            echo json_encode($respuesta);
        }
        break;

    //Eliminar categorías
    case BaseAPI.'/api/categorias/eliminacion':
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

            //Obtener los datos de la solicitud
            $datos = json_decode(file_get_contents('php://input'), true);
            $nombreCategoria = $datos['nombreCategoria'];

            //Crear una instancia del controlador y validar los datos
            $controlador = new NuevaPublicacionController();
            $resultado = $controlador->eliminarCategoria($nombreCategoria);
            unset($controlador);

            //Responder con exito o errores según el resultado
            if ($resultado['valido']) {
                $respuesta = ['exito' => true, 'mensaje' => ['Categoría eliminada correctamente']];
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

    //Creación de listas
    case BaseAPI.'/api/listas/creacion':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Obtener los datos del formulario
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $visibilidad = $_POST['visibilidad'];

            $imagenes = isset($_FILES['imagenes']) ? $_FILES['imagenes'] : null;

            //Crear una instancia del controlador y validar los datos
            $controlador = new NuevaListaController();
            $resultado = $controlador->validarDatosNuevaLista($nombre, $descripcion, $visibilidad, $imagenes);

            //Si la lista se creó correctamente y se especificaron imágenes:
            if ($resultado['valido'] && $imagenes != null) {

                //Subir las imágenes de la lista
                $listaCreada = $resultado['respuesta'];
                $resultado = $controlador->asignarImagenesLista($listaCreada, $imagenes);

                //Deshechar la instancia del controlador
                unset($controlador);

                //Si se subieron las imágenes correctamente, responder con exito la creación de la lista
                if ($resultado['valido']) {
                    $respuesta = ['exito' => true, 'mensaje' => ['Lista creada correctamente']];
                    echo json_encode($respuesta);
                }
                else{
                    $respuesta = ['exito' => false, 'mensaje' => $resultado['errores']];
                    echo json_encode($respuesta);
                }
            } 
            //Si la lista se creó correctamente pero no se especificaron imágenes, responder con exito la creación de la lista
            else if ($resultado['valido']) {
                unset($controlador);
                $respuesta = ['exito' => true, 'mensaje' => ['Lista creada correctamente']];
                echo json_encode($respuesta);
            }
            else {
                //Si hubo errores al crear la lista, responder con errores
                unset($controlador);
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

    //Obtener una lista de usuario específica
    case BaseAPI.'/api/listas/obtener':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Obtener los datos de la solicitud
            $idLista = $_POST['id'];

            //Obtener la información de la lista
            $controlador = new EditarListaController();
            $resultado = $controlador->traerLista($idLista);

            //Si se obtuvo la lista:
            if ($resultado['valido']) {

                //Obtener las imágenes de la lista
                $listaActual = $resultado['respuesta'];
                $imagenesLista = $controlador->traerImagenesLista($listaActual);
                if ($imagenesLista['valido'] && $imagenesLista['respuesta'] != null && count($imagenesLista['respuesta']) > 0)
                    $listaActual->Imagenes = $imagenesLista['respuesta'];

                //Si se pudieron obtener las imágenes:
                if ($imagenesLista['valido']) {

                    //Obtener los productos de la lista
                    $productosLista = $controlador->traerProductosLista($listaActual);
                    if ($productosLista['valido'])
                        $listaActual->Productos = $productosLista['respuesta'];

                    //Desechar la instancia del controlador
                    unset($controlador);

                    //Si se pudieron obtener los productos de la lista, responder con exito
                    if ($productosLista['valido']) {
                        $respuesta = ['exito' => true, 'mensaje' => $listaActual];
                        echo json_encode($respuesta);
                    }
                    //De lo contrario, responder con errores
                    else {
                        $respuesta = ['exito' => false, 'mensaje' => $productosLista['errores']];
                        echo json_encode($respuesta);
                    }
                }
                //De lo contrario, responder con errores
                else {
                    $respuesta = ['exito' => false, 'mensaje' => $imagenesLista['errores']];
                    echo json_encode($respuesta);
                }
            } 
            else {
                unset($controlador);
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

    //Actualizar una lista de usuario específica
    case BaseAPI.'/api/listas/actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Obtener los datos del formulario
            $idLista = $_POST['id'];
            $nombre = $_POST['nombre'];
            $descripcion = $_POST['descripcion'];
            $visibilidad = $_POST['visibilidad'];
            $imagenes = isset($_FILES['imagenes']) ? $_FILES['imagenes'] : null;

            //Crear una instancia del controlador y validar los datos
            $controlador = new EditarListaController();
            $resultado = $controlador->validarDatosEditarLista($idLista, $nombre, $descripcion, $visibilidad);

            //Si la lista se actualizó correctamente:
            if ($resultado['valido']) {

                //Obtener una referencia a la lista actualizada
                $listaActualizada = $controlador->traerLista($idLista)['respuesta'];
                
                //Mandar a eliminar todas las imagenes de la lista 
                $resultado = $controlador->eliminarImagenesLista($listaActualizada);

                //Si se eliminaron las imágenes correctamente, resubir las nuevas imágenes
                if ($resultado['valido'] && $imagenes != null) {
                    $resultado = $controlador->asignarImagenesLista($listaActualizada, $imagenes);
                }

                //Desechar la instancia del controlador
                unset($controlador);

                //Si se subieron las imágenes correctamente, responder con exito la actualización de la lista
                if ($resultado['valido']) {
                    $respuesta = ['exito' => true, 'mensaje' => ['Lista actualizada correctamente']];
                    echo json_encode($respuesta);
                }
                else {
                    $respuesta = ['exito' => false, 'mensaje' => $resultado['errores']];
                    echo json_encode($respuesta);
                }
            } 
            //Si la lista se actualizó correctamente pero no se especificaron imágenes, responder con exito la actualización de la lista
            else if ($resultado['valido']) {
                unset($controlador);
                $respuesta = ['exito' => true, 'mensaje' => ['Lista actualizada correctamente']];
                echo json_encode($respuesta);
            }
            else {
                //Si hubo errores al actualizar la lista, responder con errores
                unset($controlador);
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

    //Agregar un producto a una lista de usuario específica
    case BaseAPI.'/api/listas/agregar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Obtener los datos del formulario
            $idLista = $_POST['idLista'];
            $idProducto = $_POST['idProducto'];

            //Crear una instancia del controlador y validar los datos
            $controlador = new EditarListaController();
            $resultado = $controlador->agregarProductoLista($idLista, $idProducto);
            unset($controlador);

            //Responder con exito o errores según el resultado
            if ($resultado['valido']) {
                $respuesta = ['exito' => true, 'mensaje' => ['Producto agregado a la lista correctamente']];
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

    //Eliminar un producto de una lista de usuario específica
    case BaseAPI.'/api/listas/eliminarProducto':
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

            //Obtener los datos de la solicitud
            $datos = json_decode(file_get_contents('php://input'), true);
            $idLista = $datos['idLista'];
            $idProducto = $datos['idProducto'];

            //Crear una instancia del controlador y validar los datos
            $controlador = new EditarListaController();
            $resultado = $controlador->eliminarProductoLista($idLista, $idProducto);
            unset($controlador);

            //Responder con exito o errores según el resultado
            if ($resultado['valido']) {
                $respuesta = ['exito' => true, 'mensaje' => ['Producto eliminado de la lista correctamente']];
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

    //Eliminar una lista de usuario específica
    case BaseAPI.'/api/listas/eliminar':
        if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

            //Obtener los datos de la solicitud
            $datos = json_decode(file_get_contents('php://input'), true);
            $idLista = $datos['idLista'];

            //Crear una instancia del controlador y validar los datos
            $controlador = new PerfilController();
            $resultado = $controlador->eliminarListaEspecifica($idLista);
            unset($controlador);

            //Responder con exito o errores según el resultado
            if ($resultado['valido']) {
                $respuesta = ['exito' => true, 'mensaje' => ['Lista eliminada correctamente']];
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

        //Listas
    case BaseAPI.'/api/listas/pedidos':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            # code...
            //Obtener datos del servidor
            $usuario = $_POST['usuario'];
            $fechaMin = $_POST['fechaMin'];
            $fechaMax = $_POST['fechaMax'];   
            $categoria = $_POST['categoria'];            

            $controlador = new BusquedaController();
            $resultado = $controlador->obtenerPedidos($usuario, $fechaMin, $fechaMax, $categoria);
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
            else {
                //Método no permitido
                $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
                echo json_encode($respuesta);
            }
            break;

    case BaseAPI.'/api/listas/ventas':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            # code...
            //Obtener datos del servidor
            $usuario = $_POST['usuario'];
            $fechaMin = $_POST['fechaMin'];
            $fechaMax = $_POST['fechaMax'];   
            $categoria = $_POST['categoria'];            

            $controlador = new BusquedaController();
            $resultado = $controlador->obtenerVentas($usuario, $fechaMin, $fechaMax, $categoria);
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
            else {
                //Método no permitido
                $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
                echo json_encode($respuesta);
            }
            break;

    //Carritos
    case BaseAPI.'/api/carrito/agregar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            # code...
            //Obtener datos del servidor
            $idPropietario = $_POST['idPropietario'];
            $terminado = $_POST['terminado'];
            $idProducto = $_POST['idProducto'];   
            $Cantidad = $_POST['Cantidad'];            

            $controlador = new BusquedaController();
            $resultado = $controlador->agregarCarrito($idPropietario, $terminado, $idProducto, $Cantidad);
            unset($controlador);

            //Responder con exito o errores según el resultado
            if ($resultado['valido']) {
                $respuesta = ['exito' => true, 'mensaje' => ['Producto Agregado con exito']];
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

        case BaseAPI.'/api/carrito/agregarPrecio':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                # code...
                //Obtener datos del servidor
                $idPropietario = $_POST['idPropietario'];
                $terminado = $_POST['terminado'];
                $idProducto = $_POST['idProducto'];   
                $Cantidad = $_POST['Cantidad'];
                $Precio = $_POST['Precio']; 
    
                $controlador = new BusquedaController();
                $resultado = $controlador->agregarCarritoCotizacion($idPropietario, $terminado, $idProducto, $Cantidad, $Precio);
                unset($controlador);
    
                //Responder con exito o errores según el resultado
                if ($resultado['valido']) {
                    $respuesta = ['exito' => true, 'mensaje' => ['Producto Agregado con exito']];
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

        case BaseAPI.'/api/carrito/obtener':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                # code...
                //Obtener datos del servidor
                $idPropietario = $_POST['idPropietario'];                        
    
                $controlador = new BusquedaController();
                $resultado = $controlador->obtenerCarrito($idPropietario);
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
        else {
            //Método no permitido
            $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
            echo json_encode($respuesta);
        }
        break;

        case BaseAPI.'/api/carrito/eliminar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                # code...
                //Obtener datos del servidor
                $idPropietario = $_POST['idPropietario']; 
                $idArticulo = $_POST['idArticulo'];                       
    
                $controlador = new BusquedaController();
                $resultado = $controlador->eliminarItemCarrito($idPropietario, $idArticulo);
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
        else {
            //Método no permitido
            $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
            echo json_encode($respuesta);
        }
        break;

        case BaseAPI.'/api/carrito/comprar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                # code...
                //Obtener datos del servidor
                $idPropietario = $_POST['idPropietario'];                      
    
                $controlador = new BusquedaController();
                $resultado = $controlador->comprarCarrito($idPropietario);
                unset($controlador);
    
                //Responder con exito o errores según el resultado
            if ($resultado['valido']) {
                $respuesta = ['exito' => true, 'mensaje' => ['Compra realizada con exito']];
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

    //Obtener productos con los filtros de búsqueda correspondientes
    case BaseAPI.'/api/mensajes/buscar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            # code...
            //Obtener datos del servidor
            $IdEmisor = $_POST['IdEmisor'];

            $controlador = new BusquedaController();
            $resultado = $controlador->obtenerMensajes($IdEmisor);
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
        else {
            //Método no permitido
            $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
            echo json_encode($respuesta);
        }
        break;

        case BaseAPI.'/api/mensajes/enviar':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                # code...
                //Obtener datos del servidor
                $idArticuloEntrante = $_POST['IdArticulo'];
                $idEmisorEntrante = $_POST['IdEmisor'];
                $idReceptorEntrante = $_POST['IdReceptor'];  
                $Contenido = $_POST['Contenido'];   
    
                $controlador = new BusquedaController();
                $resultado = $controlador->crearMensaje($idArticuloEntrante, $idEmisorEntrante, $idReceptorEntrante, $Contenido);
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
            else {
                //Método no permitido
                $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
                echo json_encode($respuesta);
            }
            break;

            case BaseAPI.'/api/cotizacion/crear':
                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    # code...
                    //Obtener datos del servidor
                    $IdProducto = $_POST['IdProducto'];
                    $Nombre = $_POST['Nombre'];
                    $Descripcion = $_POST['Descripcion'];  
                    $Precio = $_POST['Precio'];   
                    $Estado = $_POST['Estado'];   
                    $Cantidad = $_POST['Cantidad']; 
        
                    $controlador = new BusquedaController();
                    $resultado = $controlador->InsertarCotizacion($IdProducto, $Nombre, $Descripcion, $Precio, $Estado, $Cantidad);
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
                else {
                    //Método no permitido
                    $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
                    echo json_encode($respuesta);
                }
                break;

                case BaseAPI.'/api/cotizacion/cambiarPrecio':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        # code...
                        //Obtener datos del servidor
                        $IdProducto = $_POST['IdProducto'];
                        $Precio = $_POST['Precio'];   
                        $Cantidad = $_POST['Cantidad'];   
            
                        $controlador = new BusquedaController();
                        $resultado = $controlador->CambiarPrecioCotizacion($IdProducto,  $Precio, $Cantidad);
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
                    else {
                        //Método no permitido
                        $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
                        echo json_encode($respuesta);
                    }
                    break;

                case BaseAPI.'/api/cotizacion/terminar':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        # code...
                        //Obtener datos del servidor
                        $IdProducto = $_POST['IdProducto'];                         
            
                        $controlador = new BusquedaController();
                        $resultado = $controlador->TerminarCotizacion($IdProducto);
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
                    else {
                        //Método no permitido
                        $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
                        echo json_encode($respuesta);
                    }
                    break;

                case BaseAPI.'/api/cotizacion/obtener':
                    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                        # code...
                        //Obtener datos del servidor
                        $IdProducto = $_POST['IdProducto'];                        
            
                        $controlador = new BusquedaController();
                        $resultado = $controlador->ObtenerCotizacionId($IdProducto);
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
                    else {
                        //Método no permitido
                        $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
                        echo json_encode($respuesta);
                    }
                    break;

    //Obtener productos con los filtros de búsqueda correspondientes
    case BaseAPI.'/api/busqueda/buscar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            # code...
            //Obtener datos del servidor
            $busqueda = $_POST['busqueda'];
            $precioMin = $_POST['precioMin'];
            $precioMax = $_POST['precioMax'];
            $categoria = $_POST['categoria'];
            $estrellas = $_POST['estrellas'];
            $popular = $_POST['popular'];

            $controlador = new BusquedaController();
            $resultado = $controlador->obtenerBusqueda($busqueda, $precioMin, $precioMax, $categoria, $estrellas, $popular);
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
        else {
            //Método no permitido
            $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
            echo json_encode($respuesta);
        }
        break;

    //Obtener productos con un id específico
    case BaseAPI.'/api/busqueda/buscarId':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            # code...
            //Obtener datos del servidor
            $busqueda = $_POST['busqueda'];
            
            $controlador = new BusquedaController();
            $resultado = $controlador->obtenerBusquedaId($busqueda);

            //Si el resultado es valido:
            if ($resultado['valido']) {

                $producto = $resultado['respuesta'];

                //Obtener las imágenes del producto
                $imagenesProducto = $controlador->traerImagenesProducto($producto);
                if ($imagenesProducto['valido'])
                    $producto->Imagenes = $imagenesProducto['respuesta'];

                //Si se pudieron obtener las imágenes del producto:
                if ($imagenesProducto['valido']) {

                    //Obtener los videos del producto
                    $videosProducto = $controlador->traerVideosProducto($producto);
                    if ($videosProducto['valido'])
                        $producto->Videos = $videosProducto['respuesta'];

                        //Si se pudieron obtener los videos del producto:
                        if ($videosProducto['valido']) {

                            //Obtener los comentarios del producto
                            $comentariosProducto = $controlador->traerComentariosProducto($producto);
                            if ($comentariosProducto['valido'])
                                $producto->Comentarios = $comentariosProducto['respuesta'];

                            //Desechar la instancia del controlador
                            unset($controlador);

                            //Si se pudieron obtener los comentarios del producto, responder con exito
                            if ($comentariosProducto['valido']) {
                                $respuesta = ['exito' => true, 'mensaje' => $producto];
                                echo json_encode($respuesta);
                            }
                            //De lo contrario, responder con errores
                            else {
                                $respuesta = ['exito' => false, 'mensaje' => $comentariosProducto['errores']];
                                echo json_encode($respuesta);
                            }
                        }
                        //De lo contrario, responder con errores
                        else {
                            $respuesta = ['exito' => false, 'mensaje' => $videosProducto['errores']];
                            echo json_encode($respuesta);
                        }
                }
                //De lo contrario, responder con errores
                else {
                    unset($controlador);
                    $respuesta = ['exito' => false, 'mensaje' => $imagenesProducto['errores']];
                    echo json_encode($respuesta);
                }
            } 
            else {
                unset($controlador);
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

    //Creación de productos
    case BaseAPI.'/api/productos/creacion':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Obtener los datos del formulario
            $nombreProducto = $_POST['nombre'];
            $descripcionProducto = $_POST['descripcion'];
            $modoProducto = $_POST['modo'];
            $precioProducto = $_POST['precio'];
            $cantidadProducto = $_POST['cantidad'];
            $categoriaProducto = $_POST['categoria'];

            $imagenesProducto = isset($_FILES['imagenes']) ? $_FILES['imagenes'] : null;
            $videosProducto = isset($_FILES['videos']) ? $_FILES['videos'] : null;
            
            //Crear una instancia del controlador y validar los datos para crear el producto
            $controlador = new NuevaPublicacionController();
            $resultado = $controlador->validarDatosNuevoProducto($nombreProducto, $descripcionProducto, $modoProducto, $precioProducto, $cantidadProducto);

            //Si el producto se creó correctamente:
            if ($resultado['valido']) {
                
                //Categorizar el producto
                $productoCreado = $resultado['respuesta'];
                $resultado = $controlador->asignarCategoriaProducto($productoCreado, $categoriaProducto);

                //Si se pudo categorizar el producto correctamente:
                if ($resultado['valido']) {

                    //Subir las imágenes del producto
                    $resultado = $controlador->asignarImagenesProducto($productoCreado, $imagenesProducto);

                    //Si se subieron las imágenes correctamente:
                    if ($resultado['valido']) {

                        //Subir los videos del producto
                        $resultado = $controlador->asignarVideosProducto($productoCreado, $videosProducto);

                        //Si se subieron los videos correctamente, responder con exito la creación del producto
                        if ($resultado['valido']) {
                            $respuesta = ['exito' => true, 'mensaje' => ['Producto creado correctamente']];
                        }
                        //Si hubo errores al subir los videos, responder con errores, pero permitir continuar
                        else
                            $respuesta = ['exito' => false, 'mensaje' => $resultado['errores'], 'continuar' => 1];
                    }
                    //Si hubo errores al subir las imágenes, responder con errores, pero permitir continuar
                    else
                        $respuesta = ['exito' => false, 'mensaje' => $resultado['errores'], 'continuar' => 1];
                }
                //Si hubo errores al categorizar el producto, responder con errores pero permitir continuar
                else
                    $respuesta = ['exito' => false, 'mensaje' => $resultado['errores'], 'continuar' => 1];
            }
            //Si hubo errores al crear el producto, responder con errores y no permitir continuar
            else
                $respuesta = ['exito' => false, 'mensaje' => $resultado['errores'], 'continuar' => 0];
            
            //Desechar la instancia del controlador y responder con exito o errores según el resultado
            unset($controlador);
            echo json_encode($respuesta);
        } 
        else {
            //Método no permitido
            $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
            echo json_encode($respuesta);
        }
        break;

    //Obtener productos interesantes
    case BaseAPI.'/api/productos/interesantes':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            //Obtener la información de los productos interesantes
            $controlador = new DashboardController();
            $resultado = $controlador->obtenerProductosInteresantes();

            //Si el resultado es valido:
            if ($resultado['valido']) {

                //Si se encontraron productos interesantes:
                if (count($resultado['respuesta']) > 0) {

                    //Obtener las imágenes de cada producto
                    foreach ($resultado['respuesta'] as $producto) {

                        $imagenesProducto = $controlador->traerImagenesProducto($producto);
                        if ($imagenesProducto['valido'])
                            $producto->Imagenes = $imagenesProducto['respuesta'];
                    }

                    //Desechar la instancia del controlador
                    unset($controlador);

                    //Si se pudieron obtener las imágenes de los productos, responder con exito
                    if ($imagenesProducto['valido']) {
                        $respuesta = ['exito' => true, 'mensaje' => $resultado['respuesta']];
                        echo json_encode($respuesta);
                    } 
                    //De lo contrario, responder con errores
                    else {
                        $respuesta = ['exito' => false, 'mensaje' => $imagenesProducto['errores']];
                        echo json_encode($respuesta);
                    }
                }
                //Si no se encontraron productos interesantes, responder con exito pero con un mensaje vacío
                else {
                    unset($controlador);
                    $respuesta = ['exito' => true, 'mensaje' => []];
                    echo json_encode($respuesta);
                } 
            } 
            else {
                unset($controlador);
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

    //Obtener productos sin aprobar
    case BaseAPI.'/api/productos/pendientes':
        if ($_SERVER['REQUEST_METHOD'] === 'GET') {

            //Obtener la información de los productos sin aprobar
            $controlador = new AprobacionController();
            $resultado = $controlador->traerProductosSinAprobacion();

            //Si el resultado es valido:
            if ($resultado['valido']) {

                //Si se encontraron productos sin aprobar:
                if (count($resultado['respuesta']) > 0) {

                    //Obtener las imágenes de cada producto
                    foreach ($resultado['respuesta'] as $producto) {

                        $imagenesProducto = $controlador->traerImagenesProducto($producto);
                        if ($imagenesProducto['valido'])
                            $producto->Imagenes = $imagenesProducto['respuesta'];
                    }

                    //Desechar la instancia del controlador
                    unset($controlador);

                    //Si se pudieron obtener las imágenes de los productos, responder con exito
                    if ($imagenesProducto['valido']) {
                        $respuesta = ['exito' => true, 'mensaje' => $resultado['respuesta']];
                        echo json_encode($respuesta);
                    } 
                    //De lo contrario, responder con errores
                    else {
                        $respuesta = ['exito' => false, 'mensaje' => $imagenesProducto['errores']];
                        echo json_encode($respuesta);
                    }
                }
                //Si no se encontraron productos sin aprobar, responder con exito pero con un mensaje vacío
                else {
                    unset($controlador);
                    $respuesta = ['exito' => true, 'mensaje' => []];
                    echo json_encode($respuesta);
                } 
            } 
            else {
                unset($controlador);
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

    //Aprobar productos
    case BaseAPI.'/api/productos/aprobacion':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Obtener los datos de la solicitud
            $idProducto = $_POST['idProducto'];
            $aprobacion = $_POST['aprobacion'] == 'true' ? true : false;

            //Crear una instancia del controlador y aprobar el producto
            $controlador = new AprobacionController();
            $resultado = $controlador->aprobacionRechazoProducto($idProducto, $aprobacion);
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
        else {
            //Método no permitido
            $respuesta = ['exito' => false, 'mensaje' => ['Método no permitido']];
            echo json_encode($respuesta);
        }
        break;

    //Comentar productos
    case BaseAPI.'/api/comentarios/creacion':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            //Obtener los datos de la solicitud
            $idProducto = $_POST['idProducto'];
            $titulo = $_POST['titulo'];
            $contenido = $_POST['contenido'];
            $puntuacion = $_POST['puntuacion'];

            //Crear una instancia del controlador y crear el comentario del producto
            $controlador = new PaginaProductoController();
            $resultado = $controlador->crearComentarioProducto($idProducto, $titulo, $contenido, $puntuacion);
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