<?php
header('Content-Type: application/json');

// Datos de conexión
$host = "localhost";
$baseDatos = "Proyecto_BDM";
$usuario = "root";
$contrasena = "";
$puerto = "3307";

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $baseDatos, $puerto);

// Verificar conexión
if ($conn->connect_error) {
    die(json_encode(array("error" => "Conexión fallida: " . $conn->connect_error)));
}

// Consultar la lista de usuarios y correos electrónicos
$sql = "SELECT correo, usuario FROM registroUsuario";
$result = $conn->query($sql);

$usuarios = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $usuarios[] = $row;
    }
}

// Cerrar conexión
$conn->close();

// Devolver los datos en formato JSON
echo json_encode($usuarios);
?>
