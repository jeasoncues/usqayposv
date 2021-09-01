<?php
	require_once('include/SuperClass.php');
	class compra_guia extends SuperClass{
		private $inputvars = array();
		private $inputname = 'compra_guia';
        function __construct($id=NULL,$id_compra=NULL,$id_guia_producto=NULL,$estado_fila=NULL)
		{
        $this->inputvars["id"] = $id;
		  $this->inputvars["id_compra"] = $id_compra;
		  $this->inputvars["id_guia_producto"] = $id_guia_producto;
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
          public function setIdGuiaProducto($newval){
              parent::setVar("id_guia_producto",$newval);
          }
          
          public function getIdGuiaProducto(){
              return parent::getVar("id_guia_producto");
          }
          public function setEstadoFila($newval){
              parent::setVar("estado_fila",$newval);
          }
          
          public function getEstadoFila(){
              return parent::getVar("estado_fila");
          }
        }?>