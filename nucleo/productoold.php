<?php

require_once('include/SuperClass.php');

class producto extends SuperClass { 

    private $inputvars = array();
    private $inputname = 'producto';

    function __construct($id = NULL, $nombre = NULL, $precio_compra = NULL, $precio_venta = NULL, $incluye_impuesto = NULL, $estado_fila = NULL) {
        $this->inputvars["id"] = $id;
        $this->inputvars["nombre"] = $nombre;
        $this->inputvars["precio_compra"] = $precio_compra;
        $this->inputvars["precio_venta"] = $precio_venta;
        $this->inputvars["incluye_impuesto"] = $incluye_impuesto;
        $this->inputvars["estado_fila"] = $estado_fila;

        parent::__construct($this->inputvars, $this->inputname);
    }

    public function setId($newval) {
        parent::setVar("id", $newval);
    }

    public function getId() {
        return parent::getVar("id");
    }

    public function setNombre($newval) {
        parent::setVar("nombre", $newval);
    }

    public function getNombre() {
        return parent::getVar("nombre");
    }

    public function setPrecioCompra($newval) {
        parent::setVar("precio_compra", $newval);
    }

    public function getPrecioCompra() {
        return parent::getVar("precio_compra");
    }

    public function setPrecioVenta($newval) {
        parent::setVar("precio_venta", $newval);
    }

    public function getPrecioVenta() {
        return parent::getVar("precio_venta");
    }

    public function setIncluyeImpuesto($newval) {
        parent::setVar("incluye_impuesto", $newval);
    }

    public function getIncluyeImpuesto() {
        return parent::getVar("incluye_impuesto");
    }

    public function setEstadoFila($newval) {
        parent::setVar("estado_fila", $newval);
    }

    public function getEstadoFila() {
        return parent::getVar("estado_fila");
    }
    
    public function getStock($in,$alm){
        $stock = null;
        if(intval($alm)>0){
            $stock = parent::consulta_arreglo("Select sum(cantidad) as stock from movimiento_producto where id_producto = '".$in."' AND id_almacen = '".$alm."'");
        }else{
            $stock = parent::consulta_arreglo("Select sum(cantidad) as stock from movimiento_producto where id_producto = '".$in."'");
        }
        $nstock = floatval($stock["stock"]);
        return $nstock;
    }

