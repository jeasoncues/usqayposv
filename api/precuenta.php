<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json');

require_once '../nucleo/include/MasterConexion.php';
require_once('classes/PosPrinter.php');

if (isset($_POST['op'])) {

    switch ($_POST['op']) {
        case 'precuenta':

            $objcon = new MasterConexion();

            $id_venta = $_POST["id"];

            $config = $objcon->consulta_arreglo("SELECT * from configuracion");

            $venta = $objcon->consulta_arreglo("SELECT * FROM venta where id = {$id_venta}");

            $fecha = $venta["fecha_hora"];
            $total_pedido = 0;

            $usuario = $objcon->consulta_arreglo("SELECT * FROM usuario where id = {$venta['id_usuario']}");
            $cliente = $objcon->consulta_arreglo("SELECT * FROM cliente where id = {$venta['id_cliente']}");
            // echo json_encode($cliente);
            $boleta = $objcon->consulta_arreglo("SELECT id, serie FROM boleta WHERE id_venta = {$id_venta}");

            $productos = $objcon->consulta_matriz("SELECT p.nombre, pv.* from producto_venta pv left join producto p on pv.id_producto = p.id WHERE pv.id_venta = $id_venta");

            $servicios = $objcon->consulta_matriz("SELECT s.nombre, sv.* from servicio_venta sv left join servicio s on sv.id_servicio = s.id WHERE sv.id_venta = $id_venta");

            $ventas = $objcon->consulta_arreglo("SELECT nombre FROM venta v, caja c WHERE v.id = $id_venta AND v.id_caja = c.id");
            $medio_pago = $objcon->consulta_arreglo("SELECT * FROM venta_medio_pago WHERE id_venta = $id_venta");
            $Descuento = $objcon->consulta_arreglo("SELECT * FROM venta_medio_pago WHERE id_venta = $id_venta AND medio = 'DESCUENTO'");

            $hash = $objcon->consulta_arreglo("SELECT * FROM comprobante_hash WHERE pkComprobante = $id_venta");

            $items = $objcon->consulta_arreglo("SELECT pv.id_producto
                FROM producto_venta pv
                INNER JOIN producto p ON pv.id_producto = p.id
                WHERE pv.estado_fila=1 AND pv.id_venta = $id_venta
                UNION
                SELECT sv.id_servicio
                FROM servicio_venta sv
                INNER JOIN servicio s ON sv.id_servicio = s.id
                WHERE sv.id_venta = $id_venta
                ");

            $incluye = $objcon->consulta_arreglo("SELECT incluye_impuesto
                FROM producto p 
                WHERE p.id = " . $items[0] . "
                UNION
                SELECT incluye_impuesto
                FROM servicio s 
                WHERE s.id = " . $items[0] . "");

            if (
                $incluye[0] == 0
            ) {
                $GEIG = "INAFECTA";
            } else if ($incluye[0] == 1) {
                $GEIG = "GRAVADA";
            } else if ($incluye[0] == 2) {
                $GEIG = "EXONERADA";
            } else {
                $GEIG = "GRATUITA";
            }

            $Pro_total = 0;

            $verificaImpresion = $objcon->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='" . $_COOKIE['id_caja'] . "' AND opcion='NOT' ");


            if (isset($verificaImpresion['id'])) {

                $printer = $objcon->consulta_arreglo("SELECT * FROM impresoras WHERE nombre='" . $verificaImpresion['impresora'] . "'");

                $printerName = $verificaImpresion['impresora'];
                $receipt = "PRE CUENTA - " . str_pad($id_venta, 8, "0", STR_PAD_LEFT) . "\n\n";
                $pos_printer = new PosPrinter($id_venta, $printerName, $receipt);
                $success = true;
                $texto = "Representacion impresa de la \n Pre Cuenta";
                $msg = "VENTA EXITOSA";

                try {

                    if ($printer['red'] == 1) {
                        $pos_printer->connectTypeNetwork($printerName);
                    } else {
                        $pos_printer->connectTypeWindows($printerName);
                    }

                    $pos_printer
                        ->setShopName('ticket')
                        ->setTitleReceipt()
                        ->setItems()
                        ->setMontos()
                        ->setFooter(false, $texto)
                        ->cut()
                        ->pulse();
                } catch (Exception $e) {
                    $success = false;
                    $msg = $e->getMessage();
                } finally {
                    $pos_printer->close();
                }

                echo json_encode($success);
            }

            break;

        default:
            break;
    }
}
