<?php

require_once('include/SuperClass.php');

class materia_prima extends SuperClass {

    private $inputvars = array();
    private $inputname = 'materia_prima';

    function __construct($id = NULL, $id_almacen_origen = NULL, $id_producto_origen = NULL, $id_producto_destino = NULL, $id_almacen_destino = NULL, $cantidad = NULL, $merma = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_almacen_origen"] = $id_almacen_origen;
        $this->inputvars["id_producto_origen"] = $id_producto_origen;
        $this->inputvars["id_producto_destino"] = $id_producto_destino;
        $this->inputvars["id_almacen_destino"] = $id_almacen_destino;
        $this->inputvars["cantidad"] = $cantidad;
        $this->inputvars["merma"] = $merma;
        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setIdAlmacenOrigen($newval) {
        parent::setVar("id_almacen_origen", $newval);
    }

    public function getIdAlmacenOrigen() {
        return parent::getVar("id_almacen_origen");
    }

    public function setIdProductoOrigen($newval) {
        parent::setVar("id_producto_origen", $newval);
    }

    public function getIdProductoOrigen() {
        return parent::getVar("id_producto_origen");
    }

    public function setIdProductoDestino($newval) {
        parent::setVar("id_producto_destino", $newval);
    }

    public function getIdProductoDestino() {
        return parent::getVar("id_producto_destino");
    }

    public function setIdAlmacenDestino($newval) {
        parent::setVar("id_almacen_destino", $newval);
    }

    public function getIdAlmacenDestino() {
        return parent::getVar("id_almacen_destino");
    }

    public function setCantidad($newval) {
        parent::setVar("cantidad", $newval);
    }

    public function getCantidad() {
        return parent::getVar("cantidad");
    }

    public function setMerma($newval) {
        parent::setVar("merma", $newval);
    }

    public function getMerma() {
        return parent::getVar("merma");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>