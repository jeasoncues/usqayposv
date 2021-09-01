<?php

require_once('include/SuperClass.php');

class caja extends SuperClass {

    private $inputvars = array();
    private $inputname = 'caja';

    function __construct($id = NULL, $nombre = NULL, $ubicacion = NULL, $serie_impresora = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["nombre"] = $nombre;
        $this->inputvars["ubicacion"] = $ubicacion;
        $this->inputvars["serie_impresora"] = $serie_impresora;
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

    public function setUbicacion($newval) {
        parent::setVar("ubicacion", $newval);
    }

    public function getUbicacion() {
        return parent::getVar("ubicacion");
    }
    
    public function setSerieImpresora($newval) {
        parent::setVar("serie_impresora", $newval);
    }

    public function getSerieImpresora() {
        return parent::getVar("serie_impresora");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>