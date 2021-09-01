<?php

require_once('include/SuperClass.php');

class venta_impuesto extends SuperClass {

    private $inputvars = array();
    private $inputname = 'venta_impuesto';

    function __construct($id = NULL, $id_venta = NULL, $id_impuesto = NULL, $monto = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_venta"] = $id_venta;
        $this->inputvars["id_impuesto"] = $id_impuesto;
        $this->inputvars["monto"] = $monto;
        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setIdVenta($newval) {
        parent::setVar("id_venta", $newval);
    }

    public function getIdVenta() {
        return parent::getVar("id_venta");
    }

    public function setIdImpuesto($newval) {
        parent::setVar("id_impuesto", $newval);
    }

    public function getIdImpuesto() {
        return parent::getVar("id_impuesto");
    }

    public function setMonto($newval) {
        parent::setVar("monto", $newval);
    }

    public function getMonto() {
        return parent::getVar("monto");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>