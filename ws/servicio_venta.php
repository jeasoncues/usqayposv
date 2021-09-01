<?php

require_once('../nucleo/servicio_venta.php');
$objservicio_venta = new servicio_venta();

require_once('../nucleo/venta.php');
$objventa = new venta();

require_once('../nucleo/turno.php');
$objturno = new turno();

require_once('../nucleo/servicio.php');
$objservicio = new servicio();

require_once('../nucleo/servicio_producto.php');
$objservicio_producto = new servicio_producto();

require_once('../nucleo/movimiento_producto.php');
$objmovimiento_producto = new movimiento_producto();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objservicio_venta = new servicio_venta();
            $objservicio_venta->setVar('id_venta', $_POST['id_venta']);
            $objservicio_venta->setVar('id_servicio', $_POST['id_servicio']);
            $objservicio_venta->setVar('precio', $_POST['precio']);
            $objservicio_venta->setVar('cantidad', $_POST['cantidad']);
            $objservicio_venta->setVar('total', $_POST['total']);
            $objservicio_venta->setVar('estado', "1");
            $objservicio_venta->setVar('estado_fila',"1");
            $idop = $objservicio_venta->insertDB();
            echo json_encode($idop);
            break;
            
        case 'addventa':

            $objservicio_venta = new servicio_venta();

            if (isset($_POST['id_actualizar'])) {
                $existente = $objservicio_venta->consulta_arreglo("SELECT * FROM servicio_venta WHERE id = {$_POST['id_actualizar']}");
            } else {
                $existente = $objservicio_venta->consulta_arreglo("SELECT * FROM servicio_venta WHERE id_venta = {$_POST['id_venta']} AND id_servicio = {$_POST['id_servicio']}");
            }

            $idop = 0;
            $cantidad = 0;

            if (is_array($existente)) {
                $id = $existente['id'];
                $precio = $existente['precio'];
                $cantidad = $existente['cantidad'] + $_POST['cantidad'];
                $total = $precio * $cantidad;

                $idop = $objservicio_venta->consulta_simple("UPDATE servicio_venta SET precio = {$precio}, cantidad = {$cantidad}, total = {$total} WHERE id = {$id}");

                $cantidad *= -1;

                if ($cantidad == 0){
                    echo json_encode(0);
                    die();
                }

                $objservicio_venta->consulta_simple("UPDATE movimiento_producto SET cantidad = {$cantidad} WHERE tipo_movimiento = {$id}");
            } else {

                $total = floatval($_POST['precio']) * floatval($_POST['cantidad']);
                $total = round($total, 2);

                $cantidad = $_POST['cantidad'];
                $objservicio_venta->setVar('id_venta', $_POST['id_venta']);
                $objservicio_venta->setVar('id_servicio', $_POST['id_servicio']);
                $objservicio_venta->setVar('precio', $_POST['precio']);
                $objservicio_venta->setVar('cantidad', $cantidad);
//            $objservicio_venta->setVar('total', $_POST['total']);
                $objservicio_venta->setVar('total', $total);
                $objservicio_venta->setVar('estado', "1");
                $objservicio_venta->setVar('estado_fila', "1");
                $idop = $objservicio_venta->insertDB();

                //Obtenemos fecha cierre y turno
                $objconn = new turno();
                $turno_act = $objconn->consulta_arreglo("Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'");
                $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");

                //Obtenemos productos por servicio
                $objservicio_producto = new servicio_producto();
                $res = $objservicio_producto->consulta_matriz("Select * from servicio_producto where id_servicio = '".$_POST['id_servicio']."'");
                if (is_array($res)) {
                    foreach ($res as $act) {
                        //Ahora hacemos el movimiento
                        $objmovimiento_producto = new movimiento_producto();
                        $objmovimiento_producto->setVar('id_producto', $act['id_producto']);
                        $objmovimiento_producto->setVar('id_almacen',$act["id_almacen"]);
                        $objmovimiento_producto->setVar('cantidad', "-".($act['cantidad']*$cantidad));
//                        $objmovimiento_producto->setVar('tipo_movimiento','VENTA');
                        $objmovimiento_producto->setVar('tipo_movimiento',$idop);
                        $objmovimiento_producto->setVar('id_usuario', $_POST['id_usuario']);
                        $objmovimiento_producto->setVar('fecha',date("Y-m-d H:i:s"));
                        $objmovimiento_producto->setVar('id_turno', $turno_act["id"]);
                        $objmovimiento_producto->setVar('fecha_cierre', $cierre_act["fecha_cierre"]);
                        $objmovimiento_producto->setVar('estado_fila',"1");
                        $id_movimiento = $objmovimiento_producto->insertDB();
                    }
                }
            }
            

            echo json_encode($idop);
            break;
            
        case 'delventa':
            //Obtenemos fecha cierre y turno
            $objconn = new turno();
            $turno_act = $objconn->consulta_arreglo("Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'");
            $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");

            $res = $objservicio_venta->consulta_arreglo("Select * from servicio_venta where id='".$_POST['id']."'");
            if (is_array($res)){

                $objconn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
                $objconn->consulta_simple("Delete from movimiento_producto where tipo_movimiento = '".$_POST['id']."'");
                $objconn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");


                //Eliminamos
                $objsv = new servicio_venta();
                $objsv->setVar('id', $_POST['id']);
                $objsv->deleteDB();

                echo json_encode(1);
            } else {
                echo json_encode(0);
            }

            /*
            //Obtenemos los datos del servicio a eliminar
            $objservicio_venta = new servicio_venta();
            $res = $objservicio_venta->consulta_arreglo("Select * from servicio_venta where id='".$_POST['id']."'");
            if (is_array($res)) {
                //Obtenemos productos por servicio
                $objservicio_producto = new servicio_producto();
                $res1 = $objservicio_producto->consulta_matriz("Select * from servicio_producto where id_servicio = '".$res['id_servicio']."'");
                if (is_array($res1)) {
                    foreach ($res1 as &$act) {
                        //Ahora hacemos el movimiento
                        $objmovimiento_producto = new movimiento_producto();
                        $objmovimiento_producto->setVar('id_producto', $act['id_producto']);
                        $objmovimiento_producto->setVar('id_almacen',$act["id_almacen"]);
                        $objmovimiento_producto->setVar('cantidad', $act['cantidad'] * $res['cantidad']);
                        $objmovimiento_producto->setVar('tipo_movimiento','VENTA');
                        $objmovimiento_producto->setVar('id_usuario', $_POST['id_usuario']);
                        $objmovimiento_producto->setVar('fecha',date("Y-m-d H:i:s"));
                        $objmovimiento_producto->setVar('id_turno', $turno_act["id"]);
                        $objmovimiento_producto->setVar('fecha_cierre', $cierre_act["fecha_cierre"]);
                        $objmovimiento_producto->setVar('estado_fila',"1");
                        $id_movimiento = $objmovimiento_producto->insertDB();
                    }
                }
                //Eliminamos
                $objsv = new servicio_venta();
                $objsv->setVar('id', $_POST['id']);
                $objsv->deleteDB();
                
                echo json_encode(1);
            } else {
                echo json_encode(0);
            }*/
            break;

        case 'mod':
            $objservicio_venta->setVar('id', $_POST['id']);
            $objservicio_venta->setVar('id_venta', $_POST['id_venta']);
            $objservicio_venta->setVar('id_servicio', $_POST['id_servicio']);
            $objservicio_venta->setVar('precio', $_POST['precio']);
            $objservicio_venta->setVar('cantidad', $_POST['cantidad']);
            $objservicio_venta->setVar('total', $_POST['total']);
            $objservicio_venta->setVar('estado', $_POST['estado']);
            $objservicio_venta->setVar('tiempo_iniciado', $_POST['tiempo_iniciado']);
            $objservicio_venta->setVar('tiempo_cerrado', $_POST['tiempo_cerrado']);
            $objservicio_venta->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objservicio_venta->updateDB());
            break;

        case 'del':
            $objservicio_venta->setVar('id', $_POST['id']);
            echo json_encode($objservicio_venta->deleteDB());
            break;

        case 'get':
            $res = $objservicio_venta->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_venta'] = $objventa->searchDB($res[0]['id_venta'], 'id', 1);
                $res[0]['id_venta'] = $res[0]['id_venta'][0];
                $res[0]['id_servicio'] = $objservicio->searchDB($res[0]['id_servicio'], 'id', 1);
                $res[0]['id_servicio'] = $res[0]['id_servicio'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objservicio_venta->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_venta'] = $objventa->searchDB($act['id_venta'], 'id', 1);
                    $act['id_venta'] = $act['id_venta'][0];
                    $act['id_servicio'] = $objservicio->searchDB($act['id_servicio'], 'id', 1);
                    $act['id_servicio'] = $act['id_servicio'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'listbyventa':
            $res = $objservicio_venta->consulta_matriz("Select * from servicio_venta where id_venta = '".$_POST["id"]."' ORDER BY id DESC");
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_servicio'] = $objservicio->searchDB($act['id_servicio'], 'id', 1);
                    $act['id_servicio'] = $act['id_servicio'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objservicio_venta->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_venta'] = $objventa->searchDB($act['id_venta'], 'id', 1);
                    $act['id_venta'] = $act['id_venta'][0];
                    $act['id_servicio'] = $objservicio->searchDB($act['id_servicio'], 'id', 1);
                    $act['id_servicio'] = $act['id_servicio'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>