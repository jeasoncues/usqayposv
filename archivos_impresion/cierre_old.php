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
$descuento = 0;
$descuentos = 0;

$movimientos = null;
$sql = "";
// echo $_COOKIE['id_caja'];
if(isset($_GET["turno"])){
    $sql = "Select * from movimiento_caja where fecha_cierre = '".$_GET["fecha"]."'";
     $sqlNull = "SELECT vm.id as conc
                FROM venta v
                INNER JOIN venta_medio_pago vm ON v.id = vm.id_venta 
                WHERE total is null AND v.fecha_cierre = '".$_GET["fecha"]."' ";

    $sqlticket = "SELECT ROUND(SUM(total),2) AS total FROM venta v WHERE fecha_cierre = '".$fecha_cierre."' AND tipo_comprobante = 0 ";
    $sqlboleta = "SELECT ROUND(SUM(total),2) AS total FROM venta v WHERE fecha_cierre = '".$fecha_cierre."' AND tipo_comprobante = 1 ";
    $sqlfactura = "SELECT ROUND(SUM(total),2) AS total FROM venta v WHERE fecha_cierre = '".$fecha_cierre."' AND tipo_comprobante = 2 ";

    $sqlDescuento = 
            "SELECT medio, vm.monto
            FROM venta v, venta_medio_pago vm
            WHERE v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND medio = 'DESCUENTO' ";

    $mount = "SELECT * FROM movimiento_caja WHERE tipo_movimiento like '%OPEN|PEN|EFECTIVO%' AND fecha_cierre = '".$fecha_cierre."' ";

    if(intval($_GET["turno"])>0){
        $sql .= " AND id_turno = '".$_GET["turno"]."'";
        $sqlNull .= " AND v.id_turno = '".$_GET["turno"]."'";
        $sqlDescuento .= " AND v.id_turno = '".$_GET["turno"]."'";
        $sqlticket .= " AND v.id_turno = '".$_GET["turno"]."'";
        $sqlboleta .= " AND v.id_turno = '".$_GET["turno"]."'";
        $sqlfactura .= " AND v.id_turno = '".$_GET["turno"]."'";
    }
    if(intval($_GET["caja"])>0){
        $sql .= " AND id_caja = '".$_GET["caja"]."'";
        $sqlNull .= " AND v.id_caja = '".$_GET["caja"]."'";
        $sqlDescuento .= " AND v.id_caja = '".$_GET["caja"]."'";
        $sqlticket .= " AND v.id_caja = '".$_GET["caja"]."'";
        $sqlboleta .= " AND v.id_caja = '".$_GET["caja"]."'";
        $sqlfactura .= " AND v.id_caja = '".$_GET["caja"]."'";
        $mount .= " AND id_caja = '".$_GET["caja"]."'";
    }
    $movimientos = $objconn->consulta_matriz($sql);
    $ticket = $objconn->consulta_arreglo($sqlticket);
    $boleta = $objconn->consulta_arreglo($sqlboleta);
    $factura = $objconn->consulta_arreglo($sqlfactura);
    $movimientosNull = $objconn->consulta_matriz($sqlNull);
    $movimientosDescuento = $objconn->consulta_matriz($sqlDescuento);

    $MvMount = $objconn->consulta_arreglo($mount);

    $sql2 = "Select * from movimiento_caja where fecha_cierre = '".$fecha_cierre."' AND id_caja = 0" ;
    $movimientos2 = $objconn->consulta_matriz($sql2);
}else{
    $sql = "Select * from movimiento_caja where fecha_cierre = '".$fecha_cierre."'";

    $sqlticket = "SELECT ROUND(SUM(total),2) AS total FROM venta v WHERE fecha_cierre = '".$fecha_cierre."' AND tipo_comprobante = 0 ";
    $sqlboleta = "SELECT ROUND(SUM(total),2) AS total FROM venta v WHERE fecha_cierre = '".$fecha_cierre."' AND tipo_comprobante = 1 ";
    $sqlfactura = "SELECT ROUND(SUM(total),2) AS total FROM venta v WHERE fecha_cierre = '".$fecha_cierre."' AND tipo_comprobante = 2 ";

    $sqlNull = "SELECT vm.id as conc
                FROM venta v
                INNER JOIN venta_medio_pago vm ON v.id = vm.id_venta 
                WHERE total is null AND v.fecha_cierre = '".$fecha_cierre."' ";
    $sqlDescuento = 
            "SELECT medio, vm.monto
            FROM venta v, venta_medio_pago vm
            WHERE v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND medio = 'DESCUENTO' ";

    if(intval($_GET["caja"])>0){
        $sql .= " AND id_caja = '".$_GET["caja"]."'";
        $sqlNull .= " AND v.id_caja = '".$_GET["caja"]."'";
        $sqlDescuento .= " AND v.id_caja = '".$_GET["caja"]."'";
        $mount .= " AND id_caja = '".$_COOKIE["id_caja"]."'";
        $sqlticket .= " AND id_caja = '".$_COOKIE["id_caja"]."'";
        $sqlboleta .= " AND id_caja = '".$_COOKIE["id_caja"]."'";
        $sqlfactura .= " AND id_caja = '".$_COOKIE["id_caja"]."'";
    }

    $ticket = $objconn->consulta_arreglo($sqlticket);
    $boleta = $objconn->consulta_arreglo($sqlboleta);
    $factura = $objconn->consulta_arreglo($sqlfactura);
    $movimientos = $objconn->consulta_matriz($sql);
    $movimientosNull = $objconn->consulta_matriz($sqlNull);
    $movimientosDescuento = $objconn->consulta_matriz($sqlDescuento);

    $MvMount = $objconn->consulta_arreglo($mount);

    $sql2 = "Select * from movimiento_caja where fecha_cierre = '".$fecha_cierre."' AND id_caja = 0" ;
    $movimientos2 = $objconn->consulta_matriz($sql2);

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
    $inicial = $MvMount["monto"];

if(is_array($movimientosNull)){
    foreach ($movimientosNull as $mv3){

        $movimientosCajaNull = $objconn->consulta_matriz("SELECT tipo_movimiento, mc.monto
        FROM movimiento_caja mc 
        WHERE tipo_movimiento LIKE CONCAT('%|', ".$mv3["conc"]." ,'%')");

        if(is_array($movimientosCajaNull)){
            foreach ($movimientosCajaNull as $mv4) {
                if(strpos($mv4["tipo_movimiento"],"SELL") !== FALSE){
                    $total_vendido = $total_vendido - floatval($mv4["monto"]);
                }
                // if(strpos($mv4["tipo_movimiento"],"_COBRO") !== FALSE){
                //     $cobro = $cobro - floatval($mv4["monto"]);
                // }
                if((strpos($mv4["tipo_movimiento"],"EFECTIVO") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
                    $efectivo = $efectivo - floatval($mv4["monto"]);
                }
                if((strpos($mv4["tipo_movimiento"],"VISA") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
                    $visa = $visa + floatval($mv4["monto"]);
                }      
                if((strpos($mv4["tipo_movimiento"],"MASTERCARD") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
                    $master = $master + floatval($mv4["monto"]);
                }
            }
        }
    }
}


if(is_array($movimientosDescuento)){
    foreach ($movimientosDescuento as $mvDesc){
        $descuento += $mvDesc["monto"];
    }
}


if(is_array($movimientos)){
    foreach ($movimientos as $mv){
        if(strpos($mv["tipo_movimiento"],"SELL") !== FALSE){
            $total_vendido = $total_vendido + floatval($mv["monto"]);
        }
        // if(strpos($mv["tipo_movimiento"],"_COBRO") !== FALSE){
        //     $cobro = $cobro + floatval($mv["monto"]);
        // }
        if((strpos($mv["tipo_movimiento"],"EFECTIVO") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){
            $efectivo = $efectivo + floatval($mv["monto"]);
        }
        if((strpos($mv["tipo_movimiento"],"VISA") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){
            $visa = $visa + floatval($mv["monto"]);
        }      
        if((strpos($mv["tipo_movimiento"],"MASTERCARD") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){
            $master = $master + floatval($mv["monto"]);
        }
        // if(strpos($mv["tipo_movimiento"],"OPEN") !== FALSE){
        //     $inicial = $inicial + floatval($mv["monto"]);
        // }
        if(strpos($mv["tipo_movimiento"],"INBX") !== FALSE){
            $adicional = $adicional + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"OUTBX") !== FALSE){
            $salidas = $salidas + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"PEN|EFECTIVO") !== FALSE){
            $soles = $soles + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"PEN|DESCUENTO") !== FALSE){
            $descuentos = $descuentos + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"USD|EFECTIVO") !== FALSE){
            $dolares = $dolares + floatval($mv["monto"]);
        }
        if(strpos($mv["tipo_movimiento"],"BUY") !== FALSE){
            $salidas = $salidas + floatval($mv["monto"]);
        }
        // if(strpos($mv["tipo_movimiento"],"PEN|CREDITO") !== FALSE){
        //     $credito = $credito + floatval($mv["monto"]);
        // }
    }
    $efectivo = $efectivo;
    $soles =  ($efectivo + $inicial + $adicional)- abs($salidas)   ;
    $dolares = $dolares/floatval($cambio["compra"]);
    $caja = ($efectivo + $dolares + $inicial + $adicional) - abs($salidas);
    $caja_u = ($efectivo + $dolares + $adicional) - abs($salidas);
}

// if(is_array($movimientos2)){
//     foreach ($movimientos2 as $mv2){
//         if(strpos($mv2["tipo_movimiento"],"EXT") !== FALSE){
//             $salidas_cobro = $salidas_cobro + floatval($mv2["monto"]);
//         }
//     }
// }

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
            <td><b>Total Ticket</b></td>
            <td class="precio">
                <?php 
                    if(is_numeric($ticket['total']))
                        echo number_format($ticket['total'],2);
                    else 
                        echo "0.00"
                ?>
            </td>
        </tr>
        <tr>
            <td><b>Total Boleta</b></td>
            <td class="precio"><?php 
                    if(is_numeric($boleta['total']))
                        echo number_format($boleta['total'],2);
                    else 
                        echo "0.00"
                ?></td>
        </tr>
        <tr>
            <td><b>Total Factura</b></td>
            <td class="precio"><?php 
                    if(is_numeric($factura['total']))
                        echo number_format($factura['total'],2);
                    else 
                        echo "0.00"
                ?></td>
        </tr>
        <tr>
            <td>Total Vendido</td>
            <td class="precio"><?php echo number_format($total_vendido + $cobro,2);?></td>
        </tr>
        <tr>
            <td>Efectivo</td>
            <td class="precio"><?php echo number_format($efectivo,2);?></td>
        </tr>
        <tr>
            <td>Visa</td>
            <td class="precio"><?php echo number_format($visa,2);?></td>
        </tr>
        <!-- <tr>
            <td>MasterCard</td>
            <td class="precio"><?php echo number_format($master,2);?></td>
        </tr> -->
        <tr>
            <td>Monto Inicial</td>
            <td class="precio"><?php echo number_format($inicial,2);?></td>
        </tr>
        <tr>
            <td>Ingresos Adicionales</td>
            <td class="precio"><?php echo number_format($adicional,2);?></td>
        </tr>
        <?php 
        
                $gastos = $objconn->consulta_matriz("
                    SELECT mc.monto as monto, descripcion
                    FROM movimiento_caja mc
                    INNER JOIN entrada_salida es ON mc.id = es.id_movimiento_caja
                    where fecha_cierre = '".$fecha_cierre."' ".$cajasql." ".$turno." ");
            

        ?>
        <?php
            if (is_array($gastos)):
                foreach ($gastos as $p):
                    ?>
                    <tr>
                        <td><b><?php echo "-- " .$p['descripcion']; ?></b></td>
                        <td class="precio"><b><?php echo number_format(abs($p['monto']),2); ?></b></td>
                    </tr>
                    <?php
                endforeach;
            endif;
        ?>
        <tr>
            <td>Salidas</td>
            <td class="precio"><?php echo number_format(abs($salidas),2);?></td>
        </tr>
        <tr>
            <td>Descuentos</td>
            <td class="precio"><?php echo number_format($descuento + $descuentos,2);?></td>
        </tr>
        <tr>
            <td>Soles Caja</td>
            <td class="precio"><?php echo number_format($soles,2);?></td>
        </tr>
        <!-- <tr>
            <td>Dolares Caja</td>
            <td class="precio"><?php echo number_format($dolares,2);?></td>
        </tr>
        <tr>
            <td>Total en Caja</td>
            <td class="precio"><?php echo number_format($caja,2);?></td>
        </tr> -->
        <tr>
            <td><b>Utilidad Caja</b></td>
            <td class="precio"><b><?php echo number_format($caja_u,2);?></b></td>
        </tr>
        <!-- <tr>
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
        </tr> -->
    </table>
    <br>
    <br>

    <p class="title">IMPRESO POR: <b><?php echo $res_usuario['nombres_y_apellidos']; ?></b> El <b><?php echo date('d-m-Y'); ?></b> A LAS <b><?php echo date('H:i:s'); ?></b></p>
    <br>
</body>
</html>
