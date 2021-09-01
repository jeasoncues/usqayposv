<?php

require_once('../nucleo/producto_venta.php');
$objproducto_venta = new producto_venta();

require_once('../nucleo/detalles_cotizacion.php');
$objdetalles_cotizacion = new detalles_cotizacion();

require_once('../nucleo/venta.php');
$objventa = new venta();

require_once('../nucleo/cotizacion.php');
$objcotizacion = new cotizacion();

require_once('../nucleo/producto.php');
$objproducto = new producto();

require_once('../nucleo/turno.php');
$objturno = new turno();

require_once('../nucleo/movimiento_producto.php');
$objmovimiento_producto = new movimiento_producto();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objproducto_venta->setVar('id', $_POST['id']);
            $objproducto_venta->setVar('id_venta', $_POST['id_venta']);
            $objproducto_venta->setVar('id_producto', $_POST['id_producto']);
            $objproducto_venta->setVar('precio', $_POST['precio']);
            $objproducto_venta->setVar('cantidad', $_POST['cantidad']);
            $objproducto_venta->setVar('total', $_POST['total']);
            $objproducto_venta->setVar('estado_fila', 1);
            echo json_encode($objproducto_venta->insertDB());
            break;

        case 'addventa':

            $objproducto_venta = new producto_venta();
            if (isset($_POST['id_actualizar'])) {
                $existente = $objproducto_venta->consulta_arreglo("SELECT * FROM producto_venta WHERE estado_fila= 1 AND id = {$_POST['id_actualizar']}");
            } else {
                $existente = $objproducto_venta->consulta_arreglo("SELECT * FROM producto_venta WHERE estado_fila= 1 AND id_venta = {$_POST['id_venta']} AND id_producto = {$_POST['id_producto']} AND precio = {$_POST['precio']}");
            }

            $idg = 0;

            if (is_array($existente)) {

                $id = $existente['id'];
                $precio = $existente['precio'];
                $cantidad = $existente['cantidad'] + $_POST['cantidad'];
                $total = $precio * $cantidad;

                $oferta = $objproducto_venta->consulta_arreglo(
                    "SELECT o.*
                            FROM oferta_producto op
                            INNER JOIN oferta o ON op.id_oferta = o.id
                            WHERE op.id_producto = {$existente['id_producto']} AND o.fecha_inicio <= DATE(NOW()) AND o.fecha_fin >= DATE(NOW())
                            LIMIT 1");
                                                      
                if(!empty($oferta['id'])){
                    $of_ex = $objproducto_venta->consulta_arreglo("SELECT * FROM producto_venta_oferta WHERE id_producto_venta = {$existente['id']} AND id_oferta = {$oferta['id']} LIMIT 1");
                $of_ex2 = $objproducto_venta->consulta_arreglo("SELECT * FROM venta_oferta WHERE id_venta = {$existente['id_venta']} AND id_oferta = {$oferta['id']} LIMIT 1");
                }

                if (is_array($oferta)) {
                    $isOferta = false;
                    if ($oferta['id_tipo'] == 1) {
                        $paga = $oferta['paga'];
                        $compra = $oferta['compra'];
                        $monto = 0;
                        if ($cantidad+1 > $compra) {
                            $residuo = $cantidad % $compra;
                            $cociente = intval($cantidad / $compra);
                            if ($residuo == 0) {
                                $monto = $paga * $precio * $cociente;
                            } else {
                                $monto = $cociente * $paga * $precio + $residuo * $precio;
                            }
                            $isOferta = true;
                        } else {
                            $monto = $precio * $cantidad;
                        }
                        $total = $monto;

                        if (!$of_ex && $isOferta && $cantidad>=$compra) {
                            $objproducto_venta->consulta_simple("INSERT INTO producto_venta_oferta(id_producto_venta, id_oferta)VALUES({$existente['id']}, {$oferta['id']})");
                        }

                        if ($cantidad<$compra){
                            eliminaVentaOferta($existente['id'], $oferta['id']);
                        }
                    }

                    if ($oferta['id_tipo'] == 2 && !$of_ex2) {
                        $objproducto_venta->consulta_simple("INSERT INTO venta_oferta(id_venta, id_oferta)VALUES({$existente['id_venta']}, {$oferta['id']})");
                    }

                }

                $idg = $objproducto_venta->consulta_simple("UPDATE producto_venta SET precio = {$precio}, cantidad = {$cantidad}, total = {$total} WHERE id = {$id}");

                $cantidad *= -1;

                if ($cantidad == 0){
                    eliminaVentaOferta($existente['id'], $oferta['id']);
                    echo json_encode(0);
                    die();
                }

                $objproducto_venta->consulta_simple("UPDATE movimiento_producto SET cantidad = {$cantidad} WHERE tipo_movimiento = {$id}");
            } else {
                $total = floatval($_POST['precio']) * floatval($_POST['cantidad']);
                $total = round($total, 2);
                $oferta = $objproducto_venta->consulta_arreglo(
                    "SELECT o.*
                            FROM oferta_producto op
                            INNER JOIN oferta o ON op.id_oferta = o.id
                            WHERE op.id_producto = {$_POST['id_producto']} AND o.fecha_inicio <= DATE(NOW()) AND o.fecha_fin >= DATE(NOW())
                            LIMIT 1");

                $idg = addProducto($_POST['precio'],$_POST['cantidad'], $_POST['id_producto'], $_POST['idNew']);

                if (is_array($oferta)) {
                    $isOferta = false;
                    $cantidad = $_POST['cantidad'];
                    $precio = $_POST['precio'];

                    if ($oferta['id_tipo'] == 1) {
                        $of_ex = $objproducto_venta->consulta_arreglo("SELECT * FROM producto_venta_oferta WHERE id_producto_venta = {$idg} AND id_oferta = {$oferta['id']} LIMIT 1");

                        $isOferta = true;
                        $paga = $oferta['paga'];
                        $compra = $oferta['compra'];
                        $monto = 0;
                        if ($cantidad + 1 > $compra) {
                            $residuo = $cantidad % $compra;
                            $cociente = intval($cantidad / $compra);
                            if ($residuo == 0) {
                                $monto = $paga * $precio * $cociente;
                            } else {
                                $monto = $cociente * $paga * $precio + $residuo * $precio;
                            }
                        } else {
                            $monto = $precio * $cantidad;
                        }
                        $total = $monto;

                        if (!$of_ex && $isOferta && $cantidad>=$compra) {
                            $objproducto_venta->consulta_simple("INSERT INTO producto_venta_oferta(id_producto_venta, id_oferta)VALUES({$idg}, {$oferta['id']})");
                        }

                        if ($cantidad<$compra){
                            eliminaVentaOferta($existente['id'], $oferta['id']);
                        }
                    }

                    if ($oferta['id_tipo'] == 2) {
                        $of_ex = $objproducto_venta->consulta_arreglo("SELECT * FROM venta_oferta WHERE id_venta = {$_POST['id_venta']} AND id_oferta = {$oferta['id']} LIMIT 1");

                        $hijos = $objproducto_venta->consulta_matriz(
                            "SELECT p.*
                                    FROM oferta_producto op
                                    INNER JOIN producto p ON op.id_producto = p.id
                                    WHERE id_oferta =
                                    (SELECT id_oferta
                                    FROM oferta_producto
                                    WHERE id_producto={$_POST['id_producto']} AND principal = TRUE)
                                    AND p.id <> {$_POST['id_producto']}");

                        if (is_array($hijos)) {
                            foreach ($hijos as $prod) {
                                addProducto($prod['precio_venta'], $_POST['cantidad'], $prod['id'], $_POST['idNew']);
                            }
                        }

                        if (!$of_ex) {
                            $objproducto_venta->consulta_simple("INSERT INTO venta_oferta(id_venta, id_oferta)VALUES({$_POST['id_venta']}, {$oferta['id']})");
                        }
                    }

                }
            }

            //            $objconn->consulta_simple("UPDATE venta_cupon SET descuento = {$nvoDesc} WHERE id");
            echo json_encode($idg);
            break;

        case 'delventa':
            $res = $objproducto_venta->consulta_arreglo("Select * from producto_venta where estado_fila= 1 AND id='".$_POST['id']."'");
            if (is_array($res)) {
                $oferta = $objproducto_venta->consulta_arreglo(
                    "SELECT vo.*
                            FROM oferta o
                            INNER JOIN oferta_producto op ON op.id_oferta = o.id
                            INNER JOIN venta_oferta vo ON vo.id_oferta = o.id
                            WHERE op.id_producto = {$res['id_producto']} AND vo.id_venta = {$res['id_venta']}
                            GROUP BY vo.id_oferta");
                if ($oferta) {
                    $objproducto_venta->consulta_simple("DELETE FROM venta_oferta WHERE id_venta = {$oferta['id_venta']} AND id_oferta = {$oferta['id_oferta']}");
                }

                $objconn = new turno();
                $objconn->consulta_simple("SET SQL_SAFE_UPDATES = 0;");
                $objconn->consulta_simple("Delete from movimiento_producto where tipo_movimiento = '".$_POST['id']."'");
                $objconn->consulta_simple("SET SQL_SAFE_UPDATES = 1;");

                //Quitamos el producto de la venta
                $objpv = new producto_venta();
                $objpv->setVar('id', $_POST['id']);
                $objpv->deleteDB();

                eliminaVentaOferta($res['id'], null);

                echo json_encode(1);
            } else {
                echo json_encode(0);
            }
            break;

        case 'mod':
            $objproducto_venta->setVar('id', $_POST['id']);
            $objproducto_venta->setVar('id_venta', $_POST['id_venta']);
            $objproducto_venta->setVar('id_producto', $_POST['id_producto']);
            $objproducto_venta->setVar('precio', $_POST['precio']);
            $objproducto_venta->setVar('cantidad', $_POST['cantidad']);
            $objproducto_venta->setVar('total', $_POST['total']);
            $objproducto_venta->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objproducto_venta->updateDB());
            break;

        case 'del':
            $objproducto_venta->setVar('id', $_POST['id']);
            echo json_encode($objproducto_venta->deleteDB());
            break;

        case 'get':
            $res = $objproducto_venta->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_venta'] = $objventa->searchDB($res[0]['id_venta'], 'id', 1);
                $res[0]['id_venta'] = $res[0]['id_venta'][0];
                $res[0]['id_producto'] = $objproducto->searchDB($res[0]['id_producto'], 'id', 1);
                $res[0]['id_producto'] = $res[0]['id_producto'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;
        case 'get_detalles_cotizacion':
            $res = $objdetalles_cotizacion->consulta_matriz("SELECT * from detalles_cotizacion WHERE id_coti=".$_POST["id"]."");
             echo json_encode($res);
            break;
        case 'mod_detalles_cotizacion':
            $res = $objdetalles_cotizacion->consulta_simple("UPDATE cotizacion SET estado_fila=2 WHERE id = '".$_POST["id"]."'");
            echo json_encode($res);
            break;
        case 'list':
            $res = $objproducto_venta->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_venta'] = $objventa->searchDB($act['id_venta'], 'id', 1);
                    $act['id_venta'] = $act['id_venta'][0];
                    $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                    $act['id_producto'] = $act['id_producto'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'listbyventa':
            $res = $objproducto_venta->consulta_matriz("Select * from producto_venta where estado_fila= 1 AND  id_venta = '".$_POST["id"]."' ORDER BY id DESC");
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $ofertas = $objproducto_venta->consulta_matriz(
                        "SELECT tof.nombre, tof.id AS id_tipo, o.*
                                FROM producto_venta_oferta pvo
                                INNER JOIN producto_venta pv ON pvo.id_producto_venta = pv.id
                                INNER JOIN oferta o ON pvo.id_oferta = o.id
                                INNER JOIN tipo_oferta tof ON o.id_tipo = tof.id
                                WHERE pv.estado_fila= 1 AND pv.id_producto = {$act['id_producto']}");


                    if (is_array($ofertas)) {
                        foreach ($ofertas as $o) {
                            if ($o['id_tipo'] == 1) {
                                $act['ofertas'][] = $o['descripcion'];
                            }
                        }
                    }

                    if($act['prod_secundario'] != 0){
                        $act['codigo_barra'] = $objproducto->consulta_arreglo("SELECT * FROM productos_precios WHERE id= ".$act['prod_secundario']."");
                    }else{
                        $act['codigo_barra'] = $objproducto->consulta_arreglo("SELECT * FROM producto_taxonomiap WHERE id_producto = '".$act['id_producto']."' AND id_taxonomiap = '4'");
                    }

                    $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                    $act['id_producto'] = $act['id_producto'][0];
                    
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objproducto_venta->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_venta'] = $objventa->searchDB($act['id_venta'], 'id', 1);
                    $act['id_venta'] = $act['id_venta'][0];
                    $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                    $act['id_producto'] = $act['id_producto'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'checkproducto':
            $data = array();

            $res = $objproducto_venta->consulta_matriz("Select * from producto_venta where estado_fila= 1 AND id = '".$_POST["item"]."'");

            if($res == 0){
                $res = $objproducto_venta->consulta_matriz("Select * from servicio_venta where id = '".$_POST["item"]."'");

            }

            echo json_encode($res);

            break;

        case 'updatecheckproducto':

            if($_POST["opc"] == 'p'){
                $res = $objproducto_venta->consulta_simple("UPDATE producto_venta SET id_venta='".$_POST["idVenta"]."' WHERE id = '".$_POST["item"]."'");
            }else{
                $res = $objproducto_venta->consulta_simple("UPDATE servicio_venta SET id_venta='".$_POST["idVenta"]."' WHERE id = '".$_POST["item"]."'");
            }

            echo json_encode($res);

            break;

        case 'addProdFree':
        // echo "SELECT * producto LIKE '".$_POST["live"]."'";
                $res = $objproducto_venta->consulta_arreglo("SELECT nombre, precio_venta FROM producto WHERE id = '".$_POST["id"]."'");
                echo json_encode($res);
            break;
    }

}

function addProducto($precio, $cantidad, $idProducto, $idNew){

    if($idNew > 0 )
        $prod = array("",$idNew);
    else
        $prod = explode("-", $idProducto);
    $objproducto_venta = new producto_venta();
    $objproducto_venta->setVar('id_venta', $_POST['id_venta']);
    $objproducto_venta->setVar('id_producto', $idProducto);
    $objproducto_venta->setVar('precio', $precio);
    $objproducto_venta->setVar('cantidad', $cantidad);
    //            $objproducto_venta->setVar('total', $_POST['total']);
    $objproducto_venta->setVar('total', $precio*$cantidad);
    $objproducto_venta->setVar('estado_fila', "1");
    if(sizeof($prod)>1){
        $objproducto_venta->setVar('prod_secundario', $prod[1]);
    }else
        $objproducto_venta->setVar('prod_secundario', "0");

    $idg = $objproducto_venta->insertDB();

    $objconn = new turno();
    $config = $objconn->consulta_arreglo("Select * from configuracion where id=1");
    //Identificamos el Almacen
    $ida = 0;
    if(intval($_POST["id_almacen"]) > 0){
        $ida = $_POST["id_almacen"];
    }else{
        $ida = $config["almacen_principal"];
    }
 
    //Ahora hacemos el movimiento
    $objmovimiento_producto = new movimiento_producto();
    $objmovimiento_producto->setVar('id_producto', $idProducto);
    $objmovimiento_producto->setVar('id_almacen',$ida);
    $objmovimiento_producto->setVar('cantidad', $cantidad*-1);
    $objmovimiento_producto->setVar('costo',$precio);
    //Como me da pereza, guardo el id de producto_venta en tipo movimiento
    $objmovimiento_producto->setVar('tipo_movimiento',$idg);
    $objmovimiento_producto->setVar('id_usuario', $_POST['id_usuario']);

    $objmovimiento_producto->setVar('fecha',date("Y-m-d H:i:s"));
    //Obtenemos fecha cierre y turno
    $turno_act = $objconn->consulta_arreglo("Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'");


    $objmovimiento_producto->setVar('id_turno', $turno_act["id"]);
    $objmovimiento_producto->setVar('fecha_cierre', $config["fecha_cierre"]);

    $objmovimiento_producto->setVar('estado_fila', "1");

    $id_movimiento = $objmovimiento_producto->insertDB();

    return $idg;
}

function eliminaVentaOferta($venta, $idOferta){
    $objproducto_venta = new producto_venta();

    if ($idOferta) {
        $objproducto_venta->consulta_simple("DELETE FROM producto_venta_oferta WHERE id_producto_venta = {$venta} AND id_oferta = {$idOferta}");
    } else {
        $objproducto_venta->consulta_simple("DELETE FROM producto_venta_oferta WHERE id_producto_venta = {$venta}");
    }

    /*$oferta = $objproducto_venta->consulta_arreglo(
        "SELECT vo.*
                FROM producto_venta pv
                INNER JOIN venta_oferta vo ON pv.id_venta = vo.id_venta
                WHERE vo.id_venta = {$venta}
                GROUP BY vo.id_oferta");

    if ($oferta) {
        $prods = $objproducto_venta->consulta_matriz(
            "SELECT op.*, vo.id AS v_oferta, pv.cantidad
                    FROM oferta_producto op
                    INNER JOIN producto_venta pv ON op.id_producto=pv.id_producto
                    INNER JOIN venta_oferta vo ON vo.id_oferta = op.id_oferta
                    WHERE vo.id_oferta = {$oferta['id_oferta']}
                    GROUP BY op.id_producto");

        if (sizeof($prods) == 0) {
            $objproducto_venta->consulta_simple("DELETE FROM venta_oferta WHERE id = {$prods['v_oferta']}");
        } else {
            if ($compra) {
                $eliminar = true;
                foreach ($prods as $p) {
                    if ($p['cantidad'] > $compra) {
                        $eliminar = false;
                    }
                }

                if ($eliminar) {
                    $objproducto_venta->consulta_simple("DELETE FROM venta_oferta WHERE id_venta = {$venta}");
                }
            }
        }
    }else{
        $objproducto_venta->consulta_simple("DELETE FROM venta_oferta WHERE id_venta = {$venta}");
    }*/
}
?>