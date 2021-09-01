<?php

require_once('include/SuperClass.php');

class venta extends SuperClass {

    private $inputvars = array();
    private $inputname = 'venta';

    function __construct($id = NULL, $subtotal = NULL, $total_impuestos = NULL, $total = NULL, $tipo_comprobante = NULL, $fecha_hora = NULL, $fecha_cierre = NULL, $id_turno = NULL, $id_usuario = NULL, $id_caja = NULL, $id_cliente = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["subtotal"] = $subtotal;
        $this->inputvars["total_impuestos"] = $total_impuestos;
        $this->inputvars["total"] = $total;
        $this->inputvars["tipo_comprobante"] = $tipo_comprobante;
        $this->inputvars["fecha_hora"] = $fecha_hora;
        $this->inputvars["fecha_cierre"] = $fecha_cierre;
        $this->inputvars["id_turno"] = $id_turno;
        $this->inputvars["id_usuario"] = $id_usuario;
        $this->inputvars["id_caja"] = $id_caja;
        $this->inputvars["id_cliente"] = $id_cliente;
        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setSubtotal($newval) {
        parent::setVar("subtotal", $newval);
    }

    public function getSubtotal() {
        return parent::getVar("subtotal");
    }

    public function setTotalImpuestos($newval) {
        parent::setVar("total_impuestos", $newval);
    }

    public function getTotalImpuestos() {
        return parent::getVar("total_impuestos");
    }

    public function setTotal($newval) {
        parent::setVar("total", $newval);
    }

    public function getTotal() {
        return parent::getVar("total");
    }

    public function setTipoComprobante($newval) {
        parent::setVar("tipo_comprobante", $newval);
    }

    public function getTipoComprobante() {
        return parent::getVar("tipo_comprobante");
    }

    public function setFechaHora($newval) {
        parent::setVar("fecha_hora", $newval);
    }

    public function getFechaHora() {
        return parent::getVar("fecha_hora");
    }

    public function setFechaCierre($newval) {
        parent::setVar("fecha_cierre", $newval);
    }

    public function getFechaCierre() {
        return parent::getVar("fecha_cierre");
    }

    public function setIdTurno($newval) {
        parent::setVar("id_turno", $newval);
    }

    public function getIdTurno() {
        return parent::getVar("id_turno");
    }

    public function setIdUsuario($newval) {
        parent::setVar("id_usuario", $newval);
    }

    public function getIdUsuario() {
        return parent::getVar("id_usuario");
    }

    public function setIdCaja($newval) {
        parent::setVar("id_caja", $newval);
    }

    public function getIdCaja() {
        return parent::getVar("id_caja");
    }

    public function setIdCliente($newval) {
        parent::setVar("id_cliente", $newval);
    }

    public function getIdCliente() {
        return parent::getVar("id_cliente");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>