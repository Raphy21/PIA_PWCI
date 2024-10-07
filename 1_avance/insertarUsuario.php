<?php
header('Content-Type: application/json');

// Datos de conexión
$host = "localhost";
$baseDatos = "Proyecto_BDM";
$usuario = "root";
$contrasena = "";
$puerto = "3306";

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $baseDatos, $puerto);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(array("exito" => false, "error" => "Conexión fallida: " . $conn->connect_error)));
}

// Verificar que se reciban los datos
if (isset($_POST['usuario']) && isset($_POST['correo']) && isset($_POST['contrasena'])) {
    $nombreUsuario = $conn->real_escape_string($_POST['usuario']);
    $correo = $conn->real_escape_string($_POST['correo']);
    $contrasena = $conn->real_escape_string($_POST['contrasena']);
    $contrasena = $conn->real_escape_string($_POST['contrasena']);

    // Verificar si el usuario o correo ya existen en la base de datos
    $consulta = "SELECT * FROM registro WHERE usuario = '$nombreUsuario' OR correo = '$correo'";
    $resultado = $conn->query($consulta);

    if ($resultado->num_rows > 0) {
        echo json_encode(array("exito" => false, "error" => "El nombre de usuario o el correo ya están en uso."));
    } else {
        // Insertar el nuevo usuario
        $sql = "INSERT INTO registro (usuario, correo, pass) VALUES ('$nombreUsuario', '$correo', '$contrasena')";
        
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array("exito" => true, "mensaje" => "Usuario registrado con éxito."));
        } else {
            echo json_encode(array("exito" => false, "error" => "Error al registrar el usuario: " . $conn->error));
        }
    }
} else {
    echo json_encode(array("exito" => false, "error" => "Datos incompletos."));
}

// Cerrar conexión
$conn->close();
?>
