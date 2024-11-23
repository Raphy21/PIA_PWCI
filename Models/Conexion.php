<?php
class Conexion {
    //Atributos
    private $host = "s9xpbd61ok2i7drv.cbetxkdyhwsb.us-east-1.rds.amazonaws.com";
    private $baseDatos = "omggy318wf15rtc3";
    private $usuario = "w61uabsrpaswba47";
    private $contrasena = "zgug8l6g0pj2cwrn";
    private $puerto = "3306";
    
    private static $instancia = null;
    private $conexionAbierta = null;

    //Acceso a la instancia de la conexión
    private function __construct() { }
    public static function instanciaConexion() {
        if (!self::$instancia) {
            self::$instancia = new Conexion();
        }

        return self::$instancia;
    }

    //Métodos
    public function abrirConexion() {

        //Si aún no se ha abierto una conexión, abrir una nueva
        if (!$this->conexionAbierta) {
            $this->conexionAbierta = new mysqli($this->host, $this->usuario, $this->contrasena, $this->baseDatos, $this->puerto);

            //Si hay un error en la conexión, mostrarlo
            if ($this->conexionAbierta->connect_error)
                die("Error de conexión: " . $this->conexionAbierta->connect_error);
        }
    }

    public function prepararConsulta($consulta) {

        //Si hay una conexión abierta, preparar la consulta dada por parámetro
        if ($this->conexionAbierta) {
            $preparacion = $this->conexionAbierta->prepare($consulta);

            //Si hay un error en la preparación, mostrarlo
            if ($preparacion === false)
                die("Error al preparar la consulta: " . $this->conexionAbierta->error);

            return $preparacion;
        }
        else
            return null;
    }

    public function cerrarConexion() {

        //Si hay una conexión abierta, cerrarla
        if ($this->conexionAbierta) {
            $this->conexionAbierta->close();
            $this->conexionAbierta = null;
        }
    } 
}
?>
