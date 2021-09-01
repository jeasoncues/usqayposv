<?php

require_once('include/SuperClass.php');

class servicio extends SuperClass {

    private $inputvars = array();
    private $inputname = 'servicio';

    function __construct($id = NULL, $nombre = NULL, $precio_venta = NULL, $incluye_impuesto = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["nombre"] = $nombre;
        $this->inputvars["precio_venta"] = $precio_venta;
        $this->inputvars["incluye_impuesto"] = $incluye_impuesto;
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

    public function setPrecioVenta($newval) {
        parent::setVar("precio_venta", $newval);
    }

    public function getPrecioVenta() {
        return parent::getVar("precio_venta");
    }

    public function setIncluyeImpuesto($newval) {
        parent::setVar("incluye_impuesto", $newval);
    }

    public function getIncluyeImpuesto() {
        return parent::getVar("incluye_impuesto");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>