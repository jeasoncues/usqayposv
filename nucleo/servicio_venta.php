<?php

require_once('include/SuperClass.php');

class servicio_venta extends SuperClass {

    private $inputvars = array();
    private $inputname = 'servicio_venta';

    function __construct($id = NULL, $id_venta = NULL, $id_servicio = NULL, $precio = NULL, $cantidad = NULL, $total = NULL, $estado = NULL, $tiempo_iniciado = NULL, $tiempo_cerrado = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_venta"] = $id_venta;
        $this->inputvars["id_servicio"] = $id_servicio;
        $this->inputvars["precio"] = $precio;
        $this->inputvars["cantidad"] = $cantidad;
        $this->inputvars["total"] = $total;
        $this->inputvars["estado"] = $estado;
        $this->inputvars["tiempo_iniciado"] = $tiempo_iniciado;
        $this->inputvars["tiempo_cerrado"] = $tiempo_cerrado;
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

    public function setIdServicio($newval) {
        parent::setVar("id_servicio", $newval);
    }

    public function getIdServicio() {
        return parent::getVar("id_servicio");
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

    public function setEstado($newval) {
        parent::setVar("estado", $newval);
    }

    public function getEstado() {
        return parent::getVar("estado");
    }

    public function setTiempoIniciado($newval) {
        parent::setVar("tiempo_iniciado", $newval);
    }

    public function getTiempoIniciado() {
        return parent::getVar("tiempo_iniciado");
    }

    public function setTiempoCerrado($newval) {
        parent::setVar("tiempo_cerrado", $newval);
    }

    public function getTiempoCerrado() {
        return parent::getVar("tiempo_cerrado");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>