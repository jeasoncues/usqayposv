<?php
require_once('../nucleo/producto.php');
$con = new producto();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add': {
            $descuento = empty($_POST['descuento']) ? 'NULL' : $_POST['descuento'];
            $compra = empty($_POST['cantidad_compra']) ? 'NULL' : $_POST['cantidad_compra'];
            $paga = empty($_POST['cantidad_paga']) ? 'NULL' : $_POST['cantidad_paga'];
            $producto = empty($_POST['producto']) ? 'NULL' : $_POST['producto'];
            $tipo_desc = $_POST['tipo_desc'];

            if ($_POST['id_tipo'] != 3) {
                $tipo_desc = 'NULL';
            }

            $oferta = $con->consulta_id(
                "INSERT INTO oferta (id_tipo, fecha_inicio, fecha_fin, descuento, compra, paga, tipo_desc) VALUES 
                ({$_POST['id_tipo']}, '{$_POST['fecha_inicio']}','{$_POST['fecha_fin']}', $descuento, {$compra}, {$paga}, {$tipo_desc})");

            switch (intval($_POST['id_tipo'])) {
                case 2: {
                    echo $con->consulta_simple("INSERT INTO oferta_producto(id_oferta, id_producto, principal) VALUES ({$oferta}, $producto, true)");
                    break;
                }
                case 3: {
                    for ($i = 1; $i <= $_POST['nro_cupones']; $i++) {
                        $numero = "C".str_pad("{$oferta}0$i", 10, '0', STR_PAD_LEFT);

                        echo $con->consulta_simple("INSERT INTO cupon_oferta(numero, id_oferta) VALUES ('{$numero}', {$oferta})");
                    }
                    break;
                }
            }
            break;
        }

        case 'delOferta': {
            $con->consulta_simple("DELETE FROM oferta WHERE id = {$_POST['id']}");
            break;
        }

        case 'addProducto': {

            $descuento = empty($_POST['descuento']) ? '0' : $_POST['descuento'];
            $oferta = $con->consulta_arreglo("SELECT * FROM oferta WHERE id = {$_POST['idOferta']}");

            switch (intval($oferta['id_tipo'])) {
                case 1: {
                    $existe = $con->consulta_arreglo("SELECT * FROM oferta_producto WHERE id_producto={$_POST['idProducto']} AND id_oferta = {$_POST['idOferta']} LIMIT 1");

                    if (!$existe) {
                        $res = $con->consulta_simple(
                            "INSERT INTO oferta_producto(id_oferta, id_producto, descuento) 
                          VALUES ({$_POST['idOferta']},{$_POST['idProducto']}, {$_POST['descuento']})");
                    }
                    /*if ($oferta['completa'] == 0) {
                        if ($res == 1) {
                            $con->consulta_simple("UPDATE oferta SET completa = 1 WHERE id = {$oferta['id']}");
                        }
                    }*/

                    break;
                }

                case 2: {
                    $existe = $con->consulta_arreglo("SELECT * FROM oferta_producto WHERE id_producto={$_POST['idProducto']} AND id_oferta = {$_POST['idOferta']} LIMIT 1");

                    if (!$existe){
                        $res = $con->consulta_simple(
                            "INSERT INTO oferta_producto(id_oferta, id_producto, descuento, principal) 
                          VALUES ({$_POST['idOferta']},{$_POST['idProducto']}, {$_POST['descuento']}, false)");
                    }
                    break;
                }

                case 3: {
                    $existe = $con->consulta_arreglo("SELECT * FROM oferta_producto WHERE id_producto={$_POST['idProducto']} AND id_oferta = {$_POST['idOferta']} LIMIT 1");

                    if (!$existe) {
                        $res = $con->consulta_simple(
                            "INSERT INTO oferta_producto(id_oferta, id_producto, descuento) 
                          VALUES ({$_POST['idOferta']},{$_POST['idProducto']}, {$_POST['descuento']})");
                    }
                    break;
                }
            }
            break;
        }

        case 'delProducto': {

            $oferta = $con->consulta_arreglo("SELECT * FROM oferta_producto WHERE id = {$_POST['id']}");
            $oferta = $con->consulta_arreglo("SELECT * FROM oferta WHERE id = {$oferta['id_oferta']}");

            $con->consulta_simple("DELETE FROM oferta_producto WHERE id = {$_POST['id']}");

            switch ($oferta['id_tipo']) {
                case 1: {
                    $con->consulta_simple("UPDATE oferta SET completa = 0 WHERE id = {$oferta['id']}");
                    break;
                }
            }
            echo json_encode(1);
            break;
        }

        case 'searchCupon': {
            //busca cupon de descuento a la venta
            $cupon = $con->consulta_arreglo(
                "SELECT co.id_oferta AS oferta, o.tipo_desc as tipo_desc, o.descuento as descuento, co.id
                        FROM oferta o
                        INNER JOIN cupon_oferta co ON co.id_oferta = o.id
                        WHERE o.fecha_inicio <= DATE(NOW()) 
                        AND o.fecha_fin >= DATE(NOW()) 
                        AND o.id_tipo = 3 
                        AND co.usado = FALSE 
                        AND numero = '{$_POST['cupon']}'");

            $idvc = 0;
            if (is_array($cupon)) {

                if ($cupon['tipo_desc'] == 1) { //DESCUENTO A LA VENTA
                    $total = $con->consulta_arreglo("SELECT SUM(total) AS total FROM producto_venta WHERE estado_fila= 1 AND id_venta = {$_POST['id_venta']}")['total'];

                    $descuento = $total * $cupon['descuento'] / 100;
                    $idvc = $con->consulta_id("INSERT INTO venta_cupon (id_venta, id_cupon, descuento) 
                                                  VALUES ({$_POST['id_venta']},{$cupon['id']},{$descuento})");
                    if ($idvc != 0) {
                        $con->consulta_simple("UPDATE cupon_oferta SET usado = TRUE WHERE id = {$cupon['id']}");
                    }
                } elseif ($cupon['tipo_desc'] == 0) { //Descuento a producto
                    $productos_oferta = $con->consulta_matriz("SELECT * FROM oferta_producto WHERE id_oferta = {$cupon['oferta']}");

                    if (is_array($productos_oferta)) {
                        foreach ($productos_oferta as $producto) {

                            $producto_venta = $con->consulta_arreglo(
                                "SELECT * FROM producto_venta
                                          WHERE estado_fila= 1 AND id_venta = {$_POST['id_venta']} AND id_producto = {$producto['id_producto']} LIMIT 1");
                            if (is_array($producto_venta)) {

                                $descuento = $producto_venta['precio'] * $cupon['descuento'] / 100;
                                $idvc = $con->consulta_id("INSERT INTO venta_cupon (id_venta, id_cupon, descuento) 
                                                  VALUES ({$_POST['id_venta']},{$cupon['id']},{$descuento})");
                                if ($idvc != 0) {
                                    $con->consulta_simple("UPDATE cupon_oferta SET usado = TRUE WHERE id = {$cupon['id']}");
                                }
                                break;
                            }
                        }
                    }
                }
            }

            echo json_encode($idvc);
            break;
        }

        case 'getCupones': {
            $cupones = $con->consulta_matriz(
                "SELECT vc.descuento AS descuento, co.numero AS numero, vc.id_cupon AS cupon
                          FROM venta_cupon vc
                          INNER JOIN cupon_oferta co ON vc.id_cupon = co.id
                          INNER JOIN oferta o ON co.id_oferta = o.id
                          WHERE vc.id_venta = {$_POST['id_venta']}");

            echo json_encode($cupones);
            break;
        }

        case 'deleteCupon': {
            $con->consulta_simple("DELETE FROM venta_cupon where id_cupon = {$_POST['cupon']}");
            $con->consulta_simple("UPDATE cupon_oferta SET usado = 0 WHERE id = {$_POST['cupon']}");

            echo json_encode(1);
            break;
        }

        case 'getDescuentosByVenta':{
            $descuentos = $con->consulta_matriz(
                "SELECT o.id_tipo, ot.nombre, o.compra, o.paga, o.descuento, o.tipo_desc, o.descripcion
                        FROM venta_oferta vo
                        INNER JOIN oferta o ON vo.id_oferta = o.id
                        INNER JOIN tipo_oferta ot ON o.id_tipo = ot.id
                        WHERE id_venta = {$_POST['id_venta']}");
            $descripcion=[];
            if (is_array($descuentos)){
                foreach ($descuentos as $d){
                    switch ($d['id_tipo']){
                        case '2':{
                            $descripcion[]="{$d['descripcion']}";
                            break;
                        }
                    }
                }
            }else{
                $descuentos = 0;
            }

            echo json_encode($descripcion);
            break;
        }
    }
}