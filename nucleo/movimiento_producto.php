<?php

require_once('include/SuperClass.php');

class movimiento_producto extends SuperClass {

    private $inputvars = array();
    private $inputname = 'movimiento_producto';

    function __construct($id = NULL, $id_producto = NULL, $id_almacen = NULL, $cantidad = NULL, $costo = NULL, $tipo_movimiento = NULL, $id_usuario = NULL, $id_turno = NULL, $fecha = NULL, $fecha_cierre = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["id_producto"] = $id_producto;
        $this->inputvars["id_almacen"] = $id_almacen;
        $this->inputvars["cantidad"] = $cantidad;
        $this->inputvars["costo"] = $costo;
        $this->inputvars["tipo_movimiento"] = $tipo_movimiento;
        $this->inputvars["id_usuario"] = $id_usuario;
        $this->inputvars["id_turno"] = $id_turno;
        $this->inputvars["fecha"] = $fecha;
        $this->inputvars["fecha_cierre"] = $fecha_cierre;
        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setIdProducto($newval) {
        parent::setVar("id_producto", $newval);
    }

    public function getIdProducto() {
        return parent::getVar("id_producto");
    }

    public function setIdAlmacen($newval) {
        parent::setVar("id_almacen", $newval);
    }

    public function getIdAlmacen() {
        return parent::getVar("id_almacen");
    }

    public function setCantidad($newval) {
        parent::setVar("cantidad", $newval);
    }

    public function getCantidad() {
        return parent::getVar("cantidad");
    }

    public function setCosto($newval) {
        parent::setVar("costo", $newval);
    }

    public function getCosto() {
        return parent::getVar("costo");
    }

    public function setTipoMovimiento($newval) {
        parent::setVar("tipo_movimiento", $newval);
    }

    public function getTipoMovimiento() {
        return parent::getVar("tipo_movimiento");
    }

    public function setIdUsuario($newval) {
        parent::setVar("id_usuario", $newval);
    }

    public function getIdUsuario() {
        return parent::getVar("id_usuario");
    }

    public function setIdTurno($newval) {
        parent::setVar("id_turno", $newval);
    }

    public function getIdTurno() {
        return parent::getVar("id_turno");
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
      
      $sql = "SELECT mp.id, p.nombre as producto, al.nombre as almacen, mp.cantidad,
              CASE WHEN mp.costo IS NULL THEN 'S/. 0.00' ELSE CONCAT('S/. ', CAST(mp.costo AS DECIMAL(16,2) )) END AS costo,
              CASE WHEN mp.costo IS NULL THEN 'S/. 0.00' ELSE CONCAT('S/. ', CAST((mp.costo  * mp.cantidad) AS DECIMAL(16,2))) END AS total, mp.fecha, fecha_vencimiento, lote
              FROM movimiento_producto mp JOIN guia_movimiento gm ON gm.id_movimiento_producto = mp.id
              JOIN producto p ON p.id = mp.id_producto
              JOIN almacen al ON al.id = mp.id_almacen
              WHERE gm.id_guia_producto = ".intval($request['id'])." AND mp.estado_fila = 1";

      $sqlCantidad = "SELECT COUNT(*) FROM movimiento_producto mp JOIN guia_movimiento gm
      ON gm.id_movimiento_producto = mp.id JOIN producto p ON p.id = mp.id_producto
      JOIN almacen al ON al.id = mp.id_almacen WHERE gm.id_guia_producto = ".intval($request['id'])." AND mp.estado_fila = 1 ";

      if(!empty($request['search']['value'])){
    		$aux = " AND (p.id Like '".$request['search']['value']."%'";
    		$aux .= " OR p.nombre Like '".$request['search']['value']."%'";
    		$aux .= " OR al.nombre Like '%".$request['search']['value']."%' )";
        $sql .= $aux;
        $sqlCantidad .= $aux;
      }

      $sql .= " ORDER BY p.id DESC LIMIT ".$start.",".$length."";

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
          $subdata[] = $item[7];
          $subdata[] = $item[8];
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