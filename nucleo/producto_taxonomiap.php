<?php

require_once('include/SuperClass.php');

class producto_taxonomiap extends SuperClass {

    private $inputvars = array();
    private $inputname = 'producto_taxonomiap';

    function __construct($id = NULL, $id_producto = NULL, $id_taxonomiap = NULL, $valor = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_producto"] = $id_producto;
        $this->inputvars["id_taxonomiap"] = $id_taxonomiap;
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

    public function setIdProducto($newval) {
        parent::setVar("id_producto", $newval);
    }

    public function getIdProducto() {
        return parent::getVar("id_producto");
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

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>