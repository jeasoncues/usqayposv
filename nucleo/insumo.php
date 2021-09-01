<?php

require_once('include/SuperClass.php');

class insumo extends SuperClass {

    private $inputvars = array();
    private $inputname = 'insumo';

    function __construct($id = NULL, $id_padre = NULL, $id_producto = NULL, $id_unidad_medida_insumo_porcion = NULL, 
    $valor_insumo = NULL,
    $valor_porcion = NULL,
    $conversion = NULL,
    $descripcion = NULL,
    $estado_fila = NULL
    ) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_padre"] = $id_padre;        
        $this->inputvars["id_producto"] = $id_producto;
        $this->inputvars["id_unidad_medida_insumo_porcion"] = $id_unidad_medida_insumo_porcion;
        $this->inputvars["valor_insumo"] = $valor_insumo;
        $this->inputvars["valor_porcion"] = $valor_porcion;
        $this->inputvars["conversion"] = $conversion;
        $this->inputvars["descripcion"] = $descripcion;
        $this->inputvars["estado_fila"] = $estado_fila;
        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setIdPadre($newval) {
        parent::setVar("id_padre", $newval);
    }

    public function getIdPadre() {
        return parent::getVar("id_padre");
    }

    public function setIdUnidadMedidadInsumoPorcion($newval) {
        parent::setVar("id_unidad_medida_insumo_porcion", $newval);
    }

    public function getIdUnidadMedidadInsumoPorcion() {
        return parent::getVar("id_unidad_medida_insumo_porcion");
    }

    public function setValorInsumo($newval) {
        parent::setVar("valor_insumo", $newval);
    }

    public function getValorInsumo() {
        return parent::getVar("valor_insumo");
    }

    public function setValorPorcion($newval) {
        parent::setVar("valor_porcion", $newval);
    }

    public function getValorPorcion() {
        return parent::getVar("valor_porcion");
    }

    public function setDescripcion($newval) {
        parent::setVar("descripcion", $newval);
    }

    public function getDescripcion() {
        return parent::getVar("descripcion");
    }


    public function setConversion($newval) {
        parent::setVar("conversion", $newval);
    }

    public function getConversion() {
        return parent::getVar("conversion");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>