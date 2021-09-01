<?php
header("Access-Control-Allow-Origin: *");
header('Content-Type: application/json');
require_once('../nucleo/producto_taxonomiap.php');
require_once('../nucleo/producto.php');

$res=0;
$q=0;
$page=0;
$offset=0;
$limit=0;
if (isset($_GET['q'])) {
    $q = $_GET['q'];

    //generador de cod barras aunemta ceros al inicio y un digito al final- aqui quita ese digito
     /*   $rest = substr($q, 3, -1);
        $ceros =  substr($q, 0, 3);
        if($ceros=="000"){
        $q=$rest;
        }*/
        
    /*if (busca_id($q)!=0) {
        $res=busca_id($q); 
        echo json_encode($res);
    } else*/
	if (busca_cod_barra($q)!=0)  {
       $res=busca_cod_barra($q);
       echo json_encode($res);
    } elseif (busca_nombre($q)!=0)  {
       $res= busca_nombre($q);
       echo json_encode($res);
    }else{
        echo 0;
     }
    
   
}

function busca_id($q){
   $objproducto_taxonomiap = new producto_taxonomiap();
    $where = " producto.estado_fila = 1 ";
    $where .= " AND producto.id= '{$q}'";
    if (isset($_GET['almacen'])) {
        $almacen = $_GET['almacen'];
        $where .= " AND movimiento_producto.id_almacen = {$almacen} ";
    }
    $query = "SELECT producto.id,producto.nombre,producto.precio_compra, producto.precio_venta, producto.incluye_impuesto, producto.estado_fila, sum(movimiento_producto.cantidad) as stock 
    FROM producto LEFT JOIN movimiento_producto on producto.id = movimiento_producto.id_producto 
    WHERE  {$where}
    GROUP BY movimiento_producto.id_producto ORDER BY producto.nombre"; 
    $res = $objproducto_taxonomiap->consulta_matriz($query);
    return ($res);

}
function busca_cod_barra($q){
    
    $objproducto = new producto();
    $objproducto_taxonomiap = new producto_taxonomiap();
    $query1 = ("SELECT DISTINCT id_producto FROM producto_taxonomiap WHERE valor = '{$q}' AND producto_taxonomiap.id_taxonomiap = 4 ");
    $idProd = $objproducto_taxonomiap->consulta_arreglo($query1);

  
   if($idProd !=0){
    $query = " SELECT id,producto.nombre,precio_compra,precio_venta,incluye_impuesto,estado_fila, 0 as stock 
    FROM producto WHERE id=".$idProd['id_producto'];
    $res = $objproducto_taxonomiap->consulta_matriz($query);
   }else{
    $query = " SELECT pp.id as id_secundario,pp.id_producto as id, CONCAT(p.nombre,' - ',pp.descripcion)  as nombre ,pp.precio_compra,pp.precio_venta,pp.incluye_impuesto,pp.estado_fila, 0 as stock 
    FROM productos_precios pp inner join producto p on pp.id_producto=p.id WHERE barcode='{$q}' ";
    $res = $objproducto_taxonomiap->consulta_matriz($query);
        if($res==0){
            $res =0;
        }
   
   }
   
    /*$objproducto_taxonomiap = new producto_taxonomiap();
    $where = " producto_taxonomiap.estado_fila = 1 ";
    $where .= " AND producto_taxonomiap.valor = '{$q}' AND producto_taxonomiap.id_taxonomiap = 4 ";
   
    if (isset($_GET['almacen'])) {
        $almacen = $_GET['almacen'];
        $where .= " AND movimiento_producto.id_almacen = {$almacen} ";
    }
    $query = "SELECT producto.id,producto.nombre,producto.precio_compra, producto.precio_venta, producto.incluye_impuesto, producto.estado_fila, sum(movimiento_producto.cantidad) as stock 
    FROM producto_taxonomiap inner join producto on producto_taxonomiap.id_producto = producto.id INNER JOIN movimiento_producto on producto.id = movimiento_producto.id_producto 
    WHERE  {$where}
    GROUP BY movimiento_producto.id_producto ORDER BY producto.nombre";    
    $res = $objproducto_taxonomiap->consulta_matriz($query);*/
    
    return $res;
}
function busca_nombre($q){
    $objproducto_taxonomiap = new producto_taxonomiap();
    $limit = 14;
    $page = $_GET['page'];
    $offset = $limit * $page;
    $where = " producto.estado_fila = 1 ";
    $where .= " AND producto.nombre LIKE '%{$q}%' ";
    if (isset($_GET['almacen'])) {
        $almacen = $_GET['almacen'];
        $where .= " AND movimiento_producto.id_almacen = {$almacen} ";
    }
    $query = "SELECT producto.id,producto.nombre,producto.precio_compra, producto.precio_venta, producto.incluye_impuesto, producto.estado_fila, sum(movimiento_producto.cantidad) as stock 
     FROM producto left JOIN movimiento_producto on producto.id = movimiento_producto.id_producto 
     WHERE  {$where}
    GROUP BY producto.id ORDER BY producto.nombre LIMIT {$limit} OFFSET {$offset}";    
    $res = $objproducto_taxonomiap->consulta_matriz($query);
    return $res;
}
