<?php

require_once('include/SuperClass.php');

class venta_medio_pago extends SuperClass {

    private $inputvars = array();
    private $inputname = 'venta_medio_pago';

    function __construct($id = NULL, $id_venta = NULL, $medio = NULL, $monto = NULL, $vuelto = NULL, $moneda = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_venta"] = $id_venta;
        $this->inputvars["medio"] = $medio;
        $this->inputvars["monto"] = $monto;
        $this->inputvars["vuelto"] = $vuelto;
        $this->inputvars["moneda"] = $moneda;
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

    public function setMedio($newval) {
        parent::setVar("medio", $newval);
    }

    public function getMedio() {
        return parent::getVar("medio");
    }

    public function setMonto($newval) {
        parent::setVar("monto", $newval);
    }

    public function getMonto() {
        return parent::getVar("monto");
    }

    public function setMoneda($newval) {
        parent::setVar("moneda", $newval);
    }
    
    public function getVuelto() {
        return parent::getVar("vuelto");
    }

    public function setVuelto($newval) {
        parent::setVar("vuelto", $newval);
    }

    public function getMoneda() {
        return parent::getVar("moneda");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>