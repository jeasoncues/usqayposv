<?php

require_once('include/SuperClass.php');

class plato extends SuperClass {

    private $inputvars = array();
    private $inputname = 'plato';

    function __construct($id = NULL, $id_producto = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;        
        $this->inputvars["id_producto"] = $id_producto;        
        $this->inputvars["estado_fila"] = $estado_fila;
        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setIdProducto($newval) {
        parent::setVar("id_producto", $newval);
    }

    public function getIdProducto() {
        return parent::getVar("id_producto");
    }
    

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>