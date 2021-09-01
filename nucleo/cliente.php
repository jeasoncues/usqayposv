<?php
	require_once('include/SuperClass.php');
	class cliente extends SuperClass{
		private $inputvars = array();
		private $inputname = 'cliente';
        function __construct($id=NULL,$nombre=NULL,$documento=NULL,$direccion=NULL,$tipo_cliente=NULL,$fecha_nacimiento=NULL, $correo=NULL ,$estado_fila=NULL)
		{
        $this->inputvars["id"] = $id;
		  $this->inputvars["nombre"] = $nombre;
		  $this->inputvars["documento"] = $documento;
		  $this->inputvars["direccion"] = $direccion;
		  $this->inputvars["tipo_cliente"] = $tipo_cliente;
      $this->inputvars["fecha_nacimiento"] = $fecha_nacimiento;
      $this->inputvars["correo"] = $correo;
		  $this->inputvars["estado_fila"] = $estado_fila;
		  
			parent::__construct($this->inputvars,$this->inputname);
		}
	
          public function setId($newval){
              parent::setVar("id",$newval);
          }
          
          public function getId(){
              return parent::getVar("id");
          }
          public function setNombre($newval){
              parent::setVar("nombre",$newval);
          }
          
          public function getNombre(){
              return parent::getVar("nombre");
          }
          public function setDocumento($newval){
              parent::setVar("documento",$newval);
          }
          
          public function getDocumento(){
              return parent::getVar("documento");
          }
          public function setDireccion($newval){
              parent::setVar("direccion",$newval);
          }
          
          public function getDireccion(){
              return parent::getVar("direccion");
          }
          public function setTipoCliente($newval){
              parent::setVar("tipo_cliente",$newval);
          }
          
          public function getTipoCliente(){
              return parent::getVar("tipo_cliente");
          }
          public function setFechaNacimiento($newval){
              parent::setVar("fecha_nacimiento",$newval);
          }
          
          public function getFechaNacimiento(){
              return parent::getVar("fecha_nacimiento");
          }
          public function setCorreo($newval){
              parent::setVar("correo",$newval);
          }
          
          public function getCorreo(){
              return parent::getVar("correo");
          }
          public function setEstadoFila($newval){
              parent::setVar("estado_fila",$newval);
          }
          
          public function getEstadoFila(){
              return parent::getVar("estado_fila");
          }
          public function ServerSide($request)
			    {
			      $start = $request['start'];
			      $length = $request['length'];

						$sql = "SELECT id, nombre, documento, direccion, correo,
						CASE WHEN tipo_cliente = 0 OR tipo_cliente = 1 THEN 'Natural' WHEN tipo_cliente = 2
						THEN 'Juridico' END AS tipo_cliente, fecha_nacimiento FROM cliente WHERE id <> 0";

			      $sqlCantidad = "SELECT count(*) FROM cliente";

			      if(!empty($request['search']['value'])){
			    		$aux = " AND (id Like '".$request['search']['value']."%'";
			    		$aux .= " OR documento Like '".$request['search']['value']."%'";
			    		$aux .= " OR correo Like '".$request['search']['value']."%'";
			    		$aux .= " OR nombre Like '%".$request['search']['value']."%' )";
			        $sql .= $aux;
			        $sqlCantidad .= $aux;
			      }

			      $sql .= " ORDER BY id DESC LIMIT ".$start.",".$length."";

			      // echo $sql;

			      $totalData = parent::consulta_arreglo($sqlCantidad)[0];

			      $result = parent::consulta_matriz($sql);

			      $data = array();
			      if(!empty($result)){
			        foreach ($result as $item) {
				        $subdata = array();
				        $subdata[] = $item[0];
				        $subdata[] = $item[1];
				        $subdata[] = $item[2];
				        $subdata[] = $item[3];
				        $subdata[] = $item[4];
				        $subdata[] = $item[5];
				        $subdata[] = $item[6];
				        $subdata[] = '';
				        $data[] = $subdata;
			        }
			      }else{
			        $data = [];
			      }

			      $json_data = array(
			        "draw" => intval($request["draw"]),
			        "recordsTotal" => intval($totalData),
			        "recordsFiltered" => intval($totalData),
			        "data" => $data
			      );

			      return $json_data;
			    }
        }?>