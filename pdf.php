<style>
    .table>thead>th {
        background-color: #216eb7 !important;
    }

    .table {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }
</style>
<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
require_once __DIR__ . '/vendor/autoload.php';
include_once('nucleo/include/MasterConexion.php');
include_once('nucleo/venta.php');
include_once('nucleo/producto_venta.php');
include_once('nucleo/servicio_venta.php');
include_once('nucleo/producto.php');
include_once('nucleo/usuario.php');
include_once('nucleo/servicio.php');
include_once('nucleo/caja.php');
include_once('nucleo/cliente.php');
include_once('nucleo/turno.php');

$conn = new MasterConexion();
$obj = new venta();

$caja_actual = $_COOKIE["id_caja"];
$usr_actual = $_COOKIE["id_usuario"];

$zero = 0;
$uno = 0;
$dos = 0;
$tres = 0;
$cuatro = 0;
$subtotalTable = 0;

$fechaInicio = date('Y-m-d');
$fechaFin = date('Y-m-d');
// $fechaCierre=$obj->fechaCierre();
if (isset($_GET['fecha_inicio'])) {
    $fechaInicio = $_GET['fecha_inicio'];
    $fechaVar = $fechaInicio;
}

if (isset($_GET['fecha_fin'])) {
    $fechaFin = $_GET['fecha_fin'];
}

if (isset($_GET["caja"])) {
    $caja_actual = $_GET["caja"];
}
$usr_sql = "";
if (isset($_GET["usr"])) {
    $usr = $_GET["usr"];

    if ($usr != "") {
        $usr_sql = ' AND id_usuario = ' . $usr;
    } else {
        $usr_sql = "";
    }
}

$stockAnterior = 0.00;
$stockIngreso = 0.00;
$stockSalida = 0;
$totalvendido = 0;
$totalimpuestos =  0;

