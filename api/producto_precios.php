<?php
header("Access-Control-Allow-Origin: *");
require_once('../nucleo/producto.php');
header('Content-Type: application/json');
$objproducto = new producto();

if (isset($_GET['id'])) {
    $id = $_GET['id'];



    $query = "SELECT productos_precios.id_producto, productos_precios.descripcion, productos_precios.precio_compra, productos_precios.precio_venta, productos_precios.incluye_impuesto, productos_precios.estado_fila,
    productos_precios.barcode, productos_precios.cantidad, producto.nombre as producto
    FROM productos_precios INNER JOIN producto on productos_precios.id_producto = producto.id
    WHERE productos_precios.id_producto = {$id} and productos_precios.estado_fila = 1";

    $response = $objproducto->consulta_matriz($query);

    if ($response != 0) {

        $basicPriceDB = $objproducto->where(["id", "=", $id]);
        $basicPriceDB = $basicPriceDB[0];
        foreach($response as &$ress){
            $ress=array_map("_convert_",$ress);
        }

        $basicPrice = array(
            "id_producto" => $basicPriceDB["id"],
            "descripcion" => "Precio Normal",
            "precio_compra" => $basicPriceDB["precio_compra"],
            "precio_venta" => $basicPriceDB["precio_venta"],
            "incluye_impuesto" => $basicPriceDB["incluye_impuesto"],
            "estado_fila" => $basicPriceDB["estado_fila"],
            "barcode" => null,
            "cantidad" => null,
            "producto" => $basicPriceDB["nombre"]
        );
        array_unshift($response, $basicPrice);
    }
    echo json_encode($response);
}

function _convert_($entrada){
	$codificacion_=mb_detect_encoding($entrada,"ISO-8859-1,UTF-8");
	$dataa= iconv($codificacion_,'UTF-8',$entrada);
	return $dataa;
}