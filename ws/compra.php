<?php

require_once('../nucleo/compra.php');
$objcompra = new compra();

require_once('../nucleo/usuario.php');
$objusuario = new usuario();

require_once('../nucleo/proveedor.php');
$objproveedor = new proveedor();

require_once('../nucleo/caja.php');
$objcaja = new caja();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objcompra->setVar('id', $_POST['id']);
            $objcompra->setVar('id_usuario', $_POST['id_usuario']);
            $objcompra->setVar('id_proveedor', $_POST['id_proveedor']);
            $objcompra->setVar('categoria', $_POST['categoria']);
            $objcompra->setVar('numero_documento', $_POST['numero_documento']);
            $objcompra->setVar('monto_total', $_POST['monto_total']);
            $objcompra->setVar('fecha', $_POST['fecha']);
            $objcompra->setVar('monto_pendiente', $_POST['monto_pendiente']);
            $objcompra->setVar('id_caja', $_POST['id_caja']);
            $objcompra->setVar('proximo_pago', $_POST['proximo_pago']);
            $objcompra->setVar('estado_fila', $_POST['estado_fila']);
            $id_compra = $objcompra->insertDB();

//            $monto_pendiente = floatval($_POST["monto_total"])-floatval($_POST["monto_pendiente"]);
            if(intval($id_compra)!=0){
                $monto_pagado = floatval($_POST["monto_total"] - $_POST['monto_pendiente']);
                $objconn = new compra();
                $turno_act = $objconn->consulta_arreglo("Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'");
                $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");

            
                if ($monto_pagado > 0 && intval($_POST["id_caja"]) > 0) {
                    //Obtenemos fecha cierre y turno
                    
                    $objconn->consulta_simple("Insert into movimiento_caja values(NULL,'".$_POST['id_caja']."','-".$monto_pagado."','BUY|PEN|EFECTIVO|".$id_compra."','".date("Y-m-d H:i:s")."','".$cierre_act["fecha_cierre"]."','".$turno_act["id"]."','".$_POST['id_usuario']."','1')");
                }else{
                    $objconn->consulta_simple("Insert into movimiento_caja values(NULL,'0','-".$monto_pagado."','BUY|EXT|PEN|EFECTIVO_COBRO|".$id_compra."','".date("Y-m-d H:i:s")."','".$cierre_act["fecha_cierre"]."','".$turno_act["id"]."','".$_POST['id_usuario']."','1')");
                }
            }

            echo json_encode($id_compra);

            break;

        case 'edit':
            $objcompra->setVar('id', $_POST['id']);
            $objcompra->setVar('id_usuario', $_POST['id_usuario']);
            $objcompra->setVar('id_proveedor', $_POST['id_proveedor']);
            $objcompra->setVar('categoria', $_POST['categoria']);
            $objcompra->setVar('numero_documento', $_POST['numero_documento']);
            $objcompra->setVar('monto_total', $_POST['monto_total']);
            $objcompra->setVar('fecha', $_POST['fecha']);
            $objcompra->setVar('monto_pendiente', $_POST['monto_pendiente']);
            $objcompra->setVar('id_caja', $_POST['id_caja']);
            $objcompra->setVar('proximo_pago', $_POST['proximo_pago']);
            $objcompra->setVar('estado_fila', $_POST['estado_fila']);
            $id_compra = $_POST['id'];
            $dif = -1 * $_POST['dif'];
            echo json_encode($objcompra->updateDB());

            $monto_pagado = floatval($_POST["monto_total"]);

            if ($monto_pagado > 0 && intval($_POST["id_caja"]) > 0) {
                $objconn = new compra();
                //Obtenemos fecha cierre y turno
                $turno_act = $objconn->consulta_arreglo("Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'");
                $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");

                $objconn->consulta_simple("Insert into movimiento_caja values(NULL,'".$_POST['id_caja']."','".$dif."','BUY|PEN|EFECTIVO|".$id_compra."','".date("Y-m-d H:i:s")."','".$cierre_act["fecha_cierre"]."','".$turno_act["id"]."','".$_POST['id_usuario']."','1')");
            }
            break;

        case 'pay':
            $objdatos = new compra();
            $objdatos->setId($_POST["id"]);
            $objdatos->getDB();

            $pendiente = floatval($objdatos->getMontoPendiente()) - floatval($_POST["monto"]);

            $monto_total = $objdatos->getMontoTotal();/*-floatval($_POST["monto"]);*/

            $objcompra = new compra();
            $objcompra->setVar('id', $_POST['id']);
            $objcompra->setVar('monto_pendiente', (string)$pendiente);
            $objcompra->setVar('monto_total', $monto_total);
            $objcompra->setVar('id_caja', $_POST['id_caja']);

            if ($pendiente > 0) {
                $objcompra->setVar('proximo_pago', $_POST['proximo']);
            } else {
                if ($pendiente == 0) {
                    $objcompra->setVar('proximo_pago', '');
                } else {
                    $objcompra->setVar('proximo_pago', date('Y-m-d'));
                }
            }


            $res = $objcompra->updateDB();

            $objconn = new compra();
                //Obtenemos fecha cierre y turno
            $turno_act = $objconn->consulta_arreglo("Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'");
            $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");

            if (intval($_POST["id_caja"]) > 0) {
                $objconn->consulta_simple("Insert into movimiento_caja values(NULL,'".$_POST['id_caja']."','-".$_POST["monto"]."','BUY|PEN|EFECTIVO|".$_POST['id']."','".date("Y-m-d H:i:s")."','".$cierre_act["fecha_cierre"]."','".$turno_act["id"]."','".$_POST['id_usuario']."','1')");
            }
            if (intval($_POST["id_caja"]) == 0) {
                $objconn->consulta_simple("Insert into movimiento_caja values(NULL,'0','-".$_POST["monto"]."','BUY|EXT|PEN|EFECTIVO_COBRO|".$_POST['id']."','".date("Y-m-d H:i:s")."','".$cierre_act["fecha_cierre"]."','".$turno_act["id"]."','".$_POST['id_usuario']."','1')");
            }

            echo json_encode($res);
            break;

        case 'del':
            $objdatos = new compra();
            $objdatos->setId($_POST["id"]);
            $objdatos->getDB();

