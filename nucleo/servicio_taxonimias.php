<?php

require_once('include/SuperClass.php');

class servicio_taxonimias extends SuperClass {

    private $inputvars = array();
    private $inputname = 'servicio_taxonimias';

    function __construct($id = NULL, $id_servicio = NULL, $id_taxonomias = NULL, $valor = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_servicio"] = $id_servicio;
        $this->inputvars["id_taxonomias"] = $id_taxonomias;
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

    public function setIdServicio($newval) {
        parent::setVar("id_servicio", $newval);
    }

    public function getIdServicio() {
        return parent::getVar("id_servicio");
    }

    public function setIdTaxonomias($newval) {
        parent::setVar("id_taxonomias", $newval);
    }

    public function getIdTaxonomias() {
        return parent::getVar("id_taxonomias");
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