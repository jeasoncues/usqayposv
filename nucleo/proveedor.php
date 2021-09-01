<?php
	require_once('include/SuperClass.php');
	class proveedor extends SuperClass{
		private $inputvars = array();
		private $inputname = 'proveedor';
        function __construct($id=NULL,$razon_social=NULL,$ruc=NULL,$direccion=NULL,$telefono=NULL,$estado_fila=NULL)
		{
        $this->inputvars["id"] = $id;
		  $this->inputvars["razon_social"] = $razon_social;
		  $this->inputvars["ruc"] = $ruc;
		  $this->inputvars["direccion"] = $direccion;
		  $this->inputvars["telefono"] = $telefono;
		  $this->inputvars["estado_fila"] = $estado_fila;
		  
			parent::__construct($this->inputvars,$this->inputname);
		}
	
          public function setId($newval){
              parent::setVar("id",$newval);
          }
          
          public function getId(){
              return parent::getVar("id");
          }
          public function setRazonSocial($newval){
              parent::setVar("razon_social",$newval);
          }
          
          public function getRazonSocial(){
              return parent::getVar("razon_social");
          }
          public function setRuc($newval){
              parent::setVar("ruc",$newval);
          }
          
          public function getRuc(){
              return parent::getVar("ruc");
          }
          public function setDireccion($newval){
              parent::setVar("direccion",$newval);
          }
          
          public function getDireccion(){
              return parent::getVar("direccion");
          }
          public function setTelefono($newval){
              parent::setVar("telefono",$newval);
          }
          
          public function getTelefono(){
              return parent::getVar("telefono");
          }
          public function setEstadoFila($newval){
              parent::setVar("estado_fila",$newval);
          }
          
          public function getEstadoFila(){
              return parent::getVar("estado_fila");
          }
        }?>