<?php

/** Cors */
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Content-Type: application/json');

require_once('./Helpers/Helper.php');

require_once('../nucleo/turno.php');
$objturno = new turno();

require_once('../nucleo/venta.php');
$objventa = new venta();

require_once('../nucleo/producto_venta.php');
$objproducto_venta = new producto_venta();

require_once('../nucleo/configuracion.php');
$objconfiguracion = new configuracion();

require_once('../nucleo/movimiento_producto.php');
$objmovimiento_producto = new movimiento_producto();

require_once('../nucleo/servicio_venta.php');
$objservicio_venta = new servicio_venta();

require_once('../nucleo/servicio_producto.php');
$objservicio_producto = new servicio_producto();

require_once('../nucleo/cliente.php');
$objcliente = new cliente();


if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $venta = $objventa->where(['id', '=', $id]);
    $venta = $venta[0];
    $cliente = null;
    $test =  $objventa->consulta_arreglo("SELECT * FROM venta_medio_pago WHERE id_venta={$id}");
    // echo("se ejecutoo");
    if(is_array($test)){
        $venta['finished'] = true;
    }else{
        $res = $objcliente->searchDB($venta['id_cliente'], 'id', 1);

        if (is_array($res)) {
            $cliente = $res[0];
        }

        /** Productos */
        $query = "SELECT producto_venta.id, producto_venta.id_venta, producto_venta.id_producto, producto_venta.precio,
                    producto_venta.cantidad, producto_venta.total, producto.nombre, producto.incluye_impuesto  FROM producto_venta 
                    INNER JOIN producto ON producto_venta.id_producto = producto.id WHERE producto_venta.estado_fila=1 AND producto_venta.id_venta = {$id}";
        $productos = $objproducto_venta->consulta_matriz($query);
        if ($productos != 0) {
            $productos = array_map(function ($product) {
                $product['is_product'] = true;
                return $product;
            }, $productos);
        } else {
            $productos = [];
        }


        /** Servicios */
        $queryServicios = "SELECT servicio_venta.id, servicio_venta.id_venta, servicio_venta.id_servicio as id_producto, servicio_venta.precio, 
                    servicio_venta.cantidad, servicio_venta.total, servicio.nombre  FROM servicio_venta 
                    INNER JOIN servicio ON servicio_venta.id_servicio = servicio.id WHERE servicio_venta.id_venta = {$id}";
        $servicios = $objservicio_venta->consulta_matriz($queryServicios);
        if ($servicios != 0) {
            $servicios = array_map(function ($servicio) {
                $servicio['is_product'] = false;
                return $servicio;
            }, $servicios);
        } else {
            $servicios = [];
        }
        $venta['producto_venta'] = array_merge($productos, $servicios);
        $venta['cliente'] = $cliente;
        $venta['finished'] = false;
    }        
    echo json_encode($venta);
}

