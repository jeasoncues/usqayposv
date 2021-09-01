<?php

require_once('include/SuperClass.php');

class soporte_ticket extends SuperClass {

    private $inputvars = array();
    private $inputname = 'soporte_ticket';

    function __construct($id = NULL, $email = NULL, $name = NULL, $phone = NULL, 
    $subject= NULL,
    $message= NULL,
    $id_usuario= NULL,
    $fecha_cierre= NULL,
    $fecha= NULL,
    $id_caja= NULL,
    $estado_atencion= NULL,
    $numero_ticket = NULL,
    $estado_fila= NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["email"] = $email;
        $this->inputvars["name"] = $name;
        $this->inputvars["phone"] = $phone;
        $this->inputvars["subject"] = $subject;
        $this->inputvars["message"] = $message;
        $this->inputvars["id_usuario"] = $id_usuario;
        $this->inputvars["fecha_cierre"] = $fecha_cierre;
        $this->inputvars["fecha"] = $fecha;
        $this->inputvars["id_caja"] = $id_caja;
        $this->inputvars["estado_atencion"] = $estado_atencion;
        $this->inputvars["numero_ticket"] = $numero_ticket;
        $this->inputvars["estado_fila"] = $estado_fila;        
        parent::__construct($this->inputvars, $this->inputname);
    }
    public function setNumeroTicket($newval) {
        parent::setVar("numero_ticket", $newval);
    }

    public function getNumeroTicket() {
        return parent::getVar("numero_ticket");
    }

    public function setEstadoAtencion($newval) {
        parent::setVar("estado_atencion", $newval);
    }

    public function getEstadoAtencion() {
        return parent::getVar("estado_atencion");
    }

    public function setIdCaja($newval) {
        parent::setVar("id_caja", $newval);
    }

    public function getIdCaja() {
        return parent::getVar("id_caja");
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

    public function setIdUsuario($newval) {
        parent::setVar("id_usuario", $newval);
    }

    public function getIdUsuario() {
        return parent::getVar("id_usuario");
    }

    public function setMessage($newval) {
        parent::setVar("message", $newval);
    }

    public function getMessage() {
        return parent::getVar("message");
    }  

    public function setSubject($newval) {
        parent::setVar("subject", $newval);
    }

    public function getSubject() {
        return parent::getVar("subject");
    }    

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setEmail($newval) {
        parent::setVar("email", $newval);
    }

    public function getEmail() {
        return parent::getVar("email");
    }

    public function setName($newval) {
        parent::setVar("name", $newval);
    }

    public function getName() {
        return parent::getVar("name");
    }

    public function setPhone($newval) {
        parent::setVar("phone", $newval);
    }

    public function getPhone() {
        return parent::getVar("phone");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

}

?>