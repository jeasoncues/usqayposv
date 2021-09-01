<?php

require_once('include/SuperClass.php');

class producto_venta extends SuperClass {

    private $inputvars = array();
    private $inputname = 'producto_venta';

    function __construct($id = NULL, $id_venta = NULL, $id_producto = NULL, $precio = NULL, $cantidad = NULL, $total = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_venta"] = $id_venta;
        $this->inputvars["id_producto"] = $id_producto;
        $this->inputvars["precio"] = $precio;
        $this->inputvars["cantidad"] = $cantidad;
        $this->inputvars["total"] = $total;
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

    public function setIdProducto($newval) {
        parent::setVar("id_producto", $newval);
    }

    public function getIdProducto() {
        return parent::getVar("id_producto");
    }

    public function setPrecio($newval) {
        parent::setVar("precio", $newval);
    }

    public function getPrecio() {
        return parent::getVar("precio");
    }

    public function setCantidad($newval) {
        parent::setVar("cantidad", $newval);
    }

    public function getCantidad() {
        return parent::getVar("cantidad");
    }

    public function setTotal($newval) {
        parent::setVar("total", $newval);
    }

    public function getTotal() {
        return parent::getVar("total");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>