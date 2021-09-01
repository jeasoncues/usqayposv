<?php

require_once('../nucleo/usuario.php');
$objusuario = new usuario();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objusuario->setVar('id', $_POST['id']);
            $objusuario->setVar('documento', $_POST['documento']);
            $objusuario->setVar('nombres_y_apellidos', $_POST['nombres_y_apellidos']);
            $objusuario->setVar('tipo_usuario', $_POST['tipo_usuario']);
            $objusuario->setVar('password', sha1($_POST['password']));
            // $objusuario->setVar('password', $_POST['password']);
            $objusuario->setVar('estado_fila', $_POST['estado_fila']);

            $id = $objusuario->insertDB();

            $objusuario->consulta_simple("INSERT INTO usuario_modulo_componente (id_usuario, id_modulo_componente) VALUES ({$id}, 26)");

            echo json_encode($id);
            break;

        case 'mod':
            $objusuario->setVar('id', $_POST['id']);
            $objusuario->setVar('documento', $_POST['documento']);
            $objusuario->setVar('nombres_y_apellidos', $_POST['nombres_y_apellidos']);
            $objusuario->setVar('tipo_usuario', $_POST['tipo_usuario']);
            // $objusuario->setVar('password', $_POST['password']);
            if(!empty(($_POST['password']))){
                $objusuario->setVar('password', sha1($_POST['password']));
            }
            $objusuario->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objusuario->updateDB());
            break;

        case 'del':
            $objusuario->setVar('id', $_POST['id']);
            echo json_encode($objusuario->deleteDB());
            break;

        case 'get':
            $res = $objusuario->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objusuario->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    switch(intval($act['tipo_usuario'])){
                        case 1:
                            $act['tipo_usuario'] = "Creador";
                        break;

                        case 2:
                            $act['tipo_usuario'] = "Administrador";
                        break;

                        case 3:
                            $act['tipo_usuario'] = "Cajero";
                        break;

                        case 4:
                            $act['tipo_usuario'] = "Terminal";
                        break;
                    }
                    $act['password'] = "****";
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            -$res = $objusuario->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'addPermiso':
            if (isset($_POST['id_modulo_componente']) && isset($_POST['id_usuario'])) {

                require_once('../nucleo/include/MasterConexion.php');
                $master = new MasterConexion();
                $con = $master->getConnection();

                $usu = mysqli_real_escape_string($con, $_POST['id_usuario']);
                $comp = mysqli_real_escape_string($con, $_POST['id_modulo_componente']);

                $stmt = $con->prepare("REPLACE INTO usuario_modulo_componente (id_usuario, id_modulo_componente) VALUES(?, ?)");
                $stmt->bind_param("ii", $usu, $comp);

                if ($stmt->execute()) {
                    echo json_encode(1);
                } else {
                    echo json_encode(0);
                }
            }
            break;

        case 'delPermiso':
            if (isset($_POST['id_usuario']) && isset($_POST['id_mod_comp'])) {

                require_once('../nucleo/include/MasterConexion.php');
                $master = new MasterConexion();
                $con = $master->getConnection();


                $id_usuario = mysqli_real_escape_string($con, $_POST['id_usuario']);
                $id_mod_comp = mysqli_real_escape_string($con, $_POST['id_mod_comp']);

                $stmt = $con->query("DELETE FROM usuario_modulo_componente WHERE id_usuario = {$id_usuario} AND id_modulo_componente = {$id_mod_comp}");
                if ($stmt) {
                    echo json_encode(1);
                } else {
                    echo json_encode(0);
                }

            }
            break;

        case 'truncate':
            $objusuario->consulta_simple("TRUNCATE TABLE boleta");
            $objusuario->consulta_simple("TRUNCATE TABLE cola_impresion");
            $objusuario->consulta_simple("TRUNCATE TABLE comprobante_hash");
            $objusuario->consulta_simple("TRUNCATE TABLE compra_guia");
            $objusuario->consulta_simple("TRUNCATE TABLE compra");
            $objusuario->consulta_simple("TRUNCATE TABLE guia_producto");
            $objusuario->consulta_simple("TRUNCATE TABLE movimiento_producto");
            $objusuario->consulta_simple("TRUNCATE TABLE nota_cliente");
            $objusuario->consulta_simple("TRUNCATE TABLE entrada_salida");
            $objusuario->consulta_simple("TRUNCATE TABLE factura");
            $objusuario->consulta_simple("TRUNCATE TABLE guia_movimiento");
            $objusuario->consulta_simple("TRUNCATE TABLE movimiento_caja");
            $objusuario->consulta_simple("TRUNCATE TABLE servicio_venta");
            $objusuario->consulta_simple("TRUNCATE TABLE venta");
            $objusuario->consulta_simple("TRUNCATE TABLE venta_medio_pago");
            // TRUNCATE `producto_taxonomiap`;
            // TRUNCATE `proveedor`;
            // TRUNCATE `servicio`;
            // TRUNCATE `servicio_taxonimias`;
            // TRUNCATE `taxonomiap_valor`;
            // TRUNCATE `taxonomias_valor`;
            echo 1;
        break;

        
        case 'server_side':
            $result = $objusuario->ServerSideCotos($_POST);
            echo json_encode($result);
        break;
    }
}?>