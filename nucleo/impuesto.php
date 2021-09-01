<?php

require_once('include/SuperClass.php');

class impuesto extends SuperClass {

    private $inputvars = array();
    private $inputname = 'impuesto';

    function __construct($id = NULL, $nombre = NULL, $valor = NULL, $tipo = NULL, $cargo = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["nombre"] = $nombre;
        $this->inputvars["valor"] = $valor;
        $this->inputvars["tipo"] = $tipo;
        $this->inputvars["cargo"] = $cargo;
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

    public function setValor($newval) {
        parent::setVar("valor", $newval);
    }

    public function getValor() {
        return parent::getVar("valor");
    }

    public function setTipo($newval) {
        parent::setVar("tipo", $newval);
    }

    public function getTipo() {
        return parent::getVar("tipo");
    }

    public function setCargo($newval) {
        parent::setVar("cargo", $newval);
    }

    public function getCargo() {
        return parent::getVar("cargo");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>