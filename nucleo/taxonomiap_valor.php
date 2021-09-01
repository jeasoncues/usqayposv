<?php

require_once('include/SuperClass.php');

class taxonomiap_valor extends SuperClass {

    private $inputvars = array();
    private $inputname = 'taxonomiap_valor';

    function __construct($id = NULL, $id_taxonomiap = NULL, $valor = NULL, $padre = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_taxonomiap"] = $id_taxonomiap;
        $this->inputvars["valor"] = $valor;
        $this->inputvars["padre"] = $padre;
        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setIdTaxonomiap($newval) {
        parent::setVar("id_taxonomiap", $newval);
    }

    public function getIdTaxonomiap() {
        return parent::getVar("id_taxonomiap");
    }

    public function setValor($newval) {
        parent::setVar("valor", $newval);
    }

    public function getValor() {
        return parent::getVar("valor");
    }
    
    public function setPadre($newval) {
        parent::setVar("padre", $newval);
    }

    public function getPadre() {
        return parent::getVar("padre");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>