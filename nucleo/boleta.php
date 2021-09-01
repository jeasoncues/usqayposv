<?php
	require_once('include/SuperClass.php');
	class boleta extends SuperClass{
		private $inputvars = array();
		private $inputname = 'boleta';
        function __construct($id=NULL,$id_venta=NULL,$token=NULL,$serie=NULL,$estado_fila=NULL)
		{
        $this->inputvars["id"] = $id;
		  $this->inputvars["id_venta"] = $id_venta;
		  $this->inputvars["token"] = $token;
		  $this->inputvars["serie"] = $serie;
		  $this->inputvars["estado_fila"] = $estado_fila;
		  
			parent::__construct($this->inputvars,$this->inputname);
		}
	
          public function setId($newval){
              parent::setVar("id",$newval);
          }
          
          public function getId(){
              return parent::getVar("id");
          }
          public function setIdVenta($newval){
              parent::setVar("id_venta",$newval);
          }
          
          public function getIdVenta(){
              return parent::getVar("id_venta");
          }
          public function setToken($newval){
              parent::setVar("token",$newval);
          }
          
          public function getToken(){
              return parent::getVar("token");
          }
          public function setSerie($newval){
              parent::setVar("serie",$newval);
          }
          
          public function getSerie(){
              return parent::getVar("serie");
          }
          public function setEstadoFila($newval){
              parent::setVar("estado_fila",$newval);
          }
          
          public function getEstadoFila(){
              return parent::getVar("estado_fila");
          }
        }?>