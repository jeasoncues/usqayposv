<?php
/**
 * Created by PhpStorm.
 * User: Eliu CTM
 * Date: 04/10/2018
 * Time: 16:11
 */
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>CIE</title>
</head>
<body>
<style>
    body {
        font-family: "Lucida Console", Monaco, monospace;
       font-size: 12px;
        zoom: 200%;
        font-weight: 200;
        width: 100%;
        margin: 0px;
    }
 

    .title {
        text-align: center;
        text-transform: uppercase;
        margin-bottom: 1px;
        margin-top: 1px;
    }

    hr {
        border: none;
        border-top: 1px solid black;
    }

    table {
        border-collapse: collapse;
        width: 90%;
        font-family: "Lucida Console", Monaco, monospace;
        font-size: 12px !important;
    }

    table td {
        border: 1px solid black;
    }

    table th {
        border: 1px solid black;
        text-align: center;
    }

    tr.resumen {
        text-align: right;
    }

    .resumen td {
        border: none;
    }

    .precio {
        text-align: right;
    }
</style>
<style type="text/css" media="print">
    @page {
        margin: 0;
    }
</style>
<?php

require_once '../nucleo/include/MasterConexion.php';
$objconn = new MasterConexion();

    $config = $objconn->consulta_arreglo("Select * from configuracion where id=1");
    $fecha_cierre = $config["fecha_cierre"];
    if (isset($_GET['fecha']) && !empty($_GET['fecha'])) {
    $fecha_cierre = $_GET['fecha'];
    }
    $cambio = $objconn->consulta_arreglo("Select * from tipo_cambio where moneda_origen = 'USD' AND moneda_destino = 'PEN'");


$total_vendido = 0;
$efectivo = 0;
$visa = 0;
$master = 0;
$inicial = 0;
$adicional = 0;
$salidas = 0;
$soles = 0;
$dolares = 0;
$caja = 0;
$credito = 0;
$cobro = 0;
$salidas_cobro = 0;
$movimientos2 = 0;
$notacred = 0;
$caja_u = 0;
$liquidaciones = 0;
$descuentos = 0;
$detraccion=0;
$detraccion_visa=0;
$detraccion_mastercard=0;
$detraccion_efectivo=0;
$descuento=0;
$cobro_visa = 0;
$cobro_mastercard = 0;
$cobro_deposito = 0;
$cobro_efectivo = 0;
$yape=0;
$cobro_yape=0;
$detraccion_yape=0;
$inicial = 0;
$venta=0;
$movimientos = null;
$sql = "";
$total_boleta=0;
$total_factura=0;
$total_ticket=0;
$total_boleta_discount=0;
$total_factura_discount=0;
$total_ticket_discount=0;
$sql_caja = "Select * from caja";
$ResCaja = $objconn->consulta_matriz($sql_caja);
$wherein = "(";
foreach ($ResCaja as $c) {
    $wherein .= $c["id"].","; 
}
$wherein = substr($wherein, 0, -1);
$wherein .= ")";

