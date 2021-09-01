<?php

require_once('include/SuperClass.php');

class compra extends SuperClass {

    private $inputvars = array();
    private $inputname = 'compra';

    function __construct($id = NULL, $id_usuario = NULL, $id_proveedor = NULL, $categoria = NULL, $numero_documento = NULL, $monto_total = NULL, $fecha = NULL, $monto_pendiente = NULL, $id_caja = NULL, $proximo_pago = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_usuario"] = $id_usuario;
        $this->inputvars["id_proveedor"] = $id_proveedor;
        $this->inputvars["numero_documento"] = $numero_documento;
        $this->inputvars["categoria"] = $categoria;
        $this->inputvars["monto_total"] = $monto_total;
        $this->inputvars["fecha"] = $fecha;
        $this->inputvars["monto_pendiente"] = $monto_pendiente;
        $this->inputvars["id_caja"] = $id_caja;
        $this->inputvars["proximo_pago"] = $proximo_pago;
        $this->inputvars["estado_fila"] = $estado_fila;

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

    public function setIdProveedor($newval) {
        parent::setVar("id_proveedor", $newval);
    }

    public function getIdProveedor() {
        return parent::getVar("id_proveedor");
    }

    public function setCategoria($newval) {
        parent::setVar("categoria", $newval);
    }

    public function getCategoria() {
        return parent::getVar("categoria");
    }

    public function setMontoTotal($newval) {
        parent::setVar("monto_total", $newval);
    }

    public function getMontoTotal() {
        return parent::getVar("monto_total");
    }

    public function setFecha($newval) {
        parent::setVar("fecha", $newval);
    }

    public function getFecha() {
        return parent::getVar("fecha");
    }

    public function setMontoPendiente($newval) {
        parent::setVar("monto_pendiente", $newval);
    }

    public function getMontoPendiente() {
        return parent::getVar("monto_pendiente");
    }

    public function setIdCaja($newval) {
        parent::setVar("id_caja", $newval);
    }

    public function getIdCaja() {
        return parent::getVar("id_caja");
    }

    public function setProximoPago($newval) {
        parent::setVar("proximo_pago", $newval);
    }

    public function getProximoPago() {
        return parent::getVar("proximo_pago");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }
    
    public function setNumeroDocumento($newval) {
        parent::setVar("numero_documento", $newval);
    }

    public function getNumeroDocumento() {
        return parent::getVar("numero_documento");
    }

    public function ServerSide($request)
	{
        
        $start = $request['start'];
		$length = $request['length'];

		$sql = "SELECT c.id, u.nombres_y_apellidos, p.razon_social,
        CASE WHEN categoria = 1 THEN 'Boleta' WHEN categoria = 2
		THEN 'Factura' WHEN categoria = 3 THEN 'Nota de venta' END AS categoria, 
        numero_documento, monto_total, fecha, monto_pendiente, id_caja, proximo_pago,
        estado_fila FROM compra c INNER JOIN usuario u on c.id_usuario = u.id
        LEFT JOIN proveedor p on c.id_proveedor = p.id
        LEFT JOIN caja cj on c.id_caja = cj.id";

		$sqlCantidad = "SELECT count(*) FROM compra";

			      if(!empty($request['search']['value'])){
			    		$aux = " WHERE estado_fila = 1 and (id Like '".$request['search']['value']."%'";
			    		$aux .= " OR numero_documento Like '".$request['search']['value']."%'";
			    		$aux .= " OR id_usuario Like '".$request['search']['value']."%'";
			    		$aux .= " OR id_proveedor Like '%".$request['search']['value']."%' )";
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

}

?>