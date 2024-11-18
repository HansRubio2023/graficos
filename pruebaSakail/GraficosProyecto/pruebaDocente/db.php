<?php
class conexion {
    private $servername = "sakaionline.unap.cl";
    private $username = "sakai_ro";
    private $password = "Za4vwB3haVTBGbnW";
    private $dbname = "sakail";
    public $conexion;

    public function verificarConexion() {
        $this->conexion = new mysqli($this->servername, $this->username, $this->password, $this->dbname);
        if ($this->conexion->connect_error) {
            echo "Error de conexión: " . $this->conexion->connect_error;
        } else {
          /*   echo "Conexión exitosa"; */
        }
    }
}

if (isset($_POST['verificar'])) {
    $conexion = new conexion();
    $conexion->verificarConexion();
} 

/* boton para verificar la conexion a base de datos Sakail */

/*  echo '<form action="" method="post">';
echo '<input type="submit" name="verificar" value="Verificar Conexión">';
echo '</form>'; */

?>