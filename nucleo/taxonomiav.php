<?php

require_once('include/SuperClass.php');

class taxonomiav extends SuperClass {

    private $inputvars = array();
    private $inputname = 'taxonomiav';

    function __construct($id = NULL, $padre = NULL, $nombre = NULL, $tipo_valor = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["padre"] = $padre;
        $this->inputvars["nombre"] = $nombre;
        $this->inputvars["tipo_valor"] = $tipo_valor;
        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setPadre($newval) {
        parent::setVar("padre", $newval);
    }

    public function getPadre() {
        return parent::getVar("padre");
    }

    public function setNombre($newval) {
        parent::setVar("nombre", $newval);
    }

    public function getNombre() {
        return parent::getVar("nombre");
    }

    public function setTipoValor($newval) {
        parent::setVar("tipo_valor", $newval);
    }

    public function getTipoValor() {
        return parent::getVar("tipo_valor");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>