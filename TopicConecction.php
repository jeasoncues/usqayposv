<?php

/* Clase Conexion Maestra
 * Por Gino Lluen 05 de julio del 2012
 * Usada en KamiCRUD desde 12 de mayo del 2013
 * Actualizada a Mysqli (al fin) 08 de diciembre del 2016
 */

class TopicConection {

    private $abierta;
    private $host = "localhost";
    private $usuario = "root";
    private $contra = "";
    private $base = "pos";

    public function getConnection(){
        return $this->abierta;
    }

    public function __construct($host = "localhost", $usuario = "root", $contra = "", $base = "pos") 
    {
        $this->abierta = new mysqli($host, $usuario, $contra, $base);        
        $this->abierta->set_charset("utf8");
        if ($this->abierta->connect_errno) {
            die("Mysql error csm : (" . $this->abierta->mysqli_connect_errno() . ") " . $this->abierta->mysqli_connect_error());
        }
    }

    function __destruct() {
        $this->abierta->close();
    }
}