    public function ServerSide($request)
    {
      $start = $request['start'];
      $length = $request['length'];
      
      $searchPrev=$request['search']['value'];
      $codificacion=mb_detect_encoding($searchPrev,"UTF-8,ISO-8859-1");        
      $search = iconv($codificacion,'UTF-8',$searchPrev);

      //$search = $request['search']['value'];

      $sql = "SELECT p.id, nombre, CONCAT('S/. ',precio_compra) AS precio_compra, 
        CONCAT('S/. ', precio_venta) AS precio_venta, CASE WHEN incluye_impuesto = 1 THEN 'SI' ELSE 'NO' END 
        AS incluye_impuesto, valor
        FROM producto p, producto_taxonomiap pt
        WHERE id_taxonomiap = 2 AND id_producto = p.id";

      // $sql = "SELECT p.id, p.nombre, CONCAT('S/. ',precio_compra) AS precio_compra, CONCAT('S/. ', precio_venta) AS precio_venta, CASE WHEN incluye_impuesto = 1 THEN 'SI' ELSE 'NO' END AS incluye_impuesto, valor, GROUP_CONCAT(pt.valor) AS codigos
      //     FROM producto p
      //     INNER JOIN producto_taxonomiap pt ON p.id = pt.id_producto
      //     WHERE pt.id_taxonomiap in(2)
      //     GROUP BY p.id";

      $sqlCantidad = "SELECT count(*) FROM producto p";

      //if(!empty($request['search']['value'])){
      if(!empty($search)){
        // $sql = str_replace("GROUP BY p.id", "", $sql);
        $aux = " AND (p.id LIKE '%$search%'";
        $aux .= " OR precio_compra LIKE '%$search%'";
        $aux .= " OR precio_venta LIKE '%$search%'";
        $aux .= " OR nombre LIKE '%$search%'";
        $aux .= " )";
        $sql .= $aux;
        // echo $sql;
        $sqlCantidad .= " WHERE 1 = 1 ".$aux."";
      }

      $sql .= " ORDER BY id DESC LIMIT ".$start.",".$length."";

      //echo($sql);


      $totalData = parent::consulta_arreglo($sqlCantidad)[0];

      $result = parent::consulta_matriz($sql);

      $data = array();
      if(!empty($result)){
        foreach ($result as $item) {
          $subdata = array();
          /* $subdata[] = $item[0];
          $subdata[] = utf8_decode($item[1]);
          $subdata[] = $item[2];
          $subdata[] = $item[3];
          $subdata[] = $item[4];
          $subdata[] = $item[5]; */
         /*  $subdata[] = convert_cotos($item[0]);
            $subdata[] = convert_cotos($item[1]);
            $subdata[] = convert_cotos($item[2]);
            $subdata[] = convert_cotos($item[3]);
            $subdata[] = convert_cotos($item[4]);
            $subdata[] = convert_cotos($item[5]); */

            $subdata[]=iconv(mb_detect_encoding($item[0],"UTF-8,ISO-8859-1"),'UTF-8',$item[0]);
            $subdata[]=iconv(mb_detect_encoding($item[1],"UTF-8,ISO-8859-1"),'UTF-8',$item[1]);
            $subdata[]=iconv(mb_detect_encoding($item[2],"UTF-8,ISO-8859-1"),'UTF-8',$item[2]);
            $subdata[]=iconv(mb_detect_encoding($item[3],"UTF-8,ISO-8859-1"),'UTF-8',$item[3]);
            $subdata[]=iconv(mb_detect_encoding($item[4],"UTF-8,ISO-8859-1"),'UTF-8',$item[4]);
            $subdata[]=iconv(mb_detect_encoding($item[5],"UTF-8,ISO-8859-1"),'UTF-8',$item[5]);

          // $srt=explode(",",$item[6]);
          // $subdata[] = $srt[0];
          // if(empty($srt[1]))
          //   $subdata[] = "";
          // else 
          //   $subdata[] = $srt[1];
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


    public function ServerSideCotos($request)
    {
      $now = date('Y-m-d');
      $start = $request['start'];
      $length = $request['length'];
      
      $searchPrev=$request['search']['value'];
      $codificacion=mb_detect_encoding($searchPrev,"UTF-8,ISO-8859-1");        
      $search = iconv($codificacion,'UTF-8',$searchPrev);

      //$search = $request['search']['value'];

      $sql="SELECT p.id, p.nombre, CONCAT('S/. ',p.precio_compra) AS precio_compra, 
      CONCAT('S/. ', p.precio_venta) AS precio_venta, CASE WHEN p.incluye_impuesto = 1 THEN 'SI' ELSE 'NO' END 
      AS incluye_impuesto, 
      
       (select sum(if(mp.cantidad>0, mp.cantidad, 0)) + sum(if(mp.cantidad<0, mp.cantidad, 0)) ) as stock,
       a.id as id_almacen,
       a.nombre as almacen      
      from producto p inner join movimiento_producto mp on mp.id_producto = p.id 
      inner join almacen a on mp.id_almacen = a.id 
      where mp.estado_fila = 1 and mp.fecha_cierre <= '$now' ";



  /*$sql="SELECT p.id, p.nombre, CONCAT('S/. ',p.precio_compra) AS precio_compra, 
      CONCAT('S/. ', p.precio_venta) AS precio_venta, CASE WHEN p.incluye_impuesto = 1 THEN 'SI' ELSE 'NO' END 
      AS incluye_impuesto, 
      (SELECT sum(cantidad) GROUP by id_producto)as stock,
       a.id as id_almacen,
       a.nombre as almacen      
      from producto p inner join movimiento_producto mp on mp.id_producto = p.id 
      inner join almacen a on mp.id_almacen = a.id 
      WHERE mp.estado_fila=1 GROUP by id_producto, a.id ";*/

      /* $sql = "SELECT p.id, nombre, CONCAT('S/. ',precio_compra) AS precio_compra, 
        CONCAT('S/. ', precio_venta) AS precio_venta, CASE WHEN incluye_impuesto = 1 THEN 'SI' ELSE 'NO' END 
        AS incluye_impuesto, valor
        FROM producto p, producto_taxonomiap pt
        WHERE id_taxonomiap = 2 AND id_producto = p.id"; */

      // $sql = "SELECT p.id, p.nombre, CONCAT('S/. ',precio_compra) AS precio_compra, CONCAT('S/. ', precio_venta) AS precio_venta, CASE WHEN incluye_impuesto = 1 THEN 'SI' ELSE 'NO' END AS incluye_impuesto, valor, GROUP_CONCAT(pt.valor) AS codigos
      //     FROM producto p
      //     INNER JOIN producto_taxonomiap pt ON p.id = pt.id_producto
      //     WHERE pt.id_taxonomiap in(2)
      //     GROUP BY p.id";

      $sqlCantidad = "SELECT count(*) FROM producto p";

      //if(!empty($request['search']['value'])){
      if(!empty($search)){
        // $sql = str_replace("GROUP BY p.id", "", $sql);
        $aux = " AND (p.id LIKE '%$search%'";
        $aux .= " OR precio_compra LIKE '%$search%'";
        $aux .= " OR precio_venta LIKE '%$search%'";
        $aux .= " OR nombre LIKE '%$search%'";
        $aux .= " OR almacen LIKE '%$search%'";
        $aux .= " )";
        $sql .= $aux;
        // echo $sql;
        $sqlCantidad .= " WHERE 1 = 1 ".$aux."";
      }

      $sql .= " GROUP BY mp.id_producto,a.id ORDER BY id DESC LIMIT ".$start.",".$length."";

      //echo($sql);
      var_dump($sqlCantidad);

      $totalData = parent::consulta_arreglo($sqlCantidad)[0];
      
      var_dump($totalData);
      $result = parent::consulta_matriz($sql);

      $data = array();
      if(!empty($result)){
        foreach ($result as $item) {
          $subdata = array();
          /* $subdata[] = $item[0];
          $subdata[] = utf8_decode($item[1]);
          $subdata[] = $item[2];
          $subdata[] = $item[3];
          $subdata[] = $item[4];
          $subdata[] = $item[5]; */
         /*  $subdata[] = convert_cotos($item[0]);
            $subdata[] = convert_cotos($item[1]);
            $subdata[] = convert_cotos($item[2]);
            $subdata[] = convert_cotos($item[3]);
            $subdata[] = convert_cotos($item[4]);
            $subdata[] = convert_cotos($item[5]); */

            $subdata[]=iconv(mb_detect_encoding($item[0],"UTF-8,ISO-8859-1"),'UTF-8',$item[0]);
            $subdata[]=iconv(mb_detect_encoding($item[1],"UTF-8,ISO-8859-1"),'UTF-8',$item[1]);
            $subdata[]=iconv(mb_detect_encoding($item[2],"UTF-8,ISO-8859-1"),'UTF-8',$item[2]);
            $subdata[]=iconv(mb_detect_encoding($item[3],"UTF-8,ISO-8859-1"),'UTF-8',$item[3]);
            $subdata[]=iconv(mb_detect_encoding($item[4],"UTF-8,ISO-8859-1"),'UTF-8',$item[4]);
            $subdata[]=iconv(mb_detect_encoding($item[5],"UTF-8,ISO-8859-1"),'UTF-8',$item[5]);

          // $srt=explode(",",$item[6]);
          // $subdata[] = $srt[0];
          // if(empty($srt[1]))
          //   $subdata[] = "";
          // else 
          //   $subdata[] = $srt[1];
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


    public function convert_cotos($entrada){
        $codificacion_=mb_detect_encoding($entrada,"UTF-8,ISO-8859-1");
        $dataa= iconv($codificacion_,'UTF-8',$entrada);
        return $dataa;
    }

}

?>