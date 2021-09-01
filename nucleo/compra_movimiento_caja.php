<?php
	require_once('include/SuperClass.php');
	class compra_movimiento_caja extends SuperClass{
		private $inputvars = array();
		private $inputname = 'compra_movimiento_caja';
        function __construct($id=NULL,$id_compra=NULL,$id_movimiento_caja=NULL,$estado_fila=NULL)
		{
        $this->inputvars["id"] = $id;
		  $this->inputvars["id_compra"] = $id_compra;
		  $this->inputvars["id_movimiento_caja"] = $id_movimiento_caja;
		  $this->inputvars["estado_fila"] = $estado_fila;
		  
			parent::__construct($this->inputvars,$this->inputname);
		}
	
          public function setId($newval){
              parent::setVar("id",$newval);
          }
          
          public function getId(){
              return parent::getVar("id");
          }
          public function setIdCompra($newval){
              parent::setVar("id_compra",$newval);
          }
          
          public function getIdCompra(){
              return parent::getVar("id_compra");
          }
          public function setIdMovimientoCaja($newval){
              parent::setVar("id_movimiento_caja",$newval);
          }
          
          public function getIdMovimientoCaja(){
              return parent::getVar("id_movimiento_caja");
          }
          public function setEstadoFila($newval){
              parent::setVar("estado_fila",$newval);
          }
          
          public function getEstadoFila(){
              return parent::getVar("estado_fila");
          }
        }?>