$pruebacotos="";
// echo $_COOKIE['id_caja'];
if(isset($_GET["turno"])){
    $sqlVenta = "SELECT * from venta where estado_fila=1 AND fecha_cierre = '".$_GET["fecha"]."'";
    $sql = "SELECT * from movimiento_caja where tipo_movimiento NOT LIKE '%DESCUENTO%' AND estado_fila=1 AND fecha_cierre = '".$_GET["fecha"]."'";
    $sqlDescuento = "SELECT medio, vm.monto
            FROM venta v, venta_medio_pago vm
            WHERE  vm.estado_fila=1 AND v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND medio = 'DESCUENTO' ";
    $sqlNull = "SELECT vm.id as conc
                FROM venta v
                INNER JOIN venta_medio_pago vm ON v.id = vm.id_venta 
                WHERE total is null AND v.fecha_cierre = '".$fecha_cierre."'";
    $sqlticket = 
            "SELECT v.total
            FROM venta v
            WHERE v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 0 and v.estado_fila = 1 ";
    $sqlboleta = 
            "SELECT v.total
            FROM venta v
            WHERE v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 1 and v.estado_fila = 1 ";
    $sqlfactura = 
            "SELECT v.total
            FROM venta v
            WHERE  v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante =2 and v.estado_fila = 1 ";
    $sqlticketDiscount = 
            "SELECT v.total,vm.monto
            FROM venta v, venta_medio_pago vm
            WHERE  v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 0 and v.estado_fila=1 AND vm.medio = 'DESCUENTO'";
    $sqlboletaDiscount = 
           "SELECT v.total,vm.monto
            FROM venta v, venta_medio_pago vm
            WHERE  v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 1 and v.estado_fila=1 AND vm.medio = 'DESCUENTO'";
    $sqlfacturaDiscount = 
            "SELECT v.total,vm.monto
            FROM venta v, venta_medio_pago vm
            WHERE  v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 2 and v.estado_fila=1 AND vm.medio = 'DESCUENTO'";

     $open = "SELECT * FROM movimiento_caja WHERE estado_fila=1 AND tipo_movimiento like '%OPEN|PEN|EFECTIVO%' AND fecha_cierre = '".$fecha_cierre."' ";
  
        
   // }
    if(intval($_GET["turno"])>0){
        $sqlDescuento .= " AND v.id_turno = '".$_GET["turno"]."'";
        $sql .= " AND id_turno = '".$_GET["turno"]."'";
        $sqlNull .= " AND v.id_turno = '".$_GET["turno"]."'";
        $open .= " AND id_turno = '".$_GET["turno"]."'";
        $sqlVenta .= " AND id_turno = '".$_GET["turno"]."'";
         $sqlticket .= " AND v.id_turno = '".$_GET["turno"]."'";
         $sqlboleta .= " AND v.id_turno = '".$_GET["turno"]."'";
         $sqlfactura .= " AND v.id_turno = '".$_GET["turno"]."'";
         $sqlticketDiscount .= " AND v.id_turno = '".$_GET["turno"]."'";
         $sqlboletaDiscount .= " AND v.id_turno = '".$_GET["turno"]."'";
         $sqlfacturaDiscount .= " AND v.id_turno = '".$_GET["turno"]."'";
    }
    if(intval($_GET["caja"])>0){
        $sql .= " AND id_caja = '".$_GET["caja"]."'";
        $sqlNull .= " AND v.id_caja = '".$_GET["caja"]."'";
        $sqlDescuento .= " AND v.id_caja = '".$_GET["caja"]."'";
        $open .= " AND id_caja = '".$_GET["caja"]."'";
        $sqlVenta .= " AND id_caja = '".$_GET["caja"]."'";
         $sqlticket .= " AND v.id_caja = '".$_GET["caja"]."'";
         $sqlboleta .= " AND v.id_caja = '".$_GET["caja"]."'";
         $sqlfactura .= " AND v.id_caja = '".$_GET["caja"]."'";
         $sqlticketDiscount .= " AND v.id_caja = '".$_GET["caja"]."'";
         $sqlboletaDiscount .= " AND v.id_caja = '".$_GET["caja"]."'";
         $sqlfacturaDiscount .= " AND v.id_caja = '".$_GET["caja"]."'";

    }
    $sql.=" GROUP BY fecha";

    $movimientos = $objconn->consulta_matriz($sql);
    $movimientosNull = $objconn->consulta_matriz($sqlNull);
    $movimientosVenta = $objconn->consulta_matriz($sqlVenta);
    $movimientosDescuento = $objconn->consulta_matriz($sqlDescuento);
    $movimientosTotalTicket = $objconn->consulta_matriz($sqlticket);
    $movimientosTotalBoleta = $objconn->consulta_matriz($sqlboleta);
    $movimientosTotalFactura = $objconn->consulta_matriz($sqlfactura);
    $movimientosTotalTicketDiscount = $objconn->consulta_matriz($sqlticketDiscount);
    $movimientosTotalBoletaDiscount = $objconn->consulta_matriz($sqlboletaDiscount);
    $movimientosTotalFacturaDiscount = $objconn->consulta_matriz($sqlfacturaDiscount);
    $montoOpen = $objconn->consulta_arreglo($open);

   /* $sql2 = "SELECT * from movimiento_caja where estado_fila=1 AND fecha_cierre = '".$fecha_cierre."' AND id_caja = 0" ;
     $sql2.=" GROUP BY fecha";
    $movimientos2 = $objconn->consulta_matriz($sql2);*/
}else{
    $sql = "SELECT * from movimiento_caja where estado_fila=1 AND fecha_cierre = '".$fecha_cierre."'";
    $sqlNull = "SELECT vm.id as conc
                FROM venta v
                INNER JOIN venta_medio_pago vm ON v.id = vm.id_venta 
                WHERE total is null AND v.fecha_cierre = '".$fecha_cierre."' ";
    $sqlDescuento = 
            "SELECT medio, vm.monto
            FROM venta v, venta_medio_pago vm
            WHERE  vm.estado_fila=1 AND v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND medio = 'DESCUENTO' ";
    $sqlVenta = "SELECT * from venta where estado_fila=1 AND fecha_cierre = '".$fecha_cierre."'";
    $sqlticket = 
            "SELECT v.total
            FROM venta v
            WHERE  v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 0 and v.estado_fila = 1 ";
    $sqlboleta = 
            "SELECT v.total
            FROM venta v
            WHERE  v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 1 and v.estado_fila = 1 ";
    $sqlfactura = 
            "SELECT v.total
            FROM venta v
            WHERE v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante =2 and v.estado_fila = 1 ";

    $sqlticketDiscount = 
            "SELECT v.total,vm.monto
            FROM venta v, venta_medio_pago vm
            WHERE  v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 0 and v.estado_fila=1 AND vm.medio = 'DESCUENTO'";
    $sqlboletaDiscount = 
           "SELECT v.total,vm.monto
            FROM venta v, venta_medio_pago vm
            WHERE  v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 1 and v.estado_fila=1 AND vm.medio = 'DESCUENTO'";
    $sqlfacturaDiscount = 
            "SELECT v.total,vm.monto
            FROM venta v, venta_medio_pago vm
            WHERE  v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 2 and v.estado_fila=1 AND vm.medio = 'DESCUENTO'";
 $open = "SELECT * FROM movimiento_caja WHERE estado_fila=1 AND tipo_movimiento like '%OPEN|PEN|EFECTIVO%' AND fecha_cierre = '".$fecha_cierre."' ";

    if(intval($_GET["caja"])>0){
        $sql .= " AND id_caja = '".$_GET["caja"]."'";
        $sqlNull .= " AND v.id_caja = '".$_GET["caja"]."'";
        $sqlDescuento .= " AND v.id_caja = '".$_GET["caja"]."'";
        $open .= " AND id_caja = '".$_GET["id_caja"]."'";
         $sqlticket .= " AND id_caja = '".$_GET["id_caja"]."'";
         $sqlboleta .= "AND id_caja = '".$_GET["id_caja"]."'";
         $sqlfactura .= " AND id_caja = '".$_GET["id_caja"]."'";
         $sqlticketDiscount .= " AND id_caja = '".$_GET["caja"]."'";
         $sqlboletaDiscount .= " AND id_caja = '".$_GET["caja"]."'";
         $sqlfacturaDiscount .= " AND id_caja = '".$_GET["caja"]."'";
        $sqlVenta .= " AND id_caja = '".$_GET["caja"]."'";
    }
    $sql.=" GROUP BY fecha";        
   

    $movimientos = $objconn->consulta_matriz($sql);
    $movimientosNull = $objconn->consulta_matriz($sqlNull);
    $movimientosDescuento = $objconn->consulta_matriz($sqlDescuento);
    $movimientosTotalTicket = $objconn->consulta_matriz($sqlticket);
    $movimientosTotalBoleta = $objconn->consulta_matriz($sqlboleta);
    $movimientosTotalFactura = $objconn->consulta_matriz($sqlfactura);
    $movimientosTotalTicketDiscount = $objconn->consulta_matriz($sqlticketDiscount);
    $movimientosTotalBoletaDiscount = $objconn->consulta_matriz($sqlboletaDiscount);
    $movimientosTotalFacturaDiscount = $objconn->consulta_matriz($sqlfacturaDiscount);
    $montoOpen = $objconn->consulta_arreglo($open);
    $movimientosVenta = $objconn->consulta_matriz($sqlVenta);


    // $sqlCre = "SELECT vm.id, medio, monto
    //     FROM venta v, venta_medio_pago vm
    //     WHERE tipo_comprobante = -1 AND fecha_cierre = '".$fecha_cierre."' AND v.id = vm.id_venta";

    // $movimientosCreditos = $objconn->consulta_matriz($sqlCre);

    // for($i=0; $i<count($movimientosCreditos); $i++){
    //     if($movimientosCreditos[$i]["medio"] != 'CREDITO'){
    //         $monto += $movimientosCreditos[$i]["monto"];
    //     }
    // }

    /*$sql2 = "SELECT * from movimiento_caja where estado_fila=1 AND fecha_cierre = '".$fecha_cierre."' AND id_caja = 0" ;
    $sql2.=" GROUP BY fecha"; 
    $movimientos2 = $objconn->consulta_matriz($sql2);*/

}
if(!empty($montoOpen["monto"])){
    $inicial = $montoOpen["monto"];
}

    if(intval($_GET["turno"]) > 0){
        $turno = " AND id_turno = ".$_GET["turno"];
    }else{
        $turno = " ";
    }

    if(intval($_GET["caja"]) > 0){
        $cajasql = " AND mc.monto < 0 AND mc.id_caja = ".$_GET["caja"];
    }else{
        $cajasql = " AND mc.monto < 0";
    }
   // $inicial = $MvMount["monto"];

