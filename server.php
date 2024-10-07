<?php
// Asegúrate de que estás en la raíz de tu proyecto donde está el directorio 'vendor'
require 'vendor/autoload.php'; // Cargar autoloader de Composer

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        // Agrega la nueva conexión a la lista de clientes
        $this->clients->attach($conn);
        echo "Nueva conexión: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        // Envía el mensaje a todos los clientes
        foreach ($this->clients as $client) {
            if ($from !== $client) { // No enviar el mensaje al emisor
                $client->send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        // Elimina la conexión de la lista de clientes
        $this->clients->detach($conn);
        echo "Conexión {$conn->resourceId} ha cerrado\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "Ha ocurrido un error: {$e->getMessage()}\n";
        $conn->close();
    }
}

// Iniciar servidor WebSocket
$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080 // Puerto
);

$server->run();
