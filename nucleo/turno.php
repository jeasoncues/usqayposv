<?php

require_once('include/SuperClass.php');

class turno extends SuperClass {

    private $inputvars = array();
    private $inputname = 'turno';

    function __construct($id = NULL, $nombre = NULL, $inicio = NULL, $fin = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["nombre"] = $nombre;
        $this->inputvars["inicio"] = $inicio;
        $this->inputvars["fin"] = $fin;
        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setNombre($newval) {
        parent::setVar("nombre", $newval);
    }

    public function getNombre() {
        return parent::getVar("nombre");
    }

    public function setInicio($newval) {
        parent::setVar("inicio", $newval);
    }

    public function getInicio() {
        return parent::getVar("inicio");
    }

    public function setFin($newval) {
        parent::setVar("fin", $newval);
    }

    public function getFin() {
        return parent::getVar("fin");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>