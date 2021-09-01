<?php

require_once('include/SuperClass.php');

class taxonomiav_valor extends SuperClass {

    private $inputvars = array();
    private $inputname = 'taxonomiav_valor';

    function __construct($id = NULL, $id_taxonomiav = NULL, $valor = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_taxonomiav"] = $id_taxonomiav;
        $this->inputvars["valor"] = $valor;
        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setIdTaxonomiav($newval) {
        parent::setVar("id_taxonomiav", $newval);
    }

    public function getIdTaxonomiav() {
        return parent::getVar("id_taxonomiav");
    }

    public function setValor($newval) {
        parent::setVar("valor", $newval);
    }

    public function getValor() {
        return parent::getVar("valor");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>