//            $gastado = floatval($objdatos->getMontoTotal()) - floatval($objdatos->getMontoPendiente());
            $gastado = floatval($objdatos->getMontoTotal());
            if($gastado>0 && intval($objdatos->getIdCaja())>0){
                $objconn = new compra();
                //Obtenemos fecha cierre y turno
                $turno_act = $objconn->consulta_arreglo("Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'");
                $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");

                $objconn->consulta_simple("Insert into movimiento_caja values(NULL,'".$objdatos->getIdCaja()."','".$gastado."','BUY|PEN|EFECTIVO|".$_POST['id']."','".date("Y-m-d H:i:s")."','".$cierre_act["fecha_cierre"]."','".$turno_act["id"]."','".$objdatos->getIdUsuario()."','1')");
            }

            $objcompra = new compra();
            $objcompra->setVar('id', $_POST['id']);
            echo json_encode($objcompra->deleteDB());
            break;

        case 'traer':
            $objconn = new compra();
            $data = $objconn->consulta_matriz("SELECT c.id, id_usuario, id_proveedor, razon_social, categoria, numero_documento, monto_total, fecha, monto_pendiente, id_caja, proximo_pago FROM compra c, proveedor p WHERE c.id= ".$_POST['id']." AND c.id_proveedor = p.id");

            if (is_array($data)) {
                echo json_encode($data);
            } else {
                echo json_encode(0);
            }
            break;

            // $gastado = floatval($objdatos->getMontoTotal());
            // if($gastado>0 && intval($objdatos->getIdCaja())>0){
            //     $objconn = new compra();
            //     //Obtenemos fecha cierre y turno
            //     $turno_act = $objconn->consulta_arreglo("Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'");
            //     $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");

            //     $objconn->consulta_simple("Insert into movimiento_caja values(NULL,'".$objdatos->getIdCaja()."','".$gastado."','BUY|PEN|EFECTIVO|".$_POST['id']."','".date("Y-m-d H:i:s")."','".$cierre_act["fecha_cierre"]."','".$turno_act["id"]."','".$objdatos->getIdUsuario()."','1')");
            // }

            // $objcompra = new compra();
            // $objcompra->setVar('id', $_POST['id']);
            // echo json_encode($objcompra->deleteDB());
            break;

        case 'get':
            $res = $objcompra->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_usuario'] = $objusuario->searchDB($res[0]['id_usuario'], 'id', 1);
                $res[0]['id_usuario'] = $res[0]['id_usuario'][0];
                $res[0]['id_proveedor'] = $objproveedor->searchDB($res[0]['id_proveedor'], 'id', 1);
                $res[0]['id_proveedor'] = $res[0]['id_proveedor'][0];
                if($res[0]['id_caja']==0){                      
                    $res[0]['id_caja'] = [
                        'id'=>0,
                        'nombre'=>'Fondos Externos'
                    ];
                }else{
                    $res[0]['id_caja'] = $objcaja->searchDB($res[0]['id_caja'], 'id', 1);
                    $res[0]['id_caja'] = $res[0]['id_caja'][0]; // dañado
                }                
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objcompra->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_usuario'] = $objusuario->searchDB($act['id_usuario'], 'id', 1);
                    $act['id_usuario'] = $act['id_usuario'][0];
                    $act['id_proveedor'] = $objproveedor->searchDB($act['id_proveedor'], 'id', 1);
                    $act['id_proveedor'] = $act['id_proveedor'][0];
                    $act['id_caja'] = $objcaja->searchDB($act['id_caja'], 'id', 1);
                    $act['id_caja'] = $act['id_caja'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objcompra->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_usuario'] = $objusuario->searchDB($act['id_usuario'], 'id', 1);
                    $act['id_usuario'] = $act['id_usuario'][0];
                    $act['id_proveedor'] = $objproveedor->searchDB($act['id_proveedor'], 'id', 1);
                    $act['id_proveedor'] = $act['id_proveedor'][0];
                    $act['id_caja'] = $objcaja->searchDB($act['id_caja'], 'id', 1);
                    $act['id_caja'] = $act['id_caja'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
        case 'show_pagos':
            
        break;
    }
}?>