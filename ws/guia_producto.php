<?php

require_once('../nucleo/guia_producto.php');
$objguia_producto = new guia_producto();

require_once('../nucleo/usuario.php');
$objusuario = new usuario();

require_once('../nucleo/proveedor.php');
$objproveedor = new proveedor();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objguia_producto->setVar('id_usuario', $_POST['id_usuario']);
            $objguia_producto->setVar('fecha_registro',date("Y-m-d H:i:s"));
            $objguia_producto->setVar('fecha_realizada', $_POST['fecha_realizada']);
            $objguia_producto->setVar('tipo', $_POST['tipo']);
            $objguia_producto->setVar('id_proveedor', $_POST['id_proveedor']);
            $objguia_producto->setVar('numero_guia', $_POST['numero_guia']);
            $objguia_producto->setVar('estado_fila',"1");

            echo json_encode($objguia_producto->insertDB());
            break;

        case 'mod':
            $objguia_producto->setVar('id', $_POST['id']);
            $objguia_producto->setVar('id_usuario', $_POST['id_usuario']);
            $objguia_producto->setVar('fecha_realizada', $_POST['fecha_realizada']);
            $objguia_producto->setVar('tipo', $_POST['tipo']);
            $objguia_producto->setVar('id_proveedor', $_POST['id_proveedor']);
            $objguia_producto->setVar('numero_guia', $_POST['numero_guia']);
            $objguia_producto->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objguia_producto->updateDB());
            break;

        case 'del':
            $objguia_producto->setVar('id', $_POST['id']);

            $ga = $objguia_producto->consulta_matriz("SELECT * FROM guia_movimiento WHERE id_guia_producto = {$_POST['id']}");

            if (is_array($ga)){
                foreach ($ga as $g){
                    $objguia_producto->consulta_simple("DELETE FROM movimiento_producto WHERE id = {$g['id_movimiento_producto']}");
                }
            }

            $objguia_producto->consulta_simple("DELETE FROM guia_movimiento WHERE id_guia_producto = {$_POST['id']}");

            echo json_encode($objguia_producto->deleteDB());
            break;

        case 'get':
            $res = $objguia_producto->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_usuario'] = $objusuario->searchDB($res[0]['id_usuario'], 'id', 1);
                $res[0]['id_usuario'] = $res[0]['id_usuario'][0];
                if($res[0]['id_proveedor']>0){                    
                    $res[0]['id_proveedor'] = $objproveedor->searchDB($res[0]['id_proveedor'], 'id', 1);
                    $res[0]['id_proveedor'] = $res[0]['id_proveedor'][0];
                }
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objguia_producto->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_usuario'] = $objusuario->searchDB($act['id_usuario'], 'id', 1);
                    $act['id_usuario'] = $act['id_usuario'][0];
                    $act['id_proveedor'] = $objproveedor->searchDB($act['id_proveedor'], 'id', 1);
                    $act['id_proveedor'] = $act['id_proveedor'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objguia_producto->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_usuario'] = $objusuario->searchDB($act['id_usuario'], 'id', 1);
                    $act['id_usuario'] = $act['id_usuario'][0];
                    $act['id_proveedor'] = $objproveedor->searchDB($act['id_proveedor'], 'id', 1);
                    $act['id_proveedor'] = $act['id_proveedor'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'addmov':
            //Salida
            $salida = new guia_producto();
            $salida->setVar('id_usuario', $_POST['id_usuario']);
            $salida->setVar('fecha_registro',date("Y-m-d H:i:s"));
            $salida->setVar('fecha_realizada', $_POST['fecha_realizada']);
            // $salida->setVar('tipo', '0');
            $salida->setVar('tipo', '2');
            $salida->setVar('numero_guia', $_POST['numero_guia_salida']);
            $salida->setVar('estado_fila',"1");
            $id_salida = $salida->insertDB();

            //Entrada
            $entrada = new guia_producto();
            $entrada->setVar('id_usuario', $_POST['id_usuario']);
            $entrada->setVar('fecha_registro',date("Y-m-d H:i:s"));
            $entrada->setVar('fecha_realizada', $_POST['fecha_realizada']);
            // $entrada->setVar('tipo', '1');
            $entrada->setVar('tipo', '3');
            $entrada->setVar('numero_guia', $_POST['numero_guia_entrada']);
            $entrada->setVar('estado_fila',"1");
            $id_entrada = $entrada->insertDB();
            
            $con = new guia_producto();
            $re = $con->consulta_id("Insert into movimiento_almacenes values(NULL,'".$id_entrada."','".$id_salida."',1)");            

            echo json_encode($re);
            break; 
        
        case 'modmov':
            $con = new guia_producto();
            $dt = $con->consulta_arreglo("Select * from movimiento_almacenes where id = '".$_POST["id"]."'");
            
            $salida = new guia_producto();
            $salida->setId($dt["salida"]);
            $salida->setVar('id_usuario', $_POST['id_usuario']);
            $salida->setVar('fecha_realizada', $_POST['fecha_realizada']);
            $salida->setVar('tipo', '0');
            $salida->setVar('numero_guia', $_POST['numero_guia_salida']);
            $salida->setVar('estado_fila',"1");
            $salida->updateDB();
            
            $entrada = new guia_producto();
            $entrada->setVar('id_usuario', $_POST['id_usuario']);
            $entrada->setId($dt["entrada"]);
            $entrada->setVar('fecha_realizada', $_POST['fecha_realizada']);
            $entrada->setVar('tipo', '0');
            $entrada->setVar('numero_guia', $_POST['numero_guia_entrada']);
            $entrada->setVar('estado_fila',"1");
            $entrada->updateDB();
            
            echo json_encode(1);
            break;
    
        case "getmov":
            $con = new guia_producto();
            $dt = $con->consulta_arreglo("Select * from movimiento_almacenes where id = '".$_POST["id"]."'");
            if(is_array($dt)){
                $dt["entrada"] = $con->consulta_arreglo("Select * from guia_producto where id = '".$dt["entrada"]."'");
                $dt["salida"] = $con->consulta_arreglo("Select * from guia_producto where id = '".$dt["salida"]."'");
            }
            echo json_encode($dt);
            break;
            
        case "delmov":
            $con = new guia_producto();
            $movs0 = $con->consulta_matriz("Select * from guia_movimiento_a where id_movimiento_almacenes = '".$_POST["id"]."'");

            $movsgp = $con->consulta_matriz("Select * from movimiento_almacenes where id = '".$_POST["id"]."'");
            
            if(is_array($movs0)){
                foreach($movs0 as $mv){
                    $con->consulta_simple("Delete from movimiento_producto where id = '".$mv["salida"]."'");
                    $con->consulta_simple("Delete from movimiento_producto where id = '".$mv["entrada"]."'");
                    $con->consulta_simple("Delete from guia_movimiento_a where id = '".$mv["id"]."'");

                    $con->consulta_simple("Delete from guia_movimiento where id_movimiento_producto = '".$mv["salida"]."'");
                    $con->consulta_simple("Delete from guia_movimiento where id_movimiento_producto = '".$mv["entrada"]."'");
                }
            }

            if(is_array($movsgp)){
                foreach($movsgp as $mgp){
                    $con->consulta_simple("Delete from guia_producto where id = '".$mgp["salida"]."'");
                    $con->consulta_simple("Delete from guia_producto where id = '".$mgp["entrada"]."'");
                }
            }
            $con->consulta_simple("Delete from movimiento_almacenes where id = '".$_POST["id"]."'");            

            echo json_encode(1);
            break;
    
    }
}?>