$objs = $conn->consulta_matriz("SELECT * FROM venta where fecha_cierre between '" . $fechaInicio . " 00:00:00' 
    AND '" . $fechaFin . " 23:59:59' AND estado_fila IN (1,2,3,4) 
    AND id_caja = '{$caja_actual}' {$usr_sql} ORDER BY id DESC");


$totalvendido = $conn->consulta_arreglo("SELECT sum(total)as total FROM venta where estado_fila IN (1,3,4) and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND id_caja = '{$caja_actual}' {$usr_sql}");
$totalimpuestos = $conn->consulta_arreglo("SELECT sum(total_impuestos)as impuestos FROM venta where estado_fila IN (1,3,4)  and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND id_caja = '{$_COOKIE["id_caja"]}'");
$subtotal = $conn->consulta_arreglo("SELECT sum(subtotal)as subtotal FROM venta where estado_fila IN (1,3,4)  and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND id_caja = '{$caja_actual}' {$usr_sql}");

$descuento_diario = $conn->consulta_arreglo("SELECT ROUND(SUM(monto),2) as descuento
        FROM venta v
        INNER JOIN venta_medio_pago vm ON vm.id_venta = v.id
        WHERE v.fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND medio = 'DESCUENTO' AND v.estado_fila = 1 AND id_caja = '{$caja_actual}' {$usr_sql} ORDER BY v.id DESC");

if (isset($_GET['opcion'])) {
    $tipo = $_GET['opcion'];

    if ($tipo == 3) {
        $objs = $conn->consulta_matriz("SELECT * FROM venta where fecha_cierre 
            between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' 
            AND estado_fila = 2 {$usr_sql} ORDER BY id DESC");
    } else {
        if ($tipo == "-") {
            $compr = "IN (0,1,2,-1) ";
        } else {
            $compr = "= " . $tipo;
        }

        // echo "SELECT * FROM venta where fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' AND tipo_comprobante {$compr} AND id_caja = '{$caja_actual}' {$usr_sql} ORDER BY id DESC";
        $objs = $conn->consulta_matriz("SELECT * FROM venta where fecha_cierre 
            between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' 
            AND tipo_comprobante {$compr} AND id_caja = '{$caja_actual}' {$usr_sql} ORDER BY id DESC");

        $totalvendido = $conn->consulta_arreglo("SELECT sum(total)as total FROM venta where estado_fila IN (1,3,4)  and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND tipo_comprobante {$compr} AND id_caja = '{$caja_actual}' {$usr_sql}");
        $totalimpuestos = $conn->consulta_arreglo("SELECT sum(total_impuestos)as impuestos FROM venta where estado_fila IN (1,3,4)  and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND tipo_comprobante {$compr} AND id_caja = '{$caja_actual}' {$usr_sql}");
        $subtotal = $conn->consulta_arreglo("SELECT sum(subtotal)as subtotal FROM venta where estado_fila IN (1,3,4)  and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND tipo_comprobante {$compr} AND id_caja = '{$caja_actual}' {$usr_sql}");

        $descuento_diario = $conn->consulta_arreglo("SELECT ROUND(SUM(monto),2) as descuento
        FROM venta v
        INNER JOIN venta_medio_pago vm ON vm.id_venta = v.id
        WHERE v.fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND medio = 'DESCUENTO' AND v.estado_fila = 1 AND tipo_comprobante {$compr} AND id_caja = '{$caja_actual}' {$usr_sql} ORDER BY v.id DESC");
    }
} else $tipo = '';



$restaTotalCred = 0;
$restaTotalImpCred = 0;
$restaSubTotalCred = 0;
//echo json_encode($objs)
if ($objs != 0) {
    for ($i = 0; $i < count($objs); $i++) {
        if ($objs[$i]['estado_fila'] == '1') {
            if ($objs[$i]['tipo_comprobante'] == '0')
                $zero += $objs[$i]['total'];
            else if ($objs[$i]['tipo_comprobante'] == '1')
                $uno += $objs[$i]['total'];
            else if ($objs[$i]['tipo_comprobante'] == '2')
                $dos += $objs[$i]['total'];
        } else if ($objs[$i]['estado_fila'] == '3') {
            $tres += $objs[$i]['total'];

            $flag = $conn->consulta_arreglo("SELECT * FROM ventas_notas WHERE id_venta_nota = " . $objs[$i]['id']);
            if (isset($flag['total'])) {
                $restaTotalCred += $flag['total'];
            }
            if (isset($flag['total_impuestos'])) {
                $restaTotalImpCred += $flag['total_impuestos'];
            }
            if (isset($flag['subtotal'])) {
                $restaSubTotalCred += $flag['subtotal'];
            }
            // $restaTotalImpCred += $flag['total_impuestos'];
            // $restaSubTotalCred += $flag['subtotal'];

            // $notaCred = $conn->consulta_matriz("SELECT * FROM venta_medio_pago WHERE id_venta = ".$objs[$i]['id']);
            // if(is_array($notaCred)){
            //     for($j=0; $j<count($notaCred); $j++){
            //         $flag = $conn->consulta_arreglo("SELECT * FROM ventas_notas WHERE id_venta_nota = ".$objs[$i]['id']);
            //         if(is_array($flag)){
            //             $subTotal = $notaCred[$j]['monto'] / 1.18;
            //             $restaTotalCred += $notaCred[$j]['monto'];
            //             $restaTotalImpCred += $notaCred[$j]['monto'] - $subTotal;
            //             $restaSubTotalCred += $subTotal;
            //         }
            //     }
            // }
        } else if ($objs[$i]['estado_fila'] == '4')
            $cuatro += $objs[$i]['total'];
    }
}


$tableHTML = "<table class='table'>";
//$tableHTML .= "<thead>";
$tableHTML .= "<tr>";
$tableHTML .= "<th><center>Nro de Pedido</center></th>";
$tableHTML .= "<th><center>Cliente</center></th>";
$tableHTML .= "<th><center>Usuario / Diseñador</center></th>";
$tableHTML .= "<th><center>Turno</center></th>";
$tableHTML .= "<th><center>Caja</center></th>";
$tableHTML .= "<th><center>Tipo de Comprobante</center></th>";
$tableHTML .= "<th><center>Estado</center></th>";
$tableHTML .= "<th><center>Fecha y hora de Pedido</center></th>";
$tableHTML .= "<th><center>Fecha Cierre</center></th>";
$tableHTML .= "<th><center>Dscto.</center></th>";
$tableHTML .= "<th><center>SubTotal</center></th>";
$tableHTML .= "<th><center>Total Impuestos</center></th>";
$tableHTML .= "<th><center>Total</center></th>";
$tableHTML .= "</tr>";
//$tableHTML .= "</thead>";
$tableHTML .= "<tbody>";
$total_desc = 0;
$config =  $conn->consulta_arreglo("SELECT * FROM configuracion");
if (is_array($objs)) {
    foreach ($objs as $o) {
        $dscto = 0;

        /** Id */
        $idRow = $o['id'];

        /** Cliente */
        if ($o['id_cliente'] > 0) {
            $objcliente = new cliente();
            $objcliente->setVar('id', $o['id_cliente']);
            $objcliente->getDB();
            $clienteRow = $objcliente->getNombre();
        } else {
            $clienteRow = "";
        }

        /** Usuario */
        $objusuario = new usuario();
        // echo($o['id_usuario']);
        $objusuario->setVar('id', $o['id_usuario']);
        $objusuario->getDB();
        $objDisenador = new usuario();
        $objDisenador->setVar('id', $o['id_usuario_referente']);
        $objDisenador->getDB();
        $usuarioRow = $objusuario->getNombresYApellidos() . " / " . $objDisenador->getNombresYApellidos();

        /** Turno */
        $objoturno = new turno();
        $objoturno->setVar('id', $o['id_turno']);
        $objoturno->getDB();
        $turnoRow = $objoturno->getNombre();

        /** Caja */
        $objocaja = new caja();
        $objocaja->setVar('id', $o['id_caja']);
        $objocaja->getDB();
        $cajaRow = $objocaja->getNombre();

        /** Tipo Comprobante */
        if ($o['tipo_comprobante'] == 1) {
            $bol =  $conn->consulta_arreglo("SELECT * FROM boleta WHERE id_venta = " . $o["id"]);
            if (is_array($bol))
                $comprobanteRow = $config["serie_boleta"] . "-" . $bol["id"];
            else
                $comprobanteRow = "boleta";
        } elseif ($o['tipo_comprobante'] == 2) {
            $fac =  $conn->consulta_arreglo("SELECT * FROM factura WHERE id_venta = " . $o["id"]);
            if (is_array($fac))
                $comprobanteRow = $config["serie_factura"] . "-" . $fac["id"];
            else
                $comprobanteRow = "Factura";
        } elseif ($o['tipo_comprobante'] == -1 && $o['estado_fila'] == 1) {
            $comprobanteRow = "Credito";
        } elseif ($o['tipo_comprobante'] == 0) {
            $comprobanteRow = "Sin Comprobante";
        }

        /** Estado */
        if ($o['estado_fila'] == 1) {
            $estadoRow = "Emitida";
        } else if ($o['estado_fila'] == 2) {
            $estadoRow = "Anulada";
        } else if ($o['estado_fila'] == 3) {
            $estadoRow = "Nota de Credito";
        } else {
            $estadoRow = "Nota de Debito";
        }

        /** Fecha Hora */
        $fechaHoraRow = $o['fecha_hora'];
        $fechaCierreRow = $o['fecha_cierre'];

        /** Descuento */
        $descto = $conn->consulta_arreglo(
            "SELECT ROUND(SUM(monto),2) AS dscto
                                                                        FROM venta_medio_pago
                                                                        WHERE id_venta = {$o['id']} AND medio = 'DESCUENTO'
                                                                        GROUP BY medio"
        );

        if (is_array($descto)) {
            $dscto = floatval($descto['dscto']);
            $total_desc += $dscto;
        }

        /** SubTotal */
        $subTotalRow = floatval($o['subtotal']) ? number_format(floatval($o['subtotal']), 2) : '0';

        /** Impuestos */
        $impuestosRow = floatval($o['total_impuestos']) ? number_format(floatval($o['total_impuestos']), 2) : '0';

        /** Total */
        $totalRow = floatval($o['total']) ?  number_format(floatval($o['total'] - $dscto), 2) : '0';

        /** Producto Venta */
        $producto_venta =  $conn->consulta_matriz(
            "SELECT pv.id, pv.id_venta, pv.id_producto, p.nombre, pv.precio, pv.precioaux, pv.cantidad,
            pv.largo, pv.ancho, pv.total
            FROM producto_venta pv
            INNER JOIN producto p
            ON pv.id_producto = p.id  WHERE pv.estado_fila = 1 AND pv.id_venta = " . $o["id"]
        );

        $tableHTML .= "<tr>";
        $tableHTML .= "<td style='text-align: center;'>" . $idRow . "</td>";
        $tableHTML .= "<td style='text-align: center;'>" . $clienteRow . "</td>";
        $tableHTML .= "<td style='text-align: center;'>" . $usuarioRow . "</td>";
        $tableHTML .= "<td style='text-align: center;'>" . $turnoRow . "</td>";
        $tableHTML .= "<td style='text-align: center;'>" . $cajaRow . "</td>";
        $tableHTML .= "<td style='text-align: center;'>" . $comprobanteRow . "</td>";
        $tableHTML .= "<td style='text-align: center;'>" . $estadoRow . "</td>";
        $tableHTML .= "<td style='text-align: center;'>" . $fechaHoraRow . "</td>";
        $tableHTML .= "<td style='text-align: center;'>" . $fechaCierreRow . "</td>";
        $tableHTML .= "<td style='text-align: center;'>" . $descto . "</td>";
        $tableHTML .= "<td style='text-align: center;'>" . $subTotalRow . "</td>";
        $tableHTML .= "<td style='text-align: center;'>" . $impuestosRow . "</td>";
        $tableHTML .= "<td style='text-align: center;'>" . $totalRow . "</td>";
        $tableHTML .= "</tr>";
        $tableHTML .= "<tr>";
        $tableHTML .= "<td colspan='13'>";
        $tableHTML .= "<table class='tbl'>";
        $tableHTML .= "<thead>";
        $tableHTML .= "<tr>";
        $tableHTML .= "<th width='50%'><center>Producto</center></th>";
        $tableHTML .= "<th><center>Cantidad</center></th>";
        $tableHTML .= "<th><center>Precio</center></th>";
        $tableHTML .= "<th><center>Total</center></th>";
        $tableHTML .=  "</tr>";
        $tableHTML .= "<thead>";
        $tableHTML .= "<tbody>";

        foreach ($producto_venta as $key => $pv) {

            if ($pv['precio'] == 0) {
                $precio = $pv['precioaux'];
                $cantidad = $pv['largo'] . " x " . $pv['ancho'];
            } else {
                $precio = $pv['precio'];
                $cantidad = $pv['cantidad'];
            }



            $tableHTML .= "<tr>";
            $tableHTML .= "<td style='text-align: center;'>" . $pv['nombre'] . "</td>";
            $tableHTML .= "<td style='text-align: center;'>" . $cantidad . "</td>";
            $tableHTML .= "<td style='text-align: center;'>" . $precio . "</td>";
            $tableHTML .= "<td style='text-align: center;'>" . $pv['total'] . "</td>";
            $tableHTML .= "</tr>";
        }
        $tableHTML .= "</tbody>";
        $tableHTML .=  "</table>";
        $tableHTML .= "</td>";
        $tableHTML .= "</tr>";
    }

    $tableHTML .= "<tr>";
    $tableHTML .= "<td style='text-align: center;'><b>TOTALES<b></td>";
    $tableHTML .= "<td style='text-align: center;'><b></b></td>";
    $tableHTML .= "<td style='text-align: center;'><b></b></td>";
    $tableHTML .= "<td style='text-align: center;'><b><b></b></td>";
    $tableHTML .= "<td style='text-align: center;'><b><b></b></td>";
    $tableHTML .= "<td style='text-align: center;'><b><b></b></td>";
    $tableHTML .= "<td style='text-align: center;'><b><b></b></td>";
    $tableHTML .= "<td style='text-align: center;'><b><b></b></td>";
    $tableHTML .= "<td style='text-align: center;'><b><b></b></td>";
    $tableHTML .= "<td style='text-align: center;'><b>" . number_format($total_desc, 2, '.', ' ') . "</b></td>";
    $tableHTML .= "<td style='text-align: center;'><b>" . number_format(floatval($subtotal['subtotal'] - $restaSubTotalCred), 2, '.', ' ') . "</b></td>";
    $tableHTML .= "<td style='text-align: center;'><b>" . number_format(floatval($totalimpuestos['impuestos'] - $restaTotalImpCred), 2, '.', ' ') . "</b></td>";
    $tableHTML .= "<td style='text-align: center;'><b>" . number_format(floatval($totalvendido['total'] - $total_desc - $restaTotalCred), 2, '.', ' ') . "</b></td>";

    $tableHTML .= "</tr>";
}
$tableHTML .= "</tbody>";
$tableHTML .= "</table>";


$mpdf = new \Mpdf\Mpdf(['orientation' => 'L']);
$stylesheet = file_get_contents('cotizacion.css');

$mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($tableHTML, \Mpdf\HTMLParserMode::HTML_BODY);
//$mpdf->Output();
$mpdf->Output("VentasDetalle_{$fechaInicio}_{$fechaFin}.pdf", 'D');
