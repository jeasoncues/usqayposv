<?php

require_once('include/SuperClass.php');

class receta extends SuperClass {

    private $inputvars = array();
    private $inputname = 'receta';

    function __construct($id = NULL, $id_plato = NULL, $id_insumo = NULL, $cantidad = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;        
        $this->inputvars["id_plato"] = $id_plato;
        $this->inputvars["id_insumo"] = $id_insumo;
        $this->inputvars["cantidad"] = $cantidad; 
        $this->inputvars["estado_fila"] = $estado_fila;
        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setIdPlato($newval) {
        parent::setVar("id_plato", $newval);
    }

    public function getIdPlato() {
        return parent::getVar("id_plato");
    }

    public function setIdInsumo($newval) {
        parent::setVar("id_insumo", $newval);
    }

    public function getIdInsumo() {
        return parent::getVar("id_insumo");
    }

    public function setCantidad($newval) {
        parent::setVar("cantidad", $newval);
    }

    public function getCantidad() {
        return parent::getVar("cantidad");
    }
    
    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>