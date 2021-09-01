<?php

require_once('include/SuperClass.php');

class usuario extends SuperClass {

    private $inputvars = array();
    private $inputname = 'usuario';

    function __construct($id = NULL, $documento = NULL, $nombres_y_apellidos = NULL, $tipo_usuario = NULL, $password = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["documento"] = $documento;
        $this->inputvars["nombres_y_apellidos"] = $nombres_y_apellidos;
        $this->inputvars["tipo_usuario"] = $tipo_usuario;
        $this->inputvars["password"] = $password;
        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setDocumento($newval) {
        parent::setVar("documento", $newval);
    }

    public function getDocumento() {
        return parent::getVar("documento");
    }

    public function setNombresYApellidos($newval) {
        parent::setVar("nombres_y_apellidos", $newval);
    }

    public function getNombresYApellidos() {
        return parent::getVar("nombres_y_apellidos");
    }

    public function setTipoUsuario($newval) {
        parent::setVar("tipo_usuario", $newval);
    }

    public function getTipoUsuario() {
        return parent::getVar("tipo_usuario");
    }

    public function setPassword($newval) {
        parent::setVar("password", $newval);
    }

    public function getPassword() {
        return parent::getVar("password");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }

    public function ServerSide($request)
	{
		$start = $request['start'];
		$length = $request['length'];

		$sql = "SELECT id, documento, nombres_y_apellidos,
                CASE WHEN tipo_usuario = 1 THEN 'Staff' 
                WHEN tipo_usuario = 2 THEN 'Administrador' 
                WHEN tipo_usuario = 3 THEN 'Cajero'
                WHEN tipo_usuario = 4 THEN 'Terminal'
                WHEN tipo_usuario = 99 THEN 'Super Admin'
                END AS tipo_usuario, estado_fila FROM usuario WHERE estado_fila = 1";

		$sqlCantidad = "SELECT count(*) FROM usuario";

			      if(!empty($request['search']['value'])){
			    		$aux = " AND (id Like '".$request['search']['value']."%'";
			    		$aux .= " OR documento Like '".$request['search']['value']."%'";
			    		$aux .= " OR nombres_y_apellidos Like '".$request['search']['value']."%'";
			    		$aux .= " OR tipo_usuario Like '%".$request['search']['value']."%' )";
			        $sql .= $aux;
			        $sqlCantidad .= $aux;
			      }

			      $sql .= " ORDER BY id DESC LIMIT ".$start.",".$length."";

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
    
    public function ServerSideCotos($request)
	{
		$start = $request['start'];
		$length = $request['length'];

		$sql = "SELECT id, documento, nombres_y_apellidos,
                CASE WHEN tipo_usuario = 1 THEN 'Staff' 
                WHEN tipo_usuario = 2 THEN 'Administrador' 
                WHEN tipo_usuario = 3 THEN 'Cajero'
                WHEN tipo_usuario = 4 THEN 'Terminal'
                WHEN tipo_usuario = 99 THEN 'Super Admin'
                END AS tipo_usuario, estado_fila FROM usuario WHERE id>1 and estado_fila = 1";

		$sqlCantidad = "SELECT count(*) FROM usuario where id>1";

			      if(!empty($request['search']['value'])){
			    		$aux = " AND (id Like '".$request['search']['value']."%'";
			    		$aux .= " OR documento Like '".$request['search']['value']."%'";
			    		$aux .= " OR nombres_y_apellidos Like '".$request['search']['value']."%'";
			    		$aux .= " OR tipo_usuario Like '%".$request['search']['value']."%' )";
			        $sql .= $aux;
			        $sqlCantidad .= $aux;
			      }

			      $sql .= " ORDER BY id DESC LIMIT ".$start.",".$length."";

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

}

?>