if(!empty($montoOpen["monto"])){
    $inicial = $montoOpen["monto"];
}
    
$query_liquidado = "SELECT SUM(ROUND(monto,2)) as liquidaciones from movimiento_caja where estado_fila=1 AND fecha_cierre = '".$fecha_cierre."' AND tipo_movimiento like '%LIQ%'";
if(isset($_GET['caja']) && intval($_GET["caja"])>0){
    $query_liquidado .= " AND id_caja = '".$_GET["caja"]."'";
}else{
    $sql .= " AND id_caja IN ".$wherein;
}
$monto_liquidado = $objconn->consulta_arreglo($query_liquidado);
if($monto_liquidado['liquidaciones'] == null){
    $liquidaciones = 0;
}else{
    $liquidaciones = $monto_liquidado['liquidaciones'];
}

if(is_array($movimientosDescuento)){
    foreach ($movimientosDescuento as $mvDesc){
        $descuento += $mvDesc["monto"];
    }
}
if(is_array($movimientosVenta)){
    foreach ($movimientosVenta as $mvVenta){
        if ($mvVenta["total"]!=null) {
             $venta += $mvVenta["total"];
        }
       
    }
  
}

if(is_array($movimientosNull)){
    foreach ($movimientosNull as $mv3){

        $movimientosCajaNull = $objconn->consulta_matriz("SELECT tipo_movimiento, mc.monto
        FROM movimiento_caja mc 
        WHERE estado_fila=1 AND tipo_movimiento LIKE CONCAT('%|', ".$mv3["conc"]." ,'%')");

        if(is_array($movimientosCajaNull)){
            foreach ($movimientosCajaNull as $mv4) {
                if(strpos($mv4["tipo_movimiento"],"SELL") !== FALSE){
                    $total_vendido = $total_vendido - floatval($mv4["monto"]);
                }
                if(strpos($mv4["tipo_movimiento"],"_COBRO") !== FALSE){
                    $cobro = $cobro - floatval($mv4["monto"]);

                    if(strpos($mv4["tipo_movimiento"],"VISA_COBRO") !== FALSE){
                        $cobro_visa = $cobro_visa + floatval($mv4["monto"]);
                    }
                    if(strpos($mv4["tipo_movimiento"],"MASTERCARD_COBRO") !== FALSE){
                        $cobro_mastercard = $cobro_mastercard + floatval($mv4["monto"]);
                    }
                    if(strpos($mv4["tipo_movimiento"],"DEPOSITO_COBRO") !== FALSE){
                        $cobro_deposito = $cobro_deposito + floatval($mv4["monto"]);
                    }
                    if(strpos($mv4["tipo_movimiento"],"EFECTIVO_COBRO") !== FALSE){
                        $cobro_efectivo = $cobro_efectivo + floatval($mv4["monto"]);
                    }
		   if(strpos($mv4["tipo_movimiento"],"YAPE_COBRO") !== FALSE){
                        $cobro_yape = $cobro_yape + floatval($mv4["monto"]);
                    }

                }
                if((strpos($mv4["tipo_movimiento"],"EFECTIVO") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
		
                    $efectivo = $efectivo - floatval($mv4["monto"]);
                }
                if((strpos($mv4["tipo_movimiento"],"VISA") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
			if(floatval($mv4["monto"])>=0){
			 	$visa = $visa + floatval($mv4["monto"]);
			}

                   
                }      
                if((strpos($mv4["tipo_movimiento"],"MASTERCARD") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
			if(floatval($mv4["monto"])>=0){
			 	 $master = $master + floatval($mv4["monto"]);
			}
                   
                }
		if((strpos($mv4["tipo_movimiento"],"YAPE") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
			if(floatval($mv4["monto"])>=0){
			 	  $yape = $yape + floatval($mv4["monto"]);
			}
                  
                }
            }
        }
    }
}

if(is_array($movimientos)){
    foreach ($movimientos as $mv){
       
    //echo "/\n".$mv["id"]."*".floatval($mv["monto"]);
        if(strpos($mv["tipo_movimiento"],"SELL") !== FALSE) {
           //  $total_vendido += floatval(abs($mv["monto"]));
            // echo "---".$total_vendido;
        }
        if(strpos($mv["tipo_movimiento"],"_COBRO") !== FALSE){
            $cobro += floatval($mv["monto"]);

            if(strpos($mv["tipo_movimiento"],"VISA_COBRO") !== FALSE){
                $cobro_visa += floatval($mv["monto"]);
            }
            if(strpos($mv["tipo_movimiento"],"MASTERCARD_COBRO") !== FALSE){
                $cobro_mastercard += floatval($mv["monto"]);
            }
            if(strpos($mv["tipo_movimiento"],"DEPOSITO_COBRO") !== FALSE){
                $cobro_deposito += floatval($mv["monto"]);
            }
            if(strpos($mv["tipo_movimiento"],"EFECTIVO_COBRO") !== FALSE){
                $cobro_efectivo += floatval($mv["monto"]);
            }
	 if(strpos($mv["tipo_movimiento"],"YAPE_COBRO") !== FALSE){
                $cobro_yape += floatval($mv["monto"]);
            }
        }
        if(strpos($mv["tipo_movimiento"],"_DETRACCION") !== FALSE){
            $detraccion += floatval($mv["monto"]);

            if(strpos($mv["tipo_movimiento"],"VISA_DETRACCION") !== FALSE){
                $detraccion_visa += floatval($mv["monto"]);
            }
            if(strpos($mv["tipo_movimiento"],"MASTERCARD_DETRACCION") !== FALSE){
                $detraccion_mastercard += floatval($mv["monto"]);
            }
            if(strpos($mv["tipo_movimiento"],"EFECTIVO_DETRACCION") !== FALSE){
                $detraccion_efectivo += floatval($mv["monto"]);
            }
		if(strpos($mv["tipo_movimiento"],"YAPE_DETRACCION") !== FALSE){
                $detraccion_yape += floatval($mv["monto"]);
            }
        }
       
        if((strpos($mv["tipo_movimiento"],"EFECTIVO") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){


            // echo $efectivo ."+". floatval($mv["monto"]) ."=".$efectivo + floatval($mv["monto"])." \n\n <br>";
            $efectivo += floatval($mv["monto"]);

            // echo floatval($mv["monto"])." \n\n <br>";

        }
        if((strpos($mv["tipo_movimiento"],"VISA") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){
		if(floatval($mv["monto"])>=0){
			 	  $visa += floatval($mv["monto"]);
			}
           
        }      
        if((strpos($mv["tipo_movimiento"],"MASTERCARD") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){
		if(floatval($mv["monto"])>=0){
			 	  $master += floatval($mv["monto"]);
			}
           
        }
	if((strpos($mv["tipo_movimiento"],"YAPE") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){
			if(floatval($mv["monto"])>=0){
			 	  $yape += floatval($mv["monto"]);
			}
           
        }
        // if(strpos($mv["tipo_movimiento"],"OPEN") !== FALSE){
        //     $inicial = $inicial + floatval($mv["monto"]);
        // }
        if(strpos($mv["tipo_movimiento"],"INBX") !== FALSE){
            $adicional += floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"OUTBX") !== FALSE){
            $salidas += floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"SELL|PEN|EFECTIVO") !== FALSE){
            $soles += floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"PEN|DESCUENTO") !== FALSE){
            $descuentos += floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"USD|EFECTIVO") !== FALSE){
            $dolares += floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"BUY") !== FALSE){
            $salidas += floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"PEN|CREDITO") !== FALSE){
            $credito += floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"PEN|NOTACREDITO") !== FALSE){
            $notacred += floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"EXT") !== FALSE){
                $pruebacotos= $pruebacotos."|".$mv["monto"];
                $salidas_cobro = $salidas_cobro + floatval($mv2["monto"]);
            }
    }
 // echo "venta--". $venta;
    $total_vendido= $venta-$descuento;
    $efectivo =  $total_vendido-$visa-$master-$detraccion_efectivo-$yape;
    $soles =  ($efectivo + $inicial + $adicional) -abs($salidas)- $liquidaciones-$detraccion_efectivo;
    $dolares = $dolares/floatval($cambio["compra"]);
    //Restar Liquidaciones
    $caja = ($efectivo + $dolares + $inicial + $adicional) -abs($salidas) - $liquidaciones-$detraccion_efectivo;
    $caja_u = ($efectivo + $dolares + $adicional) - abs($salidas)  - $liquidaciones;
}

if(is_array($movimientosTotalTicket)){
    foreach ($movimientosTotalTicket as $t){
            $total_ticket += floatval($t["total"]);
    }
}
if(is_array($movimientosTotalBoleta)){
    foreach ($movimientosTotalBoleta as $b){
            $total_boleta = $total_boleta + floatval($b["total"]);
    }
}
if(is_array($movimientosTotalFactura)){
    foreach ($movimientosTotalFactura as $f){
            $total_factura = $total_factura + floatval($f["total"]);
    }
}
if(is_array($movimientosTotalTicketDiscount)){
    foreach ($movimientosTotalTicketDiscount as $td){
            $total_ticket_discount += floatval($td["monto"]);
    }
}
if(is_array($movimientosTotalBoletaDiscount)){
    foreach ($movimientosTotalBoletaDiscount as $bd){
            $total_boleta_discount += floatval($bd["monto"]);
    }
}
if(is_array($movimientosTotalFacturaDiscount)){
    foreach ($movimientosTotalFacturaDiscount as $fd){
            $total_factura_discount += floatval($df["monto"]);
    }
}

    $res_turno = $objconn->consulta_arreglo("Select nombre from turno where id = {$_GET['turno']}");
    $res_caja = $objconn->consulta_arreglo("Select nombre from caja where id = {$_GET['caja']}");
    $res_usuario = $objconn->consulta_arreglo("Select nombres_y_apellidos from usuario where id = {$_GET['usuario']}");
    ?>
<hr>
<h3 class="title"><?php echo $config['nombre_negocio']; ?></h3>
<p class="title"><?php echo $config['ruc']; ?></p>
<p class="title"><?php echo $config['razon_social']; ?></p>
<p class="title"><?php echo $config['direccion']; ?></p>
<p class="title"><?php echo $config['telefono']; ?></p>
<hr>
<p class="title">CIERRE</p>
<p class="title">Fecha: <?php echo $fecha_cierre; ?></p><p class="title">Turno: <?php echo empty($res_turno['nombre']) ? 'TODOS' : $res_turno['nombre']; ?></p>
<p class="title">Caja: <?php echo empty($res_caja['nombre']) ? 'TODOS' : $res_caja['nombre']; ?></p>
<!-- <p class="title">CAJA</p>
<p class="title">Alejandra Lunes a Jueves</p>
<p class="title">Astrid Vienes y Sabado</p> -->



<hr>
    <table align="center">
        <tr>
            <td>Total Ticket</td>
            <td class="precio"><?php echo number_format($total_ticket-$total_ticket_discount,2);?></td>
        </tr>
        <tr>
            <td>Total boleta</td>
            <td class="precio"><?php echo number_format($total_boleta -$total_boleta_discount,2);?></td>
        </tr>
        <tr>
            <td>Total Factura</td>
            <td class="precio"><?php echo number_format($total_factura-$total_factura_discount ,2);?></td>
        </tr>
      
        <tr>
            <td>Total Vendido</td>
            <td class="precio"><?php echo number_format(($total_vendido+ $cobro)-$notacred-$detraccion,2);?></td>
        </tr>
        <tr>
            <td>Efectivo</td>
            <td class="precio"><?php echo number_format($efectivo,2);?></td>
        </tr>
        <tr>
            <td>Visa</td>
            <td class="precio"><?php echo number_format($visa,2);?></td>
        </tr>
        <tr>
            <td>MasterCard</td>
            <td class="precio"><?php echo number_format($master,2);?></td>
        </tr>
	<tr>
            <td>Yape</td>
            <td class="precio"><?php echo number_format($yape,2);?></td>
        </tr>
        <tr>
            <td>Monto Inicial</td>
            <td class="precio"><?php echo number_format($inicial,2);?></td>
        </tr>
        <tr>
            <td>Ingresos Adicionales</td>
            <td class="precio"><?php echo number_format($adicional,2);?></td>
        </tr>
      
        <tr>
            <td>Salidas</td>
            <td class="precio"><?php echo number_format(abs($salidas),2);?></td>
        </tr>
          <?php 
        
                $gastos = $objconn->consulta_matriz("
                    SELECT mc.monto as monto, descripcion
                    FROM movimiento_caja mc
                    INNER JOIN entrada_salida es ON mc.id = es.id_movimiento_caja
                    where fecha_cierre = '".$fecha_cierre."' ".$cajasql." ".$turno." ");
            // }else{
            //     $gastos = $objconn->consulta_matriz("
            //         SELECT mc.monto as monto, descripcion
            //         FROM movimiento_caja mc
            //         INNER JOIN entrada_salida es ON mc.id = es.id_movimiento_caja
            //         where fecha_cierre = '".$fecha_cierre."' ".$caja."");
            // }

        ?>
        <?php
            if (is_array($gastos)):
                foreach ($gastos as $p):
                    ?>
                    <tr>
                        <td><b><?php echo "-- " .utf8_decode($p['descripcion']); ?></b></td>
                        <td class="precio"><b><?php echo number_format(abs($p['monto']),2); ?></b></td>
                    </tr>
                    <?php
                endforeach;
            endif;
        ?>
        <tr>
            <td>Descuentos</td>
            <td class="precio"><?php echo number_format($descuento,2);?></td>
        </tr>
        <tr>
            <td>Soles Caja</td>
            <td class="precio"><?php echo number_format($soles,2);?></td>
        </tr>
        <tr>
            <td>Dolares Caja</td>
            <td class="precio"><?php echo number_format($dolares,2);?></td>
        </tr>
        <tr>
            <td>Total en Caja</td>
            <td class="precio"><?php echo number_format($caja,2);?></td>
        </tr>
        <tr>
            <td><b>Utilidad Caja</b></td>
            <td class="precio"><b><?php echo number_format($caja_u,2);?></b></td>
        </tr>
        <tr>
            <td>Fondos Externos en Creditos</td>
            <td class="precio"><?php echo number_format($credito,2);?></td>
        </tr>
        <tr>
            <td>Pagado en Fondos Externos</td>
            <td class="precio"><?php echo number_format($cobro,2);?></td>
        </tr>
        <tr>
            <td>Salidas Fondos Externos</td>
            <td class="precio"><?php echo number_format(abs($salidas_cobro),2);?></td>
        </tr>
    </table>
    <br>
    <br>

    <p class="title">IMPRESO POR: <?php echo $res_usuario['nombres_y_apellidos']; ?></p>
    <br>
</body>
</html>
