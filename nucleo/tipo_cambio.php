<?php
	require_once('include/SuperClass.php');
	class tipo_cambio extends SuperClass{
		private $inputvars = array();
		private $inputname = 'tipo_cambio';
        function __construct($id=NULL,$moneda_origen=NULL,$moneda_destino=NULL,$tasa=NULL,$estado_fila=NULL)
		{
        $this->inputvars["id"] = $id;
		  $this->inputvars["moneda_origen"] = $moneda_origen;
		  $this->inputvars["moneda_destino"] = $moneda_destino;
		  $this->inputvars["tasa"] = $tasa;
		  $this->inputvars["estado_fila"] = $estado_fila;
		  
			parent::__construct($this->inputvars,$this->inputname);
		}
	
          public function setId($newval){
              parent::setVar("id",$newval);
          }
          
          public function getId(){
              return parent::getVar("id");
          }
          public function setMonedaOrigen($newval){
              parent::setVar("moneda_origen",$newval);
          }
          
          public function getMonedaOrigen(){
              return parent::getVar("moneda_origen");
          }
          public function setMonedaDestino($newval){
              parent::setVar("moneda_destino",$newval);
          }
          
          public function getMonedaDestino(){
              return parent::getVar("moneda_destino");
          }
          public function setTasa($newval){
              parent::setVar("tasa",$newval);
          }
          
          public function getTasa(){
              return parent::getVar("tasa");
          }
          public function setEstadoFila($newval){
              parent::setVar("estado_fila",$newval);
          }
          
          public function getEstadoFila(){
              return parent::getVar("estado_fila");
          }
        }?>