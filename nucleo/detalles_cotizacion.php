<?php

require_once('include/SuperClass.php');

class detalles_cotizacion extends SuperClass {

    private $inputvars = array();
    private $inputname = 'detalles_cotizacion';

    function __construct($id = NULL, $id_coti = NULL, $id_producto = NULL,$cantidad = NULL, $precio = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_coti"] = $id_coti;
        $this->inputvars["id_producto"] = $id_producto;
        $this->inputvars["cantidad"] = $cantidad;
        $this->inputvars["precio"] = $precio;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setIdVenta($newval) {
        parent::setVar("id_coti", $newval);
    }

    public function getIdVenta() {
        return parent::getVar("id_coti");
    }

    public function setIdProducto($newval) {
        parent::setVar("id_producto", $newval);
    }

    public function getIdProducto() {
        return parent::getVar("id_producto");
    }

    public function setCantidad($newval) {
        parent::setVar("cantidad", $newval);
    }

    public function getCantidad() {
        return parent::getVar("cantidad");
    }

    public function setPrecio($newval) {
        parent::setVar("precio", $newval);
    }

    public function getPrecio() {
        return parent::getVar("precio");
    }


}

?>