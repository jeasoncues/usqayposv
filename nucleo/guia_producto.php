<?php

require_once('include/SuperClass.php');

class guia_producto extends SuperClass {

    private $inputvars = array();
    private $inputname = 'guia_producto';

    function __construct($id = NULL, $id_usuario = NULL, $fecha_registro = NULL, $fecha_realizada = NULL, $tipo = NULL,$numero_guia=NULL,$id_proveedor=NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_usuario"] = $id_usuario;
        $this->inputvars["fecha_registro"] = $fecha_registro;
        $this->inputvars["fecha_realizada"] = $fecha_realizada;
        $this->inputvars["tipo"] = $tipo;
        $this->inputvars["estado_fila"] = $estado_fila;
        $this->inputvars["numero_guia"] = $numero_guia;
	$this->inputvars["id_proveedor"] = $id_proveedor;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setIdUsuario($newval) {
        parent::setVar("id_usuario", $newval);
    }

    public function getIdUsuario() {
        return parent::getVar("id_usuario");
    }

    public function setFechaRegistro($newval) {
        parent::setVar("fecha_registro", $newval);
    }

    public function getFechaRegistro() {
        return parent::getVar("fecha_registro");
    }

    public function setFechaRealizada($newval) {
        parent::setVar("fecha_realizada", $newval);
    }

    public function getFechaRealizada() {
        return parent::getVar("fecha_realizada");
    }

    public function setTipo($newval) {
        parent::setVar("tipo", $newval);
    }

    public function getTipo() {
        return parent::getVar("tipo");
    }
    
    public function setNumeroGuia($newval){
        parent::setVar("numero_guia",$newval);
    }

    public function getNumeroGuia(){
        return parent::getVar("numero_guia");
    }
    public function setIdProveedor($newval){
        parent::setVar("id_proveedor",$newval);
    }

    public function getIdProveedor(){
        return parent::getVar("id_proveedor");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>