if (isset($_POST['op'])) {
    $test =  $objventa->consulta_arreglo("SELECT * FROM venta_medio_pago WHERE id_venta={$_POST['id']}");
    if(!is_array($test)){
        switch ($_POST['op']) {

            case 'add':
                /** Opción para registrar por primera vez un item de la venta */


                $success = true;
                $msg = "";
                $id_venta = null;

                // Verificar Turno

                $time = date('H:i:s');
                $queryTurno = "SELECT id, nombre from turno WHERE inicio <= '{$time}' and fin > '{$time}' and estado_fila = 1";
                $turno = $objturno->consulta_arreglo($queryTurno);

                if (!$turno) {
                    echo json_encode(['ok' => false, 'msg' => 'No se han registrado turnos']);
                    return;
                }

                $connection = $objventa->getConnection();

                $connection->autocommit(FALSE);

                try {
                    /** Crear Venta */
                    $tipo_comprobante = null;
                    $fecha_hora = date('Y-m-d H:i:s');
                    $fecha_cierre = date('Y-m-d');
                    $id_usuario = $_COOKIE["id_usuario"];
                    $id_caja = $_COOKIE["id_caja"];

                    $objventa->setVar('id', $_POST['id']);
                    /*
                    $objventa->setVar('subtotal', $_POST['subtotal']);
                    $objventa->setVar('total_impuestos', $_POST['total_impuestos']);
                    $objventa->setVar('total', $_POST['total']);
                    */
                    $objventa->setVar('subtotal', null);
                    $objventa->setVar('total_impuestos', null);
                    $objventa->setVar('total', null);
                    $objventa->setVar('tipo_comprobante', $tipo_comprobante);
                    $objventa->setVar('fecha_hora', $fecha_hora);
                    $objventa->setVar('fecha_cierre', $fecha_cierre);
                    $objventa->setVar('id_turno', $turno['id']);
                    $objventa->setVar('id_usuario', $id_usuario);
                    $objventa->setVar('id_caja', $id_caja);
                    //$objventa->setVar('id_usuario', 4);
                    //$objventa->setVar('id_caja', 1);
                    $objventa->setVar('id_cliente', $_POST['id_cliente']);
                    $objventa->setVar('estado_fila', 1);

                    $id_venta = $objventa->insertDB();

                    if ($_POST['is_product'] == 'true') {
                        /** Producto Venta */
                        //$objproducto_venta->setVar('id', $_POST['id']);
                        $objproducto_venta->setVar('id_venta', $id_venta);
                        $objproducto_venta->setVar('id_producto', $_POST['id_producto']);
                        $objproducto_venta->setVar(
                            'precio',
                            $_POST['precio']
                        );
                        $objproducto_venta->setVar('cantidad', $_POST['cantidad']);
                        $objproducto_venta->setVar('total', $_POST['total_producto']);
                        $objproducto_venta->setVar('estado_fila', 1);
                        $producto_venta_id = $objproducto_venta->insertDB();

                        /** Movimiento Producto */
                        // $objmovimiento_producto->setVar('id');
                        $id_almacen = $_POST['id_almacen'];
                        if ($id_almacen == 0) {
                            $conf = "SELECT almacen_principal FROM configuracion WHERE id = 1 ";
                            $almacen = $objconfiguracion->consulta_arreglo($conf);
                            $id_almacen = $almacen['almacen_principal'];
                        }
                        $objmovimiento_producto->setVar('id_producto', $_POST['id_producto']);
                        $objmovimiento_producto->setVar('id_almacen', $id_almacen);
                        $objmovimiento_producto->setVar('cantidad', '-' . $_POST['cantidad']);
                        $objmovimiento_producto->setVar('costo', $_POST['precio']);
                        $objmovimiento_producto->setVar('tipo_movimiento', $producto_venta_id);
                        $objmovimiento_producto->setVar('id_usuario', $id_usuario);
                        //$objmovimiento_producto->setVar('id_usuario', 4);
                        $objmovimiento_producto->setVar('id_turno', $turno['id']);
                        $objmovimiento_producto->setVar('fecha', $fecha_hora);
                        $objmovimiento_producto->setVar('fecha_cierre', $fecha_cierre);
                        $objmovimiento_producto->setVar('estado_fila', 1);

                        $objmovimiento_producto_id = $objmovimiento_producto->insertDB();
                        _testReceta(1, $objmovimiento_producto, $objmovimiento_producto_id, $_POST['id_producto'], $id_almacen, $_POST['cantidad'], $producto_venta_id, $id_usuario, $turno['id'], $fecha_hora, $fecha_cierre);
                        $msg = "Producto agregado correctamente";
                    } else if ($_POST['is_product'] == 'false') {
                        $objservicio_venta->setVar('id_venta', $id_venta);
                        $objservicio_venta->setVar('id_servicio', $_POST['id_producto']);
                        $objservicio_venta->setVar('precio', $_POST['precio']);
                        $objservicio_venta->setVar('cantidad', $_POST['cantidad']);
                        $objservicio_venta->setVar('total', $_POST['total_producto']);
                        $objservicio_venta->setVar('estado', "1");
                        $objservicio_venta->setVar('estado_fila', "1");
                        $idop = $objservicio_venta->insertDB();

                        /** Si el servicio contiene productos */
                        $query = "SELECT id, id_servicio ,id_producto, cantidad, id_almacen 
                        FROM servicio_producto WHERE id_servicio = {$_POST['id_producto']} AND estado_fila = 1";

                        $servicio_producto = $objservicio_producto->consulta_matriz($query);

                        if (is_array($servicio_producto)) {

                            foreach ($servicio_producto as $key => $value) {
                                /** Generar Movimiento Producto */

                                $cantidadFinal = $_POST['cantidad'] * $value['cantidad'];

                                $objmov_producto = new movimiento_producto();
                                $objmov_producto->setVar('id_producto', $value['id_producto']);
                                $objmov_producto->setVar('id_almacen', $value['id_almacen']);
                                // $objmov_producto->setVar('cantidad', '-' . $value['cantidad']);
                                $objmov_producto->setVar('cantidad', '-' . $cantidadFinal);
                                $objmov_producto->setVar('costo', null);
                                $objmov_producto->setVar('tipo_movimiento', $idop);
                                $objmov_producto->setVar('id_usuario', $id_usuario);
                                //$objmov_producto->setVar('id_usuario', 4);
                                $objmov_producto->setVar('id_turno', $turno['id']);
                                $objmov_producto->setVar('fecha', $fecha_hora);
                                $objmov_producto->setVar('fecha_cierre', $fecha_cierre);
                                $objmov_producto->setVar('estado_fila', 1);
                                $objmov_producto->setVar('producto_servicio', false);
                                // $objmov_producto->insertDB();
                                $id_to_test_plato = $objmov_producto->insertDB();
                                _testReceta(3, $objmov_producto, $id_to_test_plato, $value['id_producto'], $value['id_almacen'], $cantidad_final, null, $id_usuario, $turno['id'], $fecha_hora, $fecha_cierre);
                                // producto_venta_id
                            }
                        }

                        $msg = "Servicio agregado correctamente";
                    }
                } catch (\Throwable $e) {
                    $success = false;
                   $msg = $e->getTrace();
                    $connection->rollback();
                }

                if ($success) $connection->commit();

                echo json_encode(["ok" => $success, "msg" => $msg, "id_venta" => $id_venta,"finished"=> false]);

                break;

            case 'update':
                // Opción para registrar luego del primer item de venta

                $success = true;
                $msg = "";
                $id_venta = $_POST['id'];

                // Verificar Turno

                $time = date('H:i:s');
                $queryTurno = "SELECT id, nombre from turno WHERE inicio <= '{$time}' and fin > '{$time}' and estado_fila = 1";
                $turno = $objturno->consulta_arreglo($queryTurno);

                if (!$turno) {
                    echo json_encode(['ok' => false, 'msg' => 'No se han registrado turnos']);
                    return;
                }

                $connection = $objventa->getConnection();

                $connection->autocommit(FALSE);

                try {
                    /** Actualizar Venta */
                    $tipo_comprobante = null;
                    $fecha_hora = date('Y-m-d H:i:s');
                    $fecha_cierre = date('Y-m-d');
                    $id_usuario = $_COOKIE["id_usuario"];
                    $id_caja = $_COOKIE["id_caja"];

                    $objventa->setVar('id', $id_venta);
                    /*
                    $objventa->setVar('subtotal', $_POST['subtotal']);
                    $objventa->setVar('total_impuestos', $_POST['total_impuestos']);
                    $objventa->setVar('total', $_POST['total']);
                    */
                    $objventa->setVar('subtotal', null);
                    $objventa->setVar('total_impuestos', null);
                    $objventa->setVar('total', null);
                    $objventa->setVar('tipo_comprobante', $tipo_comprobante);
                    $objventa->setVar('fecha_hora', $fecha_hora);
                    $objventa->setVar('fecha_cierre', $fecha_cierre);
                    $objventa->setVar('id_turno', $turno['id']);
                    $objventa->setVar('id_usuario', $id_usuario);
                    $objventa->setVar('id_caja', $id_caja);
                    //$objventa->setVar('id_usuario', 4);
                    //$objventa->setVar('id_caja', 1);
                    $objventa->setVar('id_cliente', $_POST['id_cliente']);
                    $objventa->setVar('estado_fila', 1);

                    $resultado = $objventa->updateDB();

                    if ($_POST['is_product'] == 'true') {
                        /** Producto Venta */
                        //$objproducto_venta->setVar('id', $_POST['id']);
                        $objproducto_venta->setVar('id_venta', $id_venta);
                        $objproducto_venta->setVar('id_producto', $_POST['id_producto']);
                        $objproducto_venta->setVar(
                            'precio',
                            $_POST['precio']
                        );
                        $objproducto_venta->setVar('cantidad', $_POST['cantidad']);
                        $objproducto_venta->setVar('total', $_POST['total_producto']);
                        $objproducto_venta->setVar('estado_fila', 1);
                        $producto_venta_id = $objproducto_venta->insertDB();

                        /** Movimiento Producto */
                        // $objmovimiento_producto->setVar('id');
                        $id_almacen = $_POST['id_almacen'];
                        if ($id_almacen == 0) {
                            $conf = "SELECT almacen_principal FROM configuracion WHERE id = 1 ";
                            $almacen = $objconfiguracion->consulta_arreglo($conf);
                            $id_almacen = $almacen['almacen_principal'];
                        }
                        $objmovimiento_producto->setVar('id_producto', $_POST['id_producto']);
                        $objmovimiento_producto->setVar('id_almacen', $id_almacen);
                        $objmovimiento_producto->setVar('cantidad', '-' . $_POST['cantidad']);
                        $objmovimiento_producto->setVar('costo', $_POST['precio']);
                        $objmovimiento_producto->setVar('tipo_movimiento', $producto_venta_id);
                        $objmovimiento_producto->setVar('id_usuario', $id_usuario);
                        //$objmovimiento_producto->setVar('id_usuario', 4);
                        $objmovimiento_producto->setVar('id_turno', $turno['id']);
                        $objmovimiento_producto->setVar('fecha', $fecha_hora);
                        $objmovimiento_producto->setVar('fecha_cierre', $fecha_cierre);
                        $objmovimiento_producto->setVar('estado_fila', 1);

                        $objmovimiento_producto_id = $objmovimiento_producto->insertDB();
                        _testReceta(1, $objmovimiento_producto, $objmovimiento_producto_id, $_POST['id_producto'], $id_almacen, $_POST['cantidad'], $producto_venta_id, $id_usuario, $turno['id'], $fecha_hora, $fecha_cierre);
                        $msg = "Producto agregado correctamente";
                    } else if ($_POST['is_product'] == 'false') {
                        $objservicio_venta->setVar('id_venta', $id_venta);
                        $objservicio_venta->setVar('id_servicio', $_POST['id_producto']);
                        $objservicio_venta->setVar('precio', $_POST['precio']);
                        $objservicio_venta->setVar('cantidad', $_POST['cantidad']);
                        $objservicio_venta->setVar('total', $_POST['total_producto']);
                        $objservicio_venta->setVar('estado', "1");
                        $objservicio_venta->setVar('estado_fila', "1");
                        $idop = $objservicio_venta->insertDB();

                        /** Si el servicio contiene productos */
                        $query = "SELECT id, id_servicio ,id_producto, cantidad, id_almacen 
                        FROM servicio_producto WHERE id_servicio = {$_POST['id_producto']} AND estado_fila = 1";

                        $servicio_producto = $objservicio_producto->consulta_matriz($query);

                        if (is_array($servicio_producto)) {

                            foreach ($servicio_producto as $key => $value) {
                                /** Generar Movimiento Producto */
                                $cantidadFinal = $_POST['cantidad'] * $value['cantidad'];

                                $objmov_producto = new movimiento_producto();
                                $objmov_producto->setVar('id_producto', $value['id_producto']);
                                $objmov_producto->setVar('id_almacen', $value['id_almacen']);
                                // $objmov_producto->setVar('cantidad', '-' . $value['cantidad']);
                                $objmov_producto->setVar('cantidad', '-' . $cantidadFinal);
                                $objmov_producto->setVar('costo', null);
                                $objmov_producto->setVar('tipo_movimiento', $idop);
                                $objmov_producto->setVar('id_usuario', $id_usuario);
                                //$objmov_producto->setVar('id_usuario', 4);
                                $objmov_producto->setVar('id_turno', $turno['id']);
                                $objmov_producto->setVar('fecha', $fecha_hora);
                                $objmov_producto->setVar('fecha_cierre', $fecha_cierre);
                                $objmov_producto->setVar('estado_fila', 1);
                                $objmov_producto->setVar('producto_servicio', false);
                                // $objmov_producto->insertDB();
                                $id_to_test_plato = $objmov_producto->insertDB();
                                _testReceta(3, $objmov_producto, $id_to_test_plato, $value['id_producto'], $value['id_almacen'], $cantidad_final, null, $id_usuario, $turno['id'], $fecha_hora, $fecha_cierre);
                            }
                        }

                        $msg = "Servicio agregado correctamente";
                    }
                } catch (\Throwable $e) {
                    $success = false;
                    $msg = $e->getTrace();
                    $connection->rollback();
                }

                if ($success) $connection->commit();

                echo json_encode(["ok" => $success, "msg" => $msg, "id_venta" => $id_venta,"finished"=> false]);
                break;

            case 'delete_item':
                // Opción para eliminar un producto de la venta
                $success = true;
                $msg = "";
                $id_venta = $_POST['id'];
                $id_producto = $_POST['id_producto'];
                // Verificar Turno

                $time = date('H:i:s');
                $queryTurno = "SELECT id, nombre from turno WHERE inicio <= '{$time}' and fin > '{$time}' and estado_fila = 1";
                $turno = $objturno->consulta_arreglo($queryTurno);

                if (!$turno) {
                    echo json_encode(['ok' => false, 'msg' => 'No se han registrado turnos']);
                    return;
                }


                $connection = $objventa->getConnection();

                $connection->autocommit(FALSE);

                try {
                    /** Actualizar Venta */
                    $tipo_comprobante = null;
                    $fecha_hora = date('Y-m-d H:i:s');
                    $fecha_cierre = date('Y-m-d');
                    $id_usuario = $_COOKIE["id_usuario"];
                    $id_caja = $_COOKIE["id_caja"];

                    $objventa->setVar('id', $id_venta);
                    /*
                    $objventa->setVar('subtotal', $_POST['subtotal']);
                    $objventa->setVar('total_impuestos', $_POST['total_impuestos']);
                    $objventa->setVar('total', $_POST['total']);
                    */
                    $objventa->setVar('subtotal', null);
                    $objventa->setVar('total_impuestos', null);
                    $objventa->setVar('total', null);
                    $objventa->setVar('tipo_comprobante', $tipo_comprobante);
                    $objventa->setVar('fecha_hora', $fecha_hora);
                    $objventa->setVar('fecha_cierre', $fecha_cierre);
                    $objventa->setVar('id_turno', $turno['id']);
                    $objventa->setVar('id_usuario', $id_usuario);
                    $objventa->setVar('id_caja', $id_caja);
                    //$objventa->setVar('id_usuario', 4);
                    //$objventa->setVar('id_caja', 1);
                    $objventa->setVar('id_cliente', $_POST['id_cliente']);
                    $objventa->setVar('estado_fila', 1);

                    $resultado = $objventa->updateDB();

                    if ($_POST['is_product'] == 'true') {
                        /** Producto Venta */
                        $queryProductoVenta = "SELECT id from producto_venta WHERE estado_fila=1 AND id_venta = {$id_venta} AND id_producto = {$id_producto}";
                        $producto_venta = $objproducto_venta->consulta_arreglo($queryProductoVenta);

                        $obj_prod_venta = new producto_venta();
                        $obj_prod_venta->setVar('id', $producto_venta['id']);
                        $obj_prod_venta->deleteDB();

                        /** Movimiento Producto */
                        $queryMovimientoProducto = "SELECT id from movimiento_producto WHERE tipo_movimiento = {$producto_venta['id']} AND id_producto = {$id_producto} and producto_servicio = 1";
                        $movimiento_producto = $objmovimiento_producto->consulta_arreglo($queryMovimientoProducto);

                        $obj_mov_prod = new movimiento_producto();
                        $obj_mov_prod->setVar('id', $movimiento_producto['id']);
                        $obj_mov_prod->deleteDB();
                        // $objmovimiento_producto->setVar('id');
                        _deleteReceta($obj_mov_prod, $movimiento_producto['id'], 1);
                        $msg = "Producto eliminado de la venta correctamente";
                    } else if ($_POST['is_product'] == 'false') {
                        /** Servicio Venta */
                        $queryServicioVenta = "SELECT id from servicio_venta WHERE id_venta = {$id_venta} AND id_servicio = {$id_producto}";
                        $servicio_venta = $objservicio_venta->consulta_arreglo($queryServicioVenta);

                        $obj_serv_venta = new servicio_venta();
                        $obj_serv_venta->setVar('id', $servicio_venta['id']);
                        $obj_serv_venta->deleteDB();
                        $msg = "Servicio eliminado de la venta correctamente";

                        /** Movimiento Producto */
                        $queryMovimientoProducto = "SELECT id from movimiento_producto WHERE tipo_movimiento = {$servicio_venta['id']} AND producto_servicio = 0";
                        $movimiento_producto = $objmovimiento_producto->consulta_matriz($queryMovimientoProducto);
                        if (is_array($movimiento_producto)) {
                            foreach ($movimiento_producto as $key => $v) {
                                $obj_mov_prod = new movimiento_producto();
                                $obj_mov_prod->setVar('id', $v['id']);
                                $obj_mov_prod->deleteDB();
                                _deleteReceta($obj_mov_prod, $v['id'], 2);
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    $success = false;
                  $msg = $e->getTrace();
                    $connection->rollback();
                }

                if ($success) $connection->commit();

                echo json_encode(["ok" => $success, "msg" => $msg, "id_venta" => $id_venta,"finished"=> false]);
                break;

            case 'update_item':
                // Opción para actualizar cantidades y precios de un producto de la venta
                $success = true;
                $msg = "";
                $id_venta = $_POST['id'];
                $id_producto = $_POST['id_producto'];

                // Verificar Turno

                $time = date('H:i:s');
                $queryTurno = "SELECT id, nombre from turno WHERE inicio <= '{$time}' and fin > '{$time}' and estado_fila = 1";
                $turno = $objturno->consulta_arreglo($queryTurno);

                if (!$turno) {
                    echo json_encode(['ok' => false, 'msg' => 'No se han registrado turnos']);
                    return;
                }

                $connection = $objventa->getConnection();

                $connection->autocommit(FALSE);

                try {
                    /** Actualizar Venta */
                    $tipo_comprobante = null;
                    $fecha_hora = date('Y-m-d H:i:s');
                    $fecha_cierre = date('Y-m-d');
                    $id_usuario = $_COOKIE["id_usuario"];
                    $id_caja = $_COOKIE["id_caja"];

                    $objventa->setVar('id', $id_venta);
                    /*
                    $objventa->setVar('subtotal', $_POST['subtotal']);
                    $objventa->setVar('total_impuestos', $_POST['total_impuestos']);
                    $objventa->setVar('total', $_POST['total']);
                    */
                    $objventa->setVar('subtotal', null);
                    $objventa->setVar('total_impuestos', null);
                    $objventa->setVar('total', null);
                    $objventa->setVar('tipo_comprobante', $tipo_comprobante);
                    $objventa->setVar('fecha_hora', $fecha_hora);
                    $objventa->setVar('fecha_cierre', $fecha_cierre);
                    $objventa->setVar('id_turno', $turno['id']);
                    $objventa->setVar('id_usuario', $id_usuario);
                    $objventa->setVar('id_caja', $id_caja);
                    //$objventa->setVar('id_usuario', 4);
                    //$objventa->setVar('id_caja', 1);
                    $objventa->setVar('id_cliente', $_POST['id_cliente']);
                    $objventa->setVar('estado_fila', 1);

                    $resultado = $objventa->updateDB();

                    if ($_POST['is_product'] == 'true') {
                        /** Producto Venta */

                        $queryProductoVenta = "SELECT id from producto_venta WHERE estado_fila=1 AND id_venta = {$id_venta} AND id_producto = {$id_producto}";
                        $producto_venta = $objproducto_venta->consulta_arreglo($queryProductoVenta);

                        $objproducto_venta->setVar('id', $producto_venta['id']);
                        $objproducto_venta->setVar('id_venta', $id_venta);
                        $objproducto_venta->setVar('id_producto', $_POST['id_producto']);
                        $objproducto_venta->setVar(
                            'precio',
                            $_POST['precio']
                        );
                        $objproducto_venta->setVar('cantidad', $_POST['cantidad']);
                        $objproducto_venta->setVar('total', $_POST['total_producto']);
                        $objproducto_venta->setVar('estado_fila', 1);
                        $objproducto_venta->updateDB();
                        /** Movimiento Producto */

                        $queryMovimientoProducto = "SELECT id from movimiento_producto WHERE tipo_movimiento = {$producto_venta['id']} AND id_producto = {$id_producto} and producto_servicio = 1";
                        $movimiento_producto = $objmovimiento_producto->consulta_arreglo($queryMovimientoProducto);
                        $id_almacen = $_POST['id_almacen'];
                        if ($id_almacen == 0) {
                            $conf = "SELECT almacen_principal FROM configuracion WHERE id = 1 ";
                            $almacen = $objconfiguracion->consulta_arreglo($conf);
                            $id_almacen = $almacen['almacen_principal'];
                        }
                        $objmovimiento_producto->setVar('id', $movimiento_producto['id']);
                        $objmovimiento_producto->setVar('id_producto', $_POST['id_producto']);
                        $objmovimiento_producto->setVar('id_almacen', $id_almacen);
                        $objmovimiento_producto->setVar('cantidad', '-' . $_POST['cantidad']);
                        $objmovimiento_producto->setVar('costo', $_POST['precio']);
                        $objmovimiento_producto->setVar('tipo_movimiento', $producto_venta['id']);
                        $objmovimiento_producto->setVar('id_usuario', $id_usuario);
                        //$objmovimiento_producto->setVar('id_usuario', 4);
                        $objmovimiento_producto->setVar('id_turno', $turno['id']);
                        $objmovimiento_producto->setVar('fecha', $fecha_hora);
                        $objmovimiento_producto->setVar('fecha_cierre', $fecha_cierre);
                        $objmovimiento_producto->setVar('estado_fila', 1);
                        $objmovimiento_producto->updateDB();
                        _testReceta(2, $objmovimiento_producto, $movimiento_producto['id'], $_POST['id_producto'], $id_almacen, $_POST['cantidad'], null, $id_usuario, $turno['id'], $fecha_hora, $fecha_cierre);
                        $msg = "Producto actualizado correctamente";
                    } else if ($_POST['is_product'] == 'false') {
                        /** Servicio Venta */

                        $queryServicioVenta = "SELECT id from servicio_venta WHERE id_venta = {$id_venta} AND id_servicio = {$id_producto}";
                        $servicio_venta = $objservicio_venta->consulta_arreglo($queryServicioVenta);

                        $objservicio_venta->setVar('id', $servicio_venta['id']);
                        $objservicio_venta->setVar('id_venta', $id_venta);
                        $objservicio_venta->setVar('id_servicio', $id_producto);
                        $objservicio_venta->setVar(
                            'precio',
                            $_POST['precio']
                        );
                        $objservicio_venta->setVar('cantidad', $_POST['cantidad']);
                        $objservicio_venta->setVar('total', $_POST['total_producto']);
                        $objservicio_venta->setVar('estado_fila', 1);
                        $objservicio_venta->updateDB();

                        /** Movimiento Producto */

                        $queryMovimientoProducto = "SELECT * from movimiento_producto WHERE tipo_movimiento = {$servicio_venta['id']} AND producto_servicio = 0";

                        $movimiento_producto = $objmovimiento_producto->consulta_matriz($queryMovimientoProducto);

                        if (is_array($movimiento_producto)) {
                            foreach ($movimiento_producto as $key => $value) {
                                $obj_s_p = new servicio_producto();
                                $querySv = "SELECT id_producto, cantidad FROM servicio_producto WHERE id_producto = {$value['id_producto']} and id_servicio = {$id_producto}";
                                $servicio_producto = $obj_s_p->consulta_arreglo($querySv);
                                $cantidadFinal = $_POST['cantidad'] * $servicio_producto['cantidad'];

                                $objmpr = new movimiento_producto();
                                $objmpr->setVar('id', $value['id']);
                                $objmpr->setVar('id_producto', $value['id_producto']);
                                $objmpr->setVar('id_almacen', $value['id_almacen']);
                                $objmpr->setVar('cantidad', '-' . $cantidadFinal);
                                $objmpr->setVar('costo', $value['costo']);
                                $objmpr->setVar('tipo_movimiento', $value['tipo_movimiento']);
                                $objmpr->setVar('id_usuario', $id_usuario);
                                //$objmpr->setVar('id_usuario', 4);
                                $objmpr->setVar('id_turno', $turno['id']);
                                $objmpr->setVar('fecha', $fecha_hora);
                                $objmpr->setVar('fecha_cierre', $fecha_cierre);
                                $objmpr->setVar('estado_fila', 1);
                                $objmpr->updateDB();
                                _testReceta(4, $objmpr, $value['id'], $value['id_producto'], $value['id_almacen'], $cantidadFinal, null, $id_usuario, $turno['id'], $fecha_hora, $fecha_cierre);
                            }
                        }

                        $msg = "Servicio actualizado correctamente";
                    }
                } catch (\Throwable $e) {
                    $success = false;
                   $msg = $e->getTrace();
                    $connection->rollback();
                }

                if ($success) $connection->commit();

                echo json_encode(["ok" => $success, "msg" => $msg, "id_venta" => $id_venta,"finished"=> false]);
                break;

            case 'delete_sale':
                /** Eliminar Venta */

                // estado_fila = 2
                // registrar nuevo movimiento_producto con el tipo_movimiento VENTA cantidad positiva
                // movimiento producto -> producto_servicio -> 1 / 0

                // Opción para registrar luego del primer item de venta

                $success = true;
                $msg = "";
                $id_venta = $_POST['id'];

                // Verificar Turno

                $time = date('H:i:s');
                $queryTurno = "SELECT id, nombre from turno WHERE inicio <= '{$time}' and fin > '{$time}' and estado_fila = 1";
                $turno = $objturno->consulta_arreglo($queryTurno);

                if (!$turno) {
                    echo json_encode(['ok' => false, 'msg' => 'No se han registrado turnos']);
                    return;
                }

                $connection = $objventa->getConnection();

                $connection->autocommit(FALSE);

                try {

                    $fecha_hora = date('Y-m-d H:i:s');
                    $fecha_cierre = date('Y-m-d');

                    $id_usuario = $_COOKIE["id_usuario"];
                    $id_caja = $_COOKIE["id_caja"];

                    /** Actualizar Venta */
                    $objventa->setVar('id', $id_venta);

                    $objventa->setVar('estado_fila', 2);
                    $resultado = $objventa->updateDB();

                    /** Productos */
                    $query = "SELECT id FROM producto_venta WHERE estado_fila=1 AND id_venta = {$id_venta}";
                    $producto_venta = $objproducto_venta->consulta_matriz($query);
                    if ($producto_venta != 0) {
                        foreach ($producto_venta as $key => $value) {

                            $objmov_producto = new movimiento_producto();
                            $queryMovimientoProducto = "SELECT id, id_producto, id_almacen, cantidad, costo, tipo_movimiento, id_usuario, id_turno from movimiento_producto WHERE tipo_movimiento = {$value['id']} and producto_servicio = 1";
                            $movimiento_producto = $objmov_producto->consulta_arreglo($queryMovimientoProducto);

                            $objmvp = new movimiento_producto();
                            $objmvp->setVar('id_producto', $movimiento_producto['id_producto']);
                            $objmvp->setVar('id_almacen', $movimiento_producto['id_almacen']);
                            $objmvp->setVar('cantidad', abs($movimiento_producto['cantidad']));
                            $objmvp->setVar('costo', null);
                            $objmvp->setVar('tipo_movimiento', 'VENTA');
                            $objmvp->setVar('id_usuario', $id_usuario);
                            //$objmvp->setVar('id_usuario', 4);
                            $objmvp->setVar('id_turno', $turno['id']);
                            $objmvp->setVar('fecha', $fecha_hora);
                            $objmvp->setVar('fecha_cierre', $fecha_cierre);
                            $objmvp->setVar('estado_fila', 1);
                            $objmvp->setVar('fecha_vencimiento', '0000-00-00');
                            $objmvp->insertDB();
                            _deleteReceta($objmvp, $movimiento_producto['id'], 1);
                        }
                    }

                    /** Servicios */
                    $queryServicios = "SELECT id FROM servicio_venta WHERE id_venta = {$id_venta}";
                    $servicio_venta = $objservicio_venta->consulta_matriz($queryServicios);

                    /** Movimiento Producto */
                    if ($servicio_venta != 0) {
                        foreach ($servicio_venta as $key => $value) {

                            $objm = new movimiento_producto();

                            $queryMovimientoProducto = "SELECT * from movimiento_producto WHERE tipo_movimiento = {$value['id']} AND producto_servicio = 0";

                            $movimiento_producto = $objm->consulta_matriz($queryMovimientoProducto);

                            if (is_array($movimiento_producto)) {
                                foreach ($movimiento_producto as $key => $value) {

                                    $objmvp1 = new movimiento_producto();
                                    $objmvp1->setVar('id_producto', $value['id_producto']);
                                    $objmvp1->setVar('id_almacen', $value['id_almacen']);
                                    $objmvp1->setVar('cantidad', abs($value['cantidad']));
                                    $objmvp1->setVar('costo', null);
                                    $objmvp1->setVar(
                                        'tipo_movimiento',
                                        'VENTA'
                                    );
                                    $objmvp1->setVar('id_usuario', $id_usuario);
                                    //$objmvp1->setVar('id_usuario', 4);
                                    $objmvp1->setVar('id_turno', $turno['id']);
                                    $objmvp1->setVar('fecha', $fecha_hora);
                                    $objmvp1->setVar('fecha_cierre', $fecha_cierre);
                                    $objmvp1->setVar('estado_fila', 1);
                                    $objmvp1->setVar('fecha_vencimiento', '0000-00-00');
                                    $objmvp1->setVar('producto_servicio', false);
                                    $objmvp1->insertDB();
                                    _deleteReceta($objmvp1, $value['id'], 2);
                                }
                            }
                        }
                    }


                    $msg = "Venta anulada correctamente";
                } catch (\Throwable $e) {
                    $success = false;
                   $msg = $e->getTrace();
                    $connection->rollback();
                }

                if ($success) $connection->commit();

                echo json_encode(["ok" => $success, "msg" => $msg, "id_venta" => $id_venta,"finished"=> false]);
                break;

                
                default:
                # code...
                break;
        }        
    }else{
        echo json_encode(["ok" => true, "msg" => "Esta venta ya a sido finalizada, ingrese una nueva venta", "id_venta" => NULL,"finished"=> true]);
    }
}

function _testReceta($opc, $conn, $objmovimiento_producto_id, $id_producto, $id_almacen, $cantidad, $producto_venta_id, $id_usuario, $turno, $fecha_hora, $fecha_cierre)
{
    require_once('../nucleo/movimiento_producto.php');
    if (intval($opc) == 1) { // REGISTRO DESDE PRODUCTO
        $plato = $conn->consulta_arreglo("SELECT * FROM plato WHERE id_producto=" . $id_producto . "");
        if (isset($plato['id'])) {
            $receta = $conn->consulta_matriz("SELECT (r.cantidad*i.valor_insumo) as cantidad_total, r.id_insumo as id_insumo_receta  , r.cantidad as cantidad_receta,i.id_producto id_producto_insumo, i.valor_insumo, i.valor_porcion, i.conversion,i.id_padre as es_pdre FROM receta r INNER JOIN insumo i ON (r.id_insumo=i.id) WHERE r.id_plato=" . $plato['id'] . "");
            if (is_array($receta)) {
                // INSERTAMOS LOS MOVIMIENTOS
                foreach ($receta as $r) {
                    $cantidad_final = $r['cantidad_total'] * $cantidad;
                    $objmovimiento_producto = new movimiento_producto();
                    $objmovimiento_producto->setVar('id_producto', $r['id_producto_insumo']);
                    $objmovimiento_producto->setVar('id_almacen', $id_almacen);
                    $objmovimiento_producto->setVar('cantidad', '-' . $cantidad_final);
                    $objmovimiento_producto->setVar('costo', null);
                    $objmovimiento_producto->setVar('tipo_movimiento', $objmovimiento_producto_id);
                    $objmovimiento_producto->setVar('id_usuario', $id_usuario);
                    $objmovimiento_producto->setVar('id_turno', $turno);
                    $objmovimiento_producto->setVar('fecha', $fecha_hora);
                    $objmovimiento_producto->setVar('fecha_cierre', $fecha_cierre);
                    $objmovimiento_producto->setVar('producto_servicio', 2);
                    $objmovimiento_producto->setVar('id_insumo', $r['id_insumo_receta']);
                    $objmovimiento_producto->setVar('estado_fila', 1);
                    $objmovimiento_producto->insertDB();
                }
            } else {
                // echo json_encode("NO ARRAY");
                // NO HACEMOS NADA
            }
        }
    }
    if (intval($opc) == 2) { // EDICION DESDE UN PRODUCTO
        $insumo_movimiento = $conn->consulta_matriz("SELECT * FROM movimiento_producto WHERE tipo_movimiento=" . $objmovimiento_producto_id . " AND producto_servicio=2");
        if (is_array($insumo_movimiento)) {
            $plato = $conn->consulta_arreglo("SELECT * FROM plato WHERE id_producto=" . $id_producto . "");
            if (isset($plato['id'])) {
                $receta = $conn->consulta_matriz("SELECT (r.cantidad*i.valor_insumo) as cantidad_total, r.id_insumo as id_insumo_receta , r.cantidad as cantidad_recete,i.id_producto id_producto_insumo, i.valor_insumo, i.valor_porcion, i.conversion,i.id_padre as es_pdre FROM receta r INNER JOIN insumo i ON (r.id_insumo=i.id) WHERE r.id_plato=" . $plato['id'] . "");
                if (is_array($receta)) {
                    // INSERTAMOS LOS MOVIMIENTOS                    
                    foreach ($insumo_movimiento as $im) {
                        $objmovimiento_producto = new movimiento_producto();
                        foreach ($receta as $r) {
                            if ($r['id_producto_insumo'] == $im['id_producto'] && $r['id_insumo_receta'] == $im['id_insumo']) {
                                $cantidad_final = $r['cantidad_total'] * $cantidad;
                                $objmovimiento_producto->setVar('id', $im['id']);
                                $objmovimiento_producto->setVar('id_producto', $r['id_producto_insumo']);
                                $objmovimiento_producto->setVar('id_almacen', $id_almacen);
                                $objmovimiento_producto->setVar('cantidad', '-' . $cantidad_final);
                                $objmovimiento_producto->setVar('costo', null);
                                $objmovimiento_producto->setVar('tipo_movimiento', $objmovimiento_producto_id);
                                $objmovimiento_producto->setVar('id_usuario', $id_usuario);
                                //$objmovimiento_producto->setVar('id_usuario', 4);
                                $objmovimiento_producto->setVar('id_turno', $turno);
                                $objmovimiento_producto->setVar('fecha', $fecha_hora);
                                $objmovimiento_producto->setVar('fecha_cierre', $fecha_cierre);
                                $objmovimiento_producto->setVar('producto_servicio', 2);
                                $objmovimiento_producto->setVar('id_insumo', $r['id_insumo_receta']);
                                $objmovimiento_producto->setVar('estado_fila', 1);
                                $objmovimiento_producto->updateDB();
                                break;
                            }
                        }
                    }
                } else {
                    // echo json_encode("NO ARRAY");
                    // NO HACEMOS NADA
                }
            }
        }
    }
    if (intval($opc) == 3) { // REGISTRO DESDE PRODUCTO DE SERVICIO
        $plato = $conn->consulta_arreglo("SELECT * FROM plato WHERE id_producto=" . $id_producto . "");
        if (isset($plato['id'])) {
            $receta = $conn->consulta_matriz("SELECT (r.cantidad*i.valor_insumo) as cantidad_total  , r.id_insumo as id_insumo_receta  , r.cantidad as cantidad_receta,i.id_producto id_producto_insumo, i.valor_insumo, i.valor_porcion, i.conversion,i.id_padre as es_pdre FROM receta r INNER JOIN insumo i ON (r.id_insumo=i.id) WHERE r.id_plato=" . $plato['id'] . "");
            if (is_array($receta)) {
                // INSERTAMOS LOS MOVIMIENTOS
                foreach ($receta as $r) {
                    $cantidad_final = $r['cantidad_total'] * $cantidad;
                    $objmovimiento_producto = new movimiento_producto();
                    $objmovimiento_producto->setVar('id_producto', $r['id_producto_insumo']);
                    $objmovimiento_producto->setVar('id_almacen', $id_almacen);
                    $objmovimiento_producto->setVar('cantidad', '-' . $cantidad_final);
                    $objmovimiento_producto->setVar('costo', null);
                    $objmovimiento_producto->setVar('tipo_movimiento', $objmovimiento_producto_id);
                    $objmovimiento_producto->setVar('id_usuario', $id_usuario);
                    $objmovimiento_producto->setVar('id_turno', $turno);
                    $objmovimiento_producto->setVar('fecha', $fecha_hora);
                    $objmovimiento_producto->setVar('fecha_cierre', $fecha_cierre);
                    $objmovimiento_producto->setVar('producto_servicio', 3);
                    $objmovimiento_producto->setVar('id_insumo', $r['id_insumo_receta']);
                    $objmovimiento_producto->setVar('estado_fila', 1);
                    $objmovimiento_producto->insertDB();
                }
            } else {
                // echo json_encode("NO ARRAY");
                // NO HACEMOS NADA
            }
        }
    }
    if (intval($opc) == 4) { // EDICION DESDE UN PRODUCTO DE UN SERVICIO
        $insumo_movimiento = $conn->consulta_matriz("SELECT * FROM movimiento_producto WHERE tipo_movimiento=" . $objmovimiento_producto_id . " AND producto_servicio=3");
        if (is_array($insumo_movimiento)) {
            $plato = $conn->consulta_arreglo("SELECT * FROM plato WHERE id_producto=" . $id_producto . "");
            if (isset($plato['id'])) {
                $receta = $conn->consulta_matriz("SELECT (r.cantidad*i.valor_insumo) as cantidad_total, r.id_insumo as id_insumo_receta , r.cantidad as cantidad_recete,i.id_producto id_producto_insumo, i.valor_insumo, i.valor_porcion, i.conversion,i.id_padre as es_pdre FROM receta r INNER JOIN insumo i ON (r.id_insumo=i.id) WHERE r.id_plato=" . $plato['id'] . "");
                if (is_array($receta)) {
                    // INSERTAMOS LOS MOVIMIENTOS                    
                    foreach ($insumo_movimiento as $im) {
                        $objmovimiento_producto = new movimiento_producto();
                        foreach ($receta as $r) {
                            if ($r['id_producto_insumo'] == $im['id_producto'] && $r['id_insumo_receta'] == $im['id_insumo']) {
                                $cantidad_final = $r['cantidad_total'] * $cantidad;
                                $objmovimiento_producto->setVar('id', $im['id']);
                                $objmovimiento_producto->setVar('id_producto', $r['id_producto_insumo']);
                                $objmovimiento_producto->setVar('id_almacen', $id_almacen);
                                $objmovimiento_producto->setVar('cantidad', '-' . $cantidad_final);
                                $objmovimiento_producto->setVar('costo', null);
                                $objmovimiento_producto->setVar('tipo_movimiento', $objmovimiento_producto_id);
                                $objmovimiento_producto->setVar('id_usuario', $id_usuario);
                                //$objmovimiento_producto->setVar('id_usuario', 4);
                                $objmovimiento_producto->setVar('id_turno', $turno);
                                $objmovimiento_producto->setVar('fecha', $fecha_hora);
                                $objmovimiento_producto->setVar('fecha_cierre', $fecha_cierre);
                                $objmovimiento_producto->setVar('producto_servicio', 3);
                                $objmovimiento_producto->setVar('id_insumo', $r['id_insumo_receta']);
                                $objmovimiento_producto->setVar('estado_fila', 1);
                                $objmovimiento_producto->updateDB();
                                break;
                            }
                        }
                    }
                } else {
                    // echo json_encode("NO ARRAY");
                    // NO HACEMOS NADA
                }
            }
        }
    }
}


function _deleteReceta($conn, $id_movimiento_producto, $opc)
{
    if (intval($opc) == 1) { // PRODUCTO
        $conn->consulta_simple("DELETE FROM movimiento_producto WHERE producto_servicio=2 AND tipo_movimiento=" . $id_movimiento_producto . "");
    }
    if (intval($opc) == 2) { // SERVICIO
        $conn->consulta_simple("DELETE FROM movimiento_producto WHERE producto_servicio=3 AND tipo_movimiento=" . $id_movimiento_producto . "");
    }
}
