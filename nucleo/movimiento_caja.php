<?php

require_once('include/SuperClass.php');

class movimiento_caja extends SuperClass {

    private $inputvars = array();
    private $inputname = 'movimiento_caja';

    function __construct($id = NULL, $id_caja = NULL, $monto = NULL, $tipo_movimiento = NULL, $fecha = NULL, $fecha_cierre = NULL, $id_turno = NULL, $id_usuario = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_caja"] = $id_caja;
        $this->inputvars["monto"] = $monto;
        $this->inputvars["tipo_movimiento"] = $tipo_movimiento;
        $this->inputvars["fecha"] = $fecha;
        $this->inputvars["fecha_cierre"] = $fecha_cierre;
        $this->inputvars["id_turno"] = $id_turno;
        $this->inputvars["id_usuario"] = $id_usuario;
        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setIdCaja($newval) {
        parent::setVar("id_caja", $newval);
    }

    public function getIdCaja() {
        return parent::getVar("id_caja");
    }

    public function setMonto($newval) {
        parent::setVar("monto", $newval);
    }

    public function getMonto() {
        return parent::getVar("monto");
    }

    public function setTipoMovimiento($newval) {
        parent::setVar("tipo_movimiento", $newval);
    }

    public function getTipoMovimiento() {
        return parent::getVar("tipo_movimiento");
    }

    public function setFecha($newval) {
        parent::setVar("fecha", $newval);
    }

    public function getFecha() {
        return parent::getVar("fecha");
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

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>