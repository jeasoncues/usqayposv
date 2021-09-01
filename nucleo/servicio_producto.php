<?php

require_once('include/SuperClass.php');

class servicio_producto extends SuperClass {

    private $inputvars = array();
    private $inputname = 'servicio_producto';

    function __construct($id = NULL, $id_servicio = NULL, $id_producto = NULL, $cantidad = NULL, $id_almacen = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_servicio"] = $id_servicio;
        $this->inputvars["id_producto"] = $id_producto;
        $this->inputvars["cantidad"] = $cantidad;
        $this->inputvars["id_almacen"] = $id_almacen;
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

    public function setIdAlmacen($newval) {
        parent::setVar("id_almacen", $newval);
    }

    public function getIdAlmacen() {
        return parent::getVar("id_almacen");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>