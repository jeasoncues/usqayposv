<?php
require_once '../vendor/PHPMailer_copi/PHPMailerAutoload.php';


require_once('../nucleo/include/MasterConexion.php');
$conn = new MasterConexion();

require_once('../nucleo/venta.php');
$objventa = new venta();

require_once('../nucleo/turno.php');
$objturno = new turno();

require_once('../nucleo/usuario.php');
$objusuario = new usuario();

require_once('../nucleo/caja.php');
$objcaja = new caja();

require_once('../nucleo/cliente.php');
$objcliente = new cliente();

require_once('../nucleo/producto_venta.php');
$objpord_venta = new producto_venta();

require_once('../nucleo/venta_medio_pago.php');
$objventa_medio_pago = new venta_medio_pago();

require_once('../api/classes/PosPrinter.php');
$config = $objventa->consulta_arreglo("SELECT * FROM configuracion");
$from_address="team@hello.sistemausqay.com";
$username="reportes@usqay-cloud.com";
$pass="qkghutdrsdakehqn";

$mailDestino=$config['correoEmisor'];

if (isset($_POST['op'])) {
    switch ($_POST['op']) {

        case 'cliente_gen':
            $venta = $objventa->consulta_arreglo("SELECT * FROM venta WHERE id = '" . $_POST['id'] . "'");
            $venta['cliente'] = $objcliente->consulta_arreglo("SELECT * FROM cliente WHERE id = '" . $venta['id_cliente'] . "'");
            echo json_encode($venta);
            break;

        case 'gen_comp_null':

            $config = $objventa->consulta_arreglo("Select * from configuracion");

            $cliente = 0;
            $idCliente = 0;
            if ($_POST["doc"] != "") {
                $cliente = $objventa->consulta_arreglo("SELECT * FROM cliente WHERE documento =" . $_POST['doc']);
                // echo json_encode($cliente);
                if ($cliente == 0) {
                    if ($_POST["tipo"] == 1) $tipo_cliente = 1;
                    else $tipo_cliente = 2;

                    $objventa->consulta_simple("INSERT INTO cliente(id, nombre, documento, direccion, correo, tipo_cliente, fecha_nacimiento, estado_fila) VALUES ('','" . $_POST['nombre'] . "','" . $_POST['doc'] . "','" . $_POST['direccion'] . "','" . $_POST['correo'] . "','" . $tipo_cliente . "',NULL,'1')");

                    $cliente = $objventa->consulta_arreglo("SELECT * FROM cliente WHERE documento =" . $_POST['doc']);
                }

                $idCliente = $cliente["id"];
            }

            $venta = $objventa->consulta_arreglo("SELECT * FROM venta WHERE id = '" . $_POST['id_venta'] . "'");

            if ($_POST["tipo"] == 1) {

                $serie = $config["serie_boleta"];
                $boleta = $objventa->consulta_arreglo("SELECT * FROM boleta order by id desc limit 1");
                $numero = $boleta['id'] + 1;
                $tipDoc = 2;
                if (empty($_POST["doc"])) {
                    $tipoAdq = '-';
                    $tipoDoc = '-';
                    $tipoNom = '----';
                    $tipoEmail = '';
                } else {
                    $tipoAdq = '1';
                    $tipoDoc = $_POST["doc"];
                    $tipoNom = $_POST["nombre"];
                    $tipoEmail = $_POST["correo"];
                }

                $objventa->consulta_simple("INSERT INTO boleta (id, id_venta, token, serie, estado_fila) VALUES ('" . $numero . "', '" . $venta['id'] . "','','$serie','1')");
                // echo "INSERT INTO boleta (id, id_venta, token, serie, estado_fila) VALUES ('', '0','',$serie','1')";
            } else {
                $serie = $config["serie_factura"];
                $factura = $objventa->consulta_arreglo("SELECT * from factura order by id desc limit 1");
                $numero = $factura['id'] + 1;

                $tipoAdq = '6';
                $tipDoc = 1;


                $tipoDoc = $_POST["doc"];
                $tipoNom = $_POST["nombre"];
                $tipoEmail = $_POST["correo"];

                $objventa->consulta_simple("INSERT INTO factura (id, id_venta, token, serie, estado_fila) VALUES ('" . $numero . "', '" . $venta['id'] . "','','$serie','1')");
            }

            $cabecera = array();

            // echo $numero;

            $cabecera["operacion"] = "generar_comprobante";
            $cabecera["tipo_de_comprobante"] = $tipDoc;
            $cabecera["serie"] = $serie;
            $cabecera["numero"] = str_pad($numero, 8, "0", STR_PAD_LEFT);
            $cabecera["sunat_transaction"] = 1;
            $cabecera["cliente_tipo_de_documento"] = $tipoAdq;
            $cabecera["cliente_numero_de_documento"] = $tipoDoc;
            $cabecera["cliente_denominacion"] = $tipoNom;
            $cabecera["cliente_email"] = $tipoEmail;
            $cabecera["cliente_email_1"] = "";
            $cabecera["cliente_email_2"] = "";
            $cabecera["fecha_de_emision"] = date("d-m-Y");
            $cabecera["fecha_de_vencimiento"] = "";
            $cabecera["moneda"] = 1;
            $cabecera["tipo_de_cambio"] = "";
            $cabecera["porcentaje_de_igv"] = "18.00";
            $cabecera["descuento_global"] =  floatval(0.00);
            $cabecera["total_descuento"] =  floatval(0.00);
            $cabecera["total_anticipo"] = "";
            $cabecera["total_anticipo"] = "";
            $cabecera["total_gravada"] = number_format(floatval($_POST['subtotal']), 3, ".", "");
            $cabecera["total_inafecta"] = "";
            $cabecera["total_exonerada"] = "";
            $cabecera["total_igv"] = number_format(floatval($_POST['igv']), 3, ".", "");
            $cabecera["total_gratuita"] = "";
            $cabecera["total_otros_cargos"] = "";
            $cabecera["total"] = number_format(floatval($_POST['total']), 3, ".", "");
            $cabecera["percepcion_tipo"] = "";
            $cabecera["percepcion_base_imponible"] = "";
            $cabecera["total_percepcion"] = "";
            $cabecera["total_incluido_percepcion"] = "";
            $cabecera["detraccion"] = "false";
            $cabecera["observaciones"] = "";
            $cabecera["documento_que_se_modifica_tipo"] = "";
            $cabecera["documento_que_se_modifica_serie"] = "";
            $cabecera["documento_que_se_modifica_numero"] = "";
            $cabecera["tipo_de_nota_de_credito"] = "";
            $cabecera["tipo_de_nota_de_debito"] = "";
            $cabecera["enviar_automaticamente_a_la_sunat"] = "true";
            $cabecera["enviar_automaticamente_al_cliente"] = "true";
            $cabecera["codigo_unico"] = "";
            $cabecera["condiciones_de_pago"] = "";
            $cabecera["medio_de_pago"] = "";
            $cabecera["placa_vehiculo"] = "";
            $cabecera["orden_compra_servicio"] = "";
            $cabecera["tabla_personalizada_codigo"] = "";
            $cabecera["formato_de_pdf"] = "TICKET";


            $items = array();
            $details = $objventa->consulta_matriz("SELECT * FROM producto_venta WHERE estado_fila= 1 AND  id_venta = '" . $venta['id'] . "'");
            foreach ($details as $value) {
                $item = array();
                $objConn = new venta();
                $producto = $objConn->consulta_arreglo("SELECT * FROM producto WHERE id = '" . $value['id_producto'] . "'");
                $producto_taxonomia = $objConn->consulta_arreglo("SELECT * FROM producto_taxonomiap WHERE id_producto = '" . $value['id_producto'] . "' AND id_taxonomiap = -1");
                $valor_sunat = $producto_taxonomia['valor'];
                $separar = explode("_", $valor_sunat);
                $codigo = $separar[0];

                $neto = $value['precio'] / 1.18;
                $igv = ($value['precio'] - $neto) * $value['cantidad'];

                $item["unidad_de_medida"] = 'NIU';
                $item["codigo"] = $value['id_producto'];
                $item["descripcion"] = $producto['nombre'];
                $item["cantidad"] = $value['cantidad'];
                $item["codigo_producto_sunat"] = $codigo;
                $item["valor_unitario"] = number_format(floatval($neto), 3, ".", "");
                $item["precio_unitario"] = number_format(floatval($value['precio']), 3, ".", "");
                $item["descuento"] = '';
                $item["subtotal"] =  number_format(floatval(($value['precio'] / 1.18) * $value['cantidad']), 3, ".", "");
                $item["tipo_de_igv"] = 1;
                $item["igv"] = number_format($igv, 3, ".", "");
                $item["total"] = number_format(floatval($value['precio'] * $value['cantidad']), 3, ".", "");
                $item["anticipo_regularizacion"] = false;
                $item["anticipo_documento_serie"] = '';

                $items[] = $item;
            }

            


            $cabecera["items"] = $items;
            $data_json = json_encode($cabecera);
            $config = $objventa->consulta_arreglo("Select * from configuracion");
            $ruta = $config["ruta"];
            $token = $config["token"];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ruta);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Authorization: Token token="' . $token . '"',
                    'Content-Type: application/json',
                )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuesta  = curl_exec($ch);
            if (intval(curl_errno($ch)) === 0) {
                curl_close($ch);
                //Verificamos respuesta
                //print_r($respuesta);
                $leer_respuesta = json_decode($respuesta, true);
                if (isset($leer_respuesta['errors'])) {
                    $qr = "Insert into comprobante_hash values('" . str_pad($numero, 8, "0", STR_PAD_LEFT) . "','NO',' ',' ','" . $leer_respuesta['errors'] . "')";
                    $objventa->consulta_simple($qr);
                    // echo "UPDATE venta SET estado_fila = '-1' where id = $numero";
                    //$objventa->consulta_simple("UPDATE venta SET estado_fila = '9' where id = ".$venta['id']."");
                    if ($_POST["tipo"] == 1) {
                        $objventa->consulta_simple("DELETE FROM boleta where id = $numero");
                    } else {
                        $objventa->consulta_simple("DELETE FROM factura where id = $numero");
                    }
                    //Mostramos errores
                    // echo json_encode($leer_respuesta['errors']);
                } else {
                    $aceptada = "NO";
                    if (boolval($leer_respuesta["aceptada_por_sunat"])) {
                        $aceptada = "SI";
                    }
                    $qr = "Insert into comprobante_hash values('" . str_pad($numero, 8, "0", STR_PAD_LEFT) . "','" . $aceptada . "','" . $leer_respuesta["codigo_hash"] . "','" . $leer_respuesta["cadena_para_codigo_qr"] . "','" . $leer_respuesta['sunat_description'] . "')";
                    $objventa->consulta_simple($qr);

                    switch ($_POST["tipo"]) {
                        case 0:
                            $tipoImprime = 'NOT';
                            break;
                        case 1:
                            $tipoImprime = 'BOL';
                            break;
                        case 2:
                            $tipoImprime = 'FAC';
                            break;
                    }

                    $date_created = $venta["fecha_hora"];

                    $objventa->consulta_simple("UPDATE venta SET fecha_hora = '" . date("d-m-Y") . "' where id = " . $venta['id'] . "");

                    // echo "Insert into cola_impresion values(NULL, {$_POST['id']}, '{$tipoImprime}', {$_POST["id_caja"]}, '', 1)";
                    $objventa->consulta_simple("Insert into cola_impresion values(NULL, '" . $venta['id'] . "', '{$tipoImprime}', {$_POST["idCaja"]}, '', 1)");
                    //Mostramos Respuesta
                    // echo json_encode($aceptada);\
                    $objventa->consulta_simple("UPDATE venta SET tipo_comprobante = '" . $_POST["tipo"] . "', id_cliente = '" . $idCliente . "' where id = " . $venta['id'] . "");
                }
            } else {
                curl_close($ch);
                $qr = "Insert into comprobante_hash values('" . str_pad($numero, 8, "0", STR_PAD_LEFT) . "','NE',' ',' ','')";
                $objventa->consulta_simple($qr);
                //Mostramos errores
                echo "NE";
            }

            echo $respuesta;
            break;

        case 'add':
            $objventa->setVar('id', $_POST['id']);
            $objventa->setVar('subtotal', $_POST['subtotal']);
            $objventa->setVar('total_impuestos', $_POST['total_impuestos']);
            $objventa->setVar('total', $_POST['total']);
            $objventa->setVar('tipo_comprobante', $_POST['tipo_comprobante']);
            $objventa->setVar('fecha_hora', $_POST['fecha_hora']);
            $objventa->setVar('fecha_cierre', $_POST['fecha_cierre']);
            $objventa->setVar('id_turno', $_POST['id_turno']);
            $objventa->setVar('id_usuario', $_POST['id_usuario']);
            $objventa->setVar('id_caja', $_POST['id_caja']);
            $objventa->setVar('id_cliente', $_POST['id_cliente']);
            $objventa->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objventa->insertDB());
            break;

        case 'gen':

            //Obtenemos fecha cierre y turno
            $objventa = new turno();
            $miturno = "Select * from turno where inicio <= '" . date("H:i:s") . "' AND fin >= '" . date("H:i:s") . "'";
           
            // echo $miturno;
            $turno_act = $objventa->consulta_arreglo($miturno);
            $cierre_act = $objventa->consulta_arreglo("Select * from configuracion where id=1");

            $objventa = new venta();
            $objventa->setVar('fecha_hora', date("Y-m-d H:i:s"));
            $objventa->setVar('fecha_cierre', $cierre_act["fecha_cierre"]);
            $objventa->setVar('id_turno', $turno_act["id"]);
            $objventa->setVar('id_usuario', $_POST['id_usuario']);
            $objventa->setVar('id_caja', $_POST['id_caja']);
            $objventa->setVar('estado_fila', "1");
            if ($_POST['id_cliente']!=0){
            $objventa->setVar('id_cliente', $_POST['id_cliente']);
            }

            echo json_encode($objventa->insertDB());
            break;

        case 'cambiaimpresion':
            $imprime = 1;
            if (isset($_COOKIE["imprimir"])) {
                $imprime = intval($_COOKIE["imprimir"]);
            }

            if ($imprime === 1) {
                setcookie("imprimir", 0, 2147483647, '/');
            } else {
                setcookie("imprimir", 1, 2147483647, '/');
            }
            echo json_encode(1);
            break;

        case 'cambiadescarga':
            $descarga = 1;
            if (isset($_COOKIE["descargar"])) {
                $descarga = intval($_COOKIE["descargar"]);
            }

            if ($descarga === 1) {
                setcookie("descargar", 0, 2147483647, '/');
            } else {
                setcookie("descargar", 1, 2147483647, '/');
            }
            echo json_encode($descarga);
            break;

        case 'end':
            $objconn = new caja();

            $imprime = 1;
            if (isset($_COOKIE["imprimir"])) {
                $imprime = intval($_COOKIE["imprimir"]);
            }

            if ($imprime === 1) {
                $tipoImprime = '';
                switch ($_POST["tipo_comprobante"]) {
                    case 0:
                        $tipoImprime = 'NOT';
                        break;
                    case 1:
                        $tipoImprime = 'BOL';
                        break;
                    case 2:
                        $tipoImprime = 'FAC';
                        break;
                }

                // echo "Insert into cola_impresion values(NULL, {$_POST['id']}, '{$tipoImprime}', {$_POST["id_caja"]}, '', 1)";
                // $objconn->consulta_simple("Insert into cola_impresion values(NULL, {$_POST['id']}, '{$tipoImprime}', {$_POST["id_caja"]}, '', 1)");
            }

            //Obtenemos serie de boleta y factura

            $series = $objconn->consulta_arreglo("Select * from configuracion where id = 1");

            //Generamos boleta o factura

            switch ($_POST["tipo_comprobante"]) {
                case 1:

                    $idBol = $objconn->consulta_arreglo("SELECT id FROM boleta ORDER by id DESC limit 1");

                    $objconn->consulta_simple("INSERT into boleta values('" . $idBol['id'] . "'+1,'" . $_POST['id'] . "',NULL,'" . $series["serie_boleta"] . "','1')");
                    $tipo = 'B';
                    break;

                case 2:

                    $idFac = $objconn->consulta_arreglo("SELECT id FROM factura ORDER by id DESC limit 1");

                    $objconn->consulta_simple("INSERT into factura values('" . $idFac['id'] . "'+1,'" . $_POST['id'] . "',NULL,'" . $series["serie_factura"] . "','1')");
                    $tipo = 'F';
                    break;
                case 0:
                    $tipo = 0;
                    break;
                default:
                    $tipo = 0;
            }

            //Generamos trabajo impresion

            // echo "JJJJJJ ".$series['fecha_cierre'];

            $objventa = new venta();
            $objventa->setVar('id', $_POST['id']);
            $objventa->setVar('subtotal', $_POST['subtotal']);
            $objventa->setVar('total_impuestos', $_POST['total_impuestos']);
            $objventa->setVar('total', $_POST['total']);
            $objventa->setVar('tipo_comprobante', $_POST['tipo_comprobante']);
            $objventa->setVar('id_cliente', $_POST['id_cliente']);
            $objventa->setVar('fecha_cierre', $series['fecha_cierre']);
            echo json_encode($objventa->updateDB());


            $vn = $objventa->consulta_arreglo("SELECT * FROM ventas_notas where id_venta = " . $_POST["id"]);
            $sum = 0;
            if (is_array($vn)) {
                // $vmp = $objventa->consulta_matriz("SELECT * FROM venta_medio_pago where id_venta = ".$_POST["id"]);
                // echo json_encode($vn);

                // if (is_array($vmp)) {
                //     foreach ($vmp as $mp) {
                //         $sum += $mp["monto"];
                //     }

                $objventa->consulta_simple("UPDATE ventas_notas 
                        SET total = " . $_POST['total'] . ",
                        total_impuestos = " . $_POST['total_impuestos'] . ",
                        subtotal = " . $_POST['subtotal'] . " 
                        WHERE id_venta = " . $_POST["id"]);
                // }
            }

            if ($tipo === 'B') {
                $comprobante = $objconn->consulta_arreglo(
                    "SELECT v.id, v.subtotal, v.total_impuestos, v.total,
                        DATE(v.fecha_hora) as fecha, v.id_cliente, b.id AS correlativo, b.serie
                        FROM venta v
                        INNER JOIN boleta b ON b.id_venta = v.id
                        WHERE v.id = {$_POST['id']}"
                );

                $correlativo = $comprobante['correlativo'];
            } else if ($tipo === 'F') {
                $comprobante = $objconn->consulta_arreglo(
                    "SELECT v.id, v.subtotal, v.total_impuestos, v.total,
                        DATE(v.fecha_hora) as fecha, v.id_cliente, f.id AS correlativo, f.serie
                        FROM venta v
                        INNER JOIN factura f ON f.id_venta = v.id
                        WHERE v.id = {$_POST['id']}"
                );

                $correlativo = $comprobante['correlativo'];
            }

            break;

       /* case 'imprimir':
            //echo "se imprimira un comprobante";
            $objconn = new caja();
          
            $tipo = $_POST['tipo'];
            $id = $_POST['id'];
            $id_caja = $_POST['id_caja'];

            $res = 1;

            if ($tipo == 'BOL') {
                

                $verificaImpresion = $objconn->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='" . $id_caja . "' AND opcion='BOL' ");

                if (isset($verificaImpresion['id'])) {
                   $printer = $objconn->consulta_arreglo("SELECT * FROM impresoras WHERE nombre='" . $verificaImpresion['impresora'] . "'");
                    $printerName = $verificaImpresion['impresora'];
                    $boleta = $objconn->consulta_arreglo("SELECT id, serie FROM boleta WHERE id_venta = {$id}");
                    $receipt = "BOLETA DE VENTA ELECTRONICA" . "\n" . $boleta['serie'] . "-" . str_pad($boleta['id'], 8, "0", STR_PAD_LEFT) . "\n\n";
                    $pos_printer = new PosPrinter($id, $printerName, $receipt);

                    $texto = "Representacion impresa de la \n BOLETA DE VENTA ELECTRONICA";
                    $hash = $objconn->consulta_arreglo("SELECT * FROM new_comprobante_hash WHERE estado=1 AND pkComprobante =" . $boleta['id']);

                    try {



                        if ($printer['red'] == 1) {
                            $pos_printer->connectTypeNetwork($printerName);
                        } else {
                            $pos_printer->connectTypeWindows($printerName);
                        }

                        $pos_printer->setShopName('boleta')
                            ->setTitleReceipt(true)
                            ->setItems()
                            ->setMontos()
                            ->setFooter(true, $texto)
                            ->setQr($boleta['serie'], $boleta['id'], $hash["hash"])
                            ->cut()
                            ->pulse();
                    } catch (Exception $e) {
                        $res = 0;
                    } finally {
                        $pos_printer->close();
                    }
                }
            }

            if ($tipo == 'FAC') {

                $verificaImpresion = $objconn->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='" . $id_caja . "' AND opcion='FAC' ");

                if (isset($verificaImpresion['id'])) {

                    $printer = $objconn->consulta_arreglo("SELECT * FROM impresoras WHERE caja='" . $id_caja . "' AND nombre='" . $verificaImpresion['impresora'] . "'");

                    $printerName = $verificaImpresion['impresora'];

                    $factura = $objconn->consulta_arreglo("SELECT id, serie FROM factura WHERE id_venta = {$id}");
                    $config = $objconn->consulta_arreglo("SELECT * from configuracion");
                    $receipt = "FACTURA ELECTRONICA" . "\n" . $config['serie_factura'] . "-" . str_pad($factura['id'], 8, "0", STR_PAD_LEFT) . "\n\n";
                    $pos_printer = new PosPrinter($id, $printerName, $receipt);

                    $texto = "Representacion impresa de la \n FACTURA DE VENTA ELECTRONICA";
                    $hash = $objconn->consulta_arreglo("SELECT * FROM new_comprobante_hash WHERE estado=1 AND pkComprobante =" . $factura['id']);

                    try {

                        if ($printer['red'] == 1) {
                            $pos_printer->connectTypeNetwork($printerName);
                        } else {
                            $pos_printer->connectTypeWindows($printerName);
                        }

                        $pos_printer->setShopName('factura')
                            ->setTitleReceipt(true)
                            ->setItems()
                            ->setMontos()
                            ->setFooter(true, $texto)
                            ->setQr($config['serie_factura'], $factura['id'], $hash["hash"])
                            ->cut()
                            ->pulse();
                    } catch (Exception $e) {
                        $res = false;
                    } finally {
                        $pos_printer->close();
                    }
                }
            }

            if ($tipo == 'NOT') {
           // echo "imprime NOTA";
                $verificaImpresion = $objconn->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='" . $id_caja . "' AND opcion='NOT' ");

                if (isset($verificaImpresion['id'])) {

                    $printer = $objconn->consulta_arreglo("SELECT * FROM impresoras WHERE caja='" . $id_caja . "' AND nombre='" . $verificaImpresion['impresora'] . "'");

                    $printerName = $verificaImpresion['impresora'];
                    $receipt = "NOTA DE VENTA - " . str_pad($id, 8, "0", STR_PAD_LEFT) . "\n\n";
                    $pos_printer = new PosPrinter($id, $printerName, $receipt);
                    $success = true;
                    $texto = "Representacion impresa de un ticket el cual \n puede canjear por un comprobante electronico";
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
                        $res = false;
                        $msg = $e->getMessage();
                    } finally {
                        $pos_printer->close();
                    }
                }
            }
            if ($tipo == 'COT') {

                $verificaImpresion = $objconn->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='" . $id_caja . "' AND opcion='COT' ");

                if (isset($verificaImpresion['id'])) {

                    $printer = $objconn->consulta_arreglo("SELECT * FROM impresoras WHERE caja='" . $id_caja . "' AND  nombre='" . $verificaImpresion['impresora'] . "'");

                    $printerName = $verificaImpresion['impresora'];
                    $receipt = "COTIZACION  - " . str_pad($id, 8, "0", STR_PAD_LEFT) . "\n\n";
                    $pos_printer = new PosPrinter($id, $printerName, $receipt);
                    $success = true;
                    $texto = "Representacion impresa de una Cotizacion \n";
                    $msg = "VENTA EXITOSA";

                    try {

                        if ($printer['red'] == 1) {
                            $pos_printer->connectTypeNetwork($printerName);
                        } else {
                            $pos_printer->connectTypeWindows($printerName);
                        }

                        $pos_printer
                            ->setShopName('cotiza')
                            ->setTitleReceipt()
                            ->setItems()
                            ->setMontos()
                            ->setFooter(false, $texto)
                            ->cut()
                            ->pulse();
                    } catch (Exception $e) {
                        $res = false;
                        $msg = $e->getMessage();
                    } finally {
                        $pos_printer->close();
                    }
                }
            }
            
            if ($tipo == 'NOTCD') {
                

                $verificaImpresion = $objconn->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='" . $id_caja . "' AND opcion='NOTCD' ");
               
                if (isset($verificaImpresion['id'])) {
                   $printer = $objconn->consulta_arreglo("SELECT * FROM impresoras WHERE nombre='" . $verificaImpresion['impresora'] . "'");
                    $printerName = $verificaImpresion['impresora'];
                    

                    if($_POST['tipoComp']==1){
                        $boleta = $objconn->consulta_arreglo("SELECT id, serie FROM boleta WHERE id_venta = {$id}");
                        $serie=$boleta['serie'];
                        $numero=str_pad($boleta['id'], 8, "0", STR_PAD_LEFT);
                    }else{
                        $factura = $objconn->consulta_arreglo("SELECT id, serie FROM factura WHERE id_venta = {$id}");
                        $serie=$factura['serie'];
                        $numero=str_pad($factura['id'], 8, "0", STR_PAD_LEFT);
                    }
                    
                    $motivoEmision = $objconn->consulta_arreglo("SELECT * FROM motivo_emision  WHERE estado=1 AND id_nota=". $_POST['motivoEmision']." AND nota =" . $_POST['nota']);
                    $receipt = "MOTIVO EMISION: ". $motivoEmision['descripcion'] ."\n\n";
                    if($_POST['nota']==1){
                        $receipt .= "NOTA DE CREDITO ELECTRONICA" . "\n" . $serie. "-" . $numero. "\n\n";
                        $texto = "Representacion impresa de la \n NOTA DE CREDITO ELECTRONICA";
                    }else{
                        $receipt .= "NOTA DE DEBITO ELECTRONICA" . "\n" . $serie. "-" . $numero. "\n\n";
                        $texto = "Representacion impresa de la \n NOTA DE DEBITO ELECTRONICA";
                    }
                    
                    $pos_printer = new PosPrinter($id, $printerName, $receipt);
                    $hash = $objconn->consulta_arreglo("SELECT * FROM new_comprobante_hash WHERE estado=1 AND pkComprobante =" . $numero);
                    
                 

                    try {



                        if ($printer['red'] == 1) {
                            $pos_printer->connectTypeNetwork($printerName);
                        } else {
                            $pos_printer->connectTypeWindows($printerName);
                        }

                        $pos_printer->setShopName('notacd')
                            ->setTitleReceipt(true)
                            ->setItems()
                            ->setMontos()
                            ->setFooter(true, $texto)
                            ->setQr($serie, $numero, $hash["hash"])
                            ->cut()
                            ->pulse();
                    } catch (Exception $e) {
                        $res = 0;
                    } finally {
                        $pos_printer->close();
                    }
                }
            }

            echo json_encode($res);

            break;*/
        case 'imprimir':
            $objconn = new caja();
            $objconn->consulta_simple("Insert into cola_impresion values(NULL, {$_POST['id']}, '{$_POST['tipo']}', {$_POST["id_caja"]}, '', 1)");
            echo json_encode(1);
            break;
        case 'addGuia':
            $objconn = new caja();

            echo $objconn->consulta_simple("Insert into guia_remision values(NULL, " . $_POST['id'] . ", '" . date("Y-m-d H:i:s") . "')");

            break;

        case 'mod':
            // echo "modificar la enta";
            $config = $objventa->consulta_arreglo("Select * from configuracion");
            // echo $config['fecha_cierre'];
            $objventa->setVar('id', $_POST['id']);
            $objventa->setVar('subtotal', $_POST['subtotal']);
            $objventa->setVar('total_impuestos', $_POST['total_impuestos']);
            $objventa->setVar('total', $_POST['total']);
            $objventa->setVar('tipo_comprobante', $_POST['tipo_comprobante']);
            $objventa->setVar('fecha_hora', $_POST['fecha_hora']);
            $objventa->setVar('fecha_cierre', $config['fecha_cierre']);
            $objventa->setVar('id_turno', $_POST['id_turno']);
            $objventa->setVar('id_usuario', $_POST['id_usuario']);
            $objventa->setVar('id_caja', $_POST['id_caja']);
            $objventa->setVar('id_cliente', $_POST['id_cliente']);
            $objventa->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objventa->updateDB());
            break;
        case 'anulaventaCotos':
            $objventa = new venta();
            $objventa->setVar('id', $_POST['id']);
            $objventa->getDB();
            $res = $objventa->consulta_arreglo("SELECT * FROM venta WHERE id = " . $_POST["id"]);
            $respuesta = [
                'success' => false,
                'message' => "Operacion No Exitosa, no se pudo procesar",
            ];

            if ($res['tipo_comprobante'] == 1 || $res['tipo_comprobante'] == 2) {
                $valida_fecha = _valida_fecha($res['fecha_hora']);
                if ($valida_fecha['success'] == true) {
                    $consulta = _consultar_documento($objventa, $res['id'], $res['tipo_comprobante']);
                    if ($consulta['success'] == true) {
                        if ($res['tipo_comprobante'] == 1)
                            $tipo = '2';
                        else
                            $tipo = '1';
                        $anul = $objventa->consulta_arreglo("
                                SELECT * FROM boleta where id_venta = " . $_POST["id"] . "
                                UNION
                                SELECT * FROM factura where id_venta = " . $_POST["id"] . "");

                        $cabecera = array();
                        $cabecera["operacion"] = "generar_anulacion";
                        $cabecera["tipo_de_comprobante"] = $tipo;
                        $cabecera["serie"] = $anul["serie"];
                        $cabecera["numero"] = $anul["id"];
                        $cabecera["motivo"] = "ERROR DE SISTEMA";
                        // print_r($cabecera);
                        $data_json = json_encode($cabecera);

                        $config = $objventa->consulta_arreglo("Select * from configuracion");
                        $ruta = $config["ruta"];
                        $token = $config["token"];

                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $ruta);
                        curl_setopt(
                            $ch,
                            CURLOPT_HTTPHEADER,
                            array(
                                'Authorization: Token token="' . $token . '"',
                                'Content-Type: application/json',
                            )
                        );
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $respuestaEliminacion  = curl_exec($ch);                        
                        if ($respuestaEliminacion != "") {                            
                                $n = (array) json_decode(stripslashes($respuestaEliminacion));                               
                                if( !isset($n["errors"])){
                                    $objconn = new caja();
                                    // echo "Select * from producto_venta where id_venta = '".$_POST["id"];
                                    $productos = $objconn->consulta_matriz("Select * from producto_venta where estado_fila= 1 AND id_venta = '" . $_POST["id"] . "'");
                                    $servicios = $objconn->consulta_matriz("Select * from servicio_venta where id_venta = '" . $_POST["id"] . "'");

                                    //Eliminamos Pagos de paso
                                    $pagos = $objconn->consulta_matriz("Select * from venta_medio_pago where id_venta = '" . $_POST["id"] . "'");
                                    if (is_array($pagos)) {
                                        $cambio = $objconn->consulta_arreglo("Select * from tipo_cambio where moneda_origen = 'USD' AND moneda_destino = 'PEN'");
                                        foreach ($pagos as $pa) {
                                            $monto = $pa["monto"] - $pa["vuelto"];
                                            if ($pa["moneda"] === "USD") {
                                                $monto = floatval($pa["monto"]) * floatval($cambio["compra"]);
                                            }
                                            if ($pa["medio"] != "DESCUENTO") {
                                                $objconn->consulta_simple("Insert into movimiento_caja values(NULL,'" . $objventa->getIdCaja() . "','-" . $monto . "','SELL|" . $pa["moneda"] . "|" . $pa["medio"] . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','" . $objventa->getIdTurno() . "','" . $objventa->getIdUsuario() . "','1')");
                                            }
                                            $objconn->consulta_simple("UPDATE venta_medio_pago set estado_fila = 2 where id = '" . $pa["id"] . "'");
                                            // Cambios de estados a los movimiento caja generados por venta medio pago
                                            $movimient_c = $objconn->consulta_arreglo("SELECT * FROM movimiento_caja WHERE tipo_movimiento like concat('SELL','|','%') AND tipo_movimiento like concat('%','|','" . $pa["id"] . "')");

                                            if (is_array($movimient_c)) {
                                                $objconn->consulta_simple("UPDATE movimiento_caja set estado_fila = 2 where id = '" . $movimient_c["id"] . "'");
                                            }
                                        }
                                    }

                                    if (is_array($productos)) {
                                        foreach ($productos as $pr) {
                                            $objconn->consulta_simple("Insert into movimiento_producto values(NULL,'" . $pr["id_producto"] . "','1','" . $pr["cantidad"] . "',NULL,'VENTA','" . $objventa->getIdUsuario() . "','" . $objventa->getIdTurno() . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','1','','','1',NULL)");
                                            $objconn->consulta_simple("UPDATE producto_venta where estado_fila = 2 id = '" . $pr["id"] . "'");
                                            $queryMovimientoProducto = "SELECT id, id_producto, id_almacen, cantidad, costo, tipo_movimiento, id_usuario, id_turno from movimiento_producto WHERE tipo_movimiento = {$pr['id']} and producto_servicio = 1";
                                            $movimiento_producto = $objconn->consulta_arreglo($queryMovimientoProducto);
                                            _deleteReceta($objconn, $pr['id'], 1);
                                        }
                                    }

                                    if (is_array($servicios)) {
                                        foreach ($servicios as $ser) {
                                            $proxser = $objconn->consulta_matriz("Select * from servicio_producto where id_servicio = '" . $ser["id_servicio"] . "'");
                                            if (is_array($proxser)) {
                                                foreach ($proxser as $ps) {
                                                    $objconn->consulta_simple("Insert into movimiento_producto values(NULL,'" . $ps["id_producto"] . "','" . $ps["id_almacen"] . "','" . $ps["cantidad"] . "',NULL,'VENTA','" . $objventa->getIdUsuario() . "','" . $objventa->getIdTurno() . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','1','','','0',NULL)");

                                                    $queryMovimientoProducto = "SELECT id, id_producto, id_almacen, cantidad, costo, tipo_movimiento, id_usuario, id_turno from movimiento_producto WHERE tipo_movimiento = {$pr['id']} and producto_servicio = 1";
                                                    $movimiento_producto = $objconn->consulta_arreglo($queryMovimientoProducto);
                                                    _deleteReceta($objconn, $ps['id'], 2);
                                                }
                                            }
                                            $objconn->consulta_simple("UPDATE servicio_venta set estado_fila = 2  where id = '" . $ser["id"] . "'");
                                        }
                                    }
                                    $objconn->consulta_simple("UPDATE venta set estado_fila = 2 where id = '" . $_POST["id"] . "'");
                                    $respuesta['success'] = true;
                                    $respuesta['message'] = "Operacion Exitosa";
                                }else{                                    
                                    $respuesta['success'] = false;
                                    $respuesta['message'] = $n["errors"];
                                }
                            
                        }else{
                            $respuesta['success'] = false;
                            $respuesta['message'] = "Se produjo un error de Conectividad";
                        }
                    } else {
                        $respuesta['success'] = false;
                        $respuesta['message'] = $consulta['message'];
                    }
                } else {
                    $respuesta['success'] = false;
                    $respuesta['message'] = $valida_fecha['message'];
                }
            } else {
                $objconn = new caja();
                // echo "Select * from producto_venta where id_venta = '".$_POST["id"];
                $productos = $objconn->consulta_matriz("Select * from producto_venta where estado_fila= 1 AND id_venta = '" . $_POST["id"] . "'");
                $servicios = $objconn->consulta_matriz("Select * from servicio_venta where id_venta = '" . $_POST["id"] . "'");

                //Eliminamos Pagos de paso
                $pagos = $objconn->consulta_matriz("Select * from venta_medio_pago where id_venta = '" . $_POST["id"] . "'");
                if (is_array($pagos)) {
                    $cambio = $objconn->consulta_arreglo("Select * from tipo_cambio where moneda_origen = 'USD' AND moneda_destino = 'PEN'");
                    foreach ($pagos as $pa) {
                        $monto = $pa["monto"] - $pa["vuelto"];
                        if ($pa["moneda"] === "USD") {
                            $monto = floatval($pa["monto"]) * floatval($cambio["compra"]);
                        }
                        if ($pa["medio"] != "DESCUENTO") {
                            $objconn->consulta_simple("Insert into movimiento_caja values(NULL,'" . $objventa->getIdCaja() . "','-" . $monto . "','SELL|" . $pa["moneda"] . "|" . $pa["medio"] . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','" . $objventa->getIdTurno() . "','" . $objventa->getIdUsuario() . "','1')");
                        }
                        $objconn->consulta_simple("UPDATE venta_medio_pago set estado_fila = 2 where id = '" . $pa["id"] . "'");
                        // Cambios de estados a los movimiento caja generados por venta medio pago
                        $movimient_c = $objconn->consulta_arreglo("SELECT * FROM movimiento_caja WHERE tipo_movimiento like concat('SELL','|','%') AND tipo_movimiento like concat('%','|','" . $pa["id"] . "')");

                        if (is_array($movimient_c)) {
                            $objconn->consulta_simple("UPDATE movimiento_caja set estado_fila = 2 where id = '" . $movimient_c["id"] . "'");
                        }
                    }
                }

                if (is_array($productos)) {
                    foreach ($productos as $pr) {
                        $objconn->consulta_simple("Insert into movimiento_producto values(NULL,'" . $pr["id_producto"] . "','1','" . $pr["cantidad"] . "',NULL,'VENTA','" . $objventa->getIdUsuario() . "','" . $objventa->getIdTurno() . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','1','','','1',NULL)");
                        $objconn->consulta_simple("UPDATE producto_venta where estado_fila = 2 id = '" . $pr["id"] . "'");
                        $queryMovimientoProducto = "SELECT id, id_producto, id_almacen, cantidad, costo, tipo_movimiento, id_usuario, id_turno from movimiento_producto WHERE tipo_movimiento = {$pr['id']} and producto_servicio = 1";
                        $movimiento_producto = $objconn->consulta_arreglo($queryMovimientoProducto);
                        _deleteReceta($objconn, $pr['id'], 1);
                    }
                }

                if (is_array($servicios)) {
                    foreach ($servicios as $ser) {
                        $proxser = $objconn->consulta_matriz("Select * from servicio_producto where id_servicio = '" . $ser["id_servicio"] . "'");
                        if (is_array($proxser)) {
                            foreach ($proxser as $ps) {
                                $objconn->consulta_simple("Insert into movimiento_producto values(NULL,'" . $ps["id_producto"] . "','" . $ps["id_almacen"] . "','" . $ps["cantidad"] . "',NULL,'VENTA','" . $objventa->getIdUsuario() . "','" . $objventa->getIdTurno() . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','1','','','0',NULL)");

                                $queryMovimientoProducto = "SELECT id, id_producto, id_almacen, cantidad, costo, tipo_movimiento, id_usuario, id_turno from movimiento_producto WHERE tipo_movimiento = {$pr['id']} and producto_servicio = 1";
                                $movimiento_producto = $objconn->consulta_arreglo($queryMovimientoProducto);
                                _deleteReceta($objconn, $ps['id'], 2);
                            }
                        }
                        $objconn->consulta_simple("UPDATE servicio_venta set estado_fila = 2  where id = '" . $ser["id"] . "'");
                    }
                }

                $objconn->consulta_simple("UPDATE venta set estado_fila = 2 where id = '" . $_POST["id"] . "'");
                $respuesta['success'] = true;
                $respuesta['message'] = "Operacion Exitosa";
            }
            if ($respuesta['success'] == true) {
               $vent_nul = $objventa->consulta_arreglo("SELECT u.nombres_y_apellidos as usuario, t.nombre as turno , v.fecha_hora  as fecha,v.total as total
                                                    FROM venta v 
                                                    inner join usuario u on u.id = v.id_usuario 
                                                    inner join turno t on t.id = v.id_turno WHERE v.id = " . $_POST["id"]."");
                $prod_venta = $objpord_venta->consulta_matriz("SELECT p.nombre,pv.precio,pv.cantidad,pv.total FROM producto_venta pv inner join producto p on  pv.id_producto = p.id WHERE pv.estado_fila= 1 AND pv.id_venta = " . $_POST["id"]);
                
               
                $fecha = date("Y-m-d");
                $hora = date('H:i:s');
                
                 //Create a new PHPMailer instance
                $mail = new PHPMailer(true);
                $mail->IsSMTP();

                 //Configuracion servidor mail
                $mail->From = $from_address; //remitente
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls'; //seguridad
                $mail->Host = "smtp.gmail.com"; // servidor smtp
                $mail->Port = 587; //puerto
                $mail->Username =$username; //nombre usuario
                $mail->Password = $pass; //contraseña
                
                //Agregar destinatario
                $mail->AddAddress($mailDestino);//correo destino
                $mail->Subject = "Anulacion de venta";// asunto,'total_vendido','file/imagen.jpg','base64','image/jpeg'
                $mail->AddEmbeddedImage('../recursos/img/logo.png','logo.png');
                $message="
                <body class=''>
                <style>
                        @charset 'UTF-8';
                        body {
                            font-family: 'Roboto', Arial, serif;
                            background: transparent;
                            border: 20px #00395e solid;
                            -webkit-font-smoothing: antialiased;
                        }

                        img {
                            max-width: 100%;
                            height: auto;		
                        }
                        .logo{
                            margin-top: 1.5em;
                        width: 50%;
                        
                        text-align: center;
                        }
                        .marg{
                            margin-left: 3em;
                            margin-right: 3em;
                        }
                        p{
                            text-align: justify;
                        }
                        
                        h1, h2, h3, h4, h5, h6 {
                        color: rgba(0, 0, 0, 0.8);
                        font-family: 'Roboto', Arial, serif;
                        font-weight: 300;
                        margin: 0 0 30px 0;
                        }
                        table {    
                            font-family: 'Lucida Sans Unicode', 'Lucida Grande', Sans-Serif;
                            font-size: 12px;   
                            margin: 45px;    
                            width: 480px;
                            text-align: left;  
                            border-collapse: collapse;
                        }

                        th {     
                            font-size: 13px;     
                            font-weight: normal;     
                            padding: 8px;     
                            background: #b9c9fe;
                            border-top: 4px solid #aabcfe;    
                            border-bottom: 1px solid #fff; 
                            color: #039; }

                        td {    
                            padding: 8px;     
                            background: #e8edff;    
                            border-bottom: 1px solid #fff;
                            color: #00395e;    
                            border-top: 1px solid transparent; }

                    
                    </style>
                            <center>
                                <img class='logo'   src=\"cid:logo.png\" /> 
                            </center>
                            <div class='marg' >
                                <br>
                                <p>
                                Hola, te informamos que siendo las ".$hora." del ".$fecha." una venta fue anulada con los siguientes datos:
                                </p>
                                
                                <p>
                                Usuario:". $vent_nul['usuario']."
                                </p>
                                <p>
                                Turno:". $vent_nul['turno']."
                                </p>
                                <p>
                                Fecha de Venta: ". $vent_nul['fecha']."
                                </p>
                                 <center>
                                    <table cellspacing='0' cellpadding='0'>                                        <tr>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Cantidad</th>
                                            <th>Sub Total</th>
                                        </tr>
                                   
                                ";
                                 foreach ($prod_venta as $value):
                                     $message .="
                                        <tr>
                                            <td> ".$value["nombre"]. " </td>
                                            <td> ".$value["precio"]." </td>
                                            <td> ".$value["cantidad"]." </td>
                                            <td> ".$value["total"]." </td>
                                        </tr>";
                                 endforeach;
                            
                               $message .="
                                     <tr>
                                            <td> </td>
                                            <td> </td>
                                            <td> TOTAL: </td>
                                            <td> ".$vent_nul["total"]." </td>
                                        </tr>
                                </table>
                                 </center>
                                <p>
                                Recuerda que estamos para ayudarte cuando lo necesites
                                </p>
                            <center>
                                <h4>
                                Usqay Sistema de Negocios
                                </h4>
                                <h4>
                                 <a href=''>Www.sistemausqay.com</a>
                                </h4>
                                <h4>
                                    Central telefonica: (01) 642 9247
                                </h4>
                            </center>

                            </div>

                        </div>
                </body>";
            

                $mail->Body = $message;
                $mail->AltBody  = $message;
            

                if ($mail->Send()) {
                    echo "msg exito";
                } else {
                    echo "error";
                }
                
            }
            echo json_encode($respuesta);
            break;

        case 'anulaventa':
            $objventa = new venta();
            $objventa->setVar('id', $_POST['id']);
            $objventa->getDB();

            $objconn = new caja();
            // echo "Select * from producto_venta where id_venta = '".$_POST["id"];
            $productos = $objconn->consulta_matriz("Select * from producto_venta where estado_fila= 1 AND  id_venta = '" . $_POST["id"] . "'");
            $servicios = $objconn->consulta_matriz("Select * from servicio_venta where id_venta = '" . $_POST["id"] . "'");

            //Eliminamos Pagos de paso
            $pagos = $objconn->consulta_matriz("Select * from venta_medio_pago where id_venta = '" . $_POST["id"] . "'");
            if (is_array($pagos)) {
                $cambio = $objconn->consulta_arreglo("Select * from tipo_cambio where moneda_origen = 'USD' AND moneda_destino = 'PEN'");
                foreach ($pagos as $pa) {
                    $monto = $pa["monto"] - $pa["vuelto"];
                    if ($pa["moneda"] === "USD") {
                        $monto = floatval($pa["monto"]) * floatval($cambio["compra"]);
                    }
                    if ($pa["medio"] != "DESCUENTO") {
                        $objconn->consulta_simple("Insert into movimiento_caja values(NULL,'" . $objventa->getIdCaja() . "','-" . $monto . "','SELL|" . $pa["moneda"] . "|" . $pa["medio"] . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','" . $objventa->getIdTurno() . "','" . $objventa->getIdUsuario() . "','1')");
                    }
                    $objconn->consulta_simple("UPDATE venta_medio_pago set estado_fila = 2 where id = '" . $pa["id"] . "'");
                    // Cambios de estados a los movimiento caja generados por venta medio pago
                    $movimient_c = $objconn->consulta_arreglo("SELECT * FROM movimiento_caja WHERE tipo_movimiento like concat('SELL','|','%') AND tipo_movimiento like concat('%','|','" . $pa["id"] . "')");

                    if (is_array($movimient_c)) {
                        $objconn->consulta_simple("UPDATE movimiento_caja set estado_fila = 2 where id = '" . $movimient_c["id"] . "'");
                    }
                }
            }

            if (is_array($productos)) {
                foreach ($productos as $pr) {
                    $objconn->consulta_simple("Insert into movimiento_producto values(NULL,'" . $pr["id_producto"] . "','1','" . $pr["cantidad"] . "',NULL,'VENTA','" . $objventa->getIdUsuario() . "','" . $objventa->getIdTurno() . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','1','','','1',NULL)");
                    $objconn->consulta_simple("UPDATE producto_venta where estado_fila = 2 id = '" . $pr["id"] . "'");
                    $queryMovimientoProducto = "SELECT id, id_producto, id_almacen, cantidad, costo, tipo_movimiento, id_usuario, id_turno from movimiento_producto WHERE tipo_movimiento = {$pr['id']} and producto_servicio = 1";
                    $movimiento_producto = $objconn->consulta_arreglo($queryMovimientoProducto);
                    _deleteReceta($objconn, $pr['id'], 1);
                }
            }

            if (is_array($servicios)) {
                foreach ($servicios as $ser) {
                    $proxser = $objconn->consulta_matriz("Select * from servicio_producto where id_servicio = '" . $ser["id_servicio"] . "'");
                    if (is_array($proxser)) {
                        foreach ($proxser as $ps) {
                            $objconn->consulta_simple("Insert into movimiento_producto values(NULL,'" . $ps["id_producto"] . "','" . $ps["id_almacen"] . "','" . $ps["cantidad"] . "',NULL,'VENTA','" . $objventa->getIdUsuario() . "','" . $objventa->getIdTurno() . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','1','','','0',NULL)");

                            $queryMovimientoProducto = "SELECT id, id_producto, id_almacen, cantidad, costo, tipo_movimiento, id_usuario, id_turno from movimiento_producto WHERE tipo_movimiento = {$pr['id']} and producto_servicio = 1";
                            $movimiento_producto = $objconn->consulta_arreglo($queryMovimientoProducto);
                            _deleteReceta($objconn, $ps['id'], 2);
                        }
                    }
                    $objconn->consulta_simple("UPDATE servicio_venta set estado_fila = 2  where id = '" . $ser["id"] . "'");
                }
            }

            $objconn->consulta_simple("UPDATE venta set estado_fila = 2 where id = '" . $_POST["id"] . "'");

            $res = $objventa->consulta_arreglo("SELECT * FROM venta WHERE id = " . $_POST["id"]);
            $config = $objventa->consulta_arreglo("Select * from configuracion");
            $respuesta = 1;
            if ($res['tipo_comprobante'] == 1 || $res['tipo_comprobante'] == 2) {

                if ($res['tipo_comprobante'] == 1)
                    $tipo = '2';
                else
                    $tipo = '1';

                $anul = $objventa->consulta_arreglo("
                    SELECT * FROM boleta where id_venta = " . $_POST["id"] . "
                    UNION
                    SELECT * FROM factura where id_venta = " . $_POST["id"] . "");

                $cabecera = array();
                $cabecera["operacion"] = "generar_anulacion";
                $cabecera["tipo_de_comprobante"] = $tipo;
                $cabecera["serie"] = $anul["serie"];
                $cabecera["numero"] = $anul["id"];
                $cabecera["motivo"] = "ERROR DE SISTEMA";
                // print_r($cabecera);
                $data_json = json_encode($cabecera);

                $config = $objventa->consulta_arreglo("Select * from configuracion");
                $ruta = $config["ruta"];
                $token = $config["token"];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ruta);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Authorization: Token token="' . $token . '"',
                        'Content-Type: application/json',
                    )
                );
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $respuesta  = curl_exec($ch);
            }

            echo $respuesta;
            break;
        case 'anulaProdNota':
        
           
                $objventa = new venta();
                $objventa->setVar('id', $_POST['id']);
                $objventa->getDB();
                $objconn = new caja();
                $productos = $objconn->consulta_matriz("Select * from producto_venta where estado_fila= 1 AND id_venta = '" . $_POST["id"] . "'");
                if (is_array($productos)) {
                    foreach ($productos as $pr) {
                        $objconn->consulta_simple("UPDATE producto_venta set estado_fila = 2 where  id = '" . $pr["id"] . "'");
                    }
                }
               
                echo json_encode(1);
                break;
        case 'addProdNota':
            //$objpord_venta->setVar('id', NULL);
            //echo $_POST['total'];
            $objpord_venta->setVar('id_venta', $_POST['idVenta']);
            $objpord_venta->setVar('id_producto', $_POST['idProd']);
            $objpord_venta->setVar('precio', $_POST['precio']);
            $objpord_venta->setVar('cantidad', $_POST['cantidad']);
            $objpord_venta->setVar('total', $_POST['total']);
            $objpord_venta->setVar('estado_fila', "1");
            $objpord_venta->setVar('opc', "p");
            $objpord_venta->setVar('prod_secundario', "0");
           echo json_encode($objpord_venta->insertDB());
             break;
        case 'anulaventa2':
            $objventa = new venta();
            $objventa->setVar('id', $_POST['id']);
            $objventa->getDB();

            $objconn = new caja();
            // echo "Select * from producto_venta where id_venta = '".$_POST["id"];
            $productos = $objconn->consulta_matriz("Select * from producto_venta where estado_fila= 1 AND id_venta = '" . $_POST["id"] . "'");
            $servicios = $objconn->consulta_matriz("Select * from servicio_venta where id_venta = '" . $_POST["id"] . "'");

            if (is_array($productos)) {
                foreach ($productos as $pr) {
                    $objconn->consulta_simple("Insert into movimiento_producto values(NULL,'" . $pr["id_producto"] . "','1','" . $pr["cantidad"] . "',NULL,'VENTA','" . $objventa->getIdUsuario() . "','" . $objventa->getIdTurno() . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','1','','','1',NULL)");
                    $objconn->consulta_simple("UPDATE producto_venta where estado_fila = 2 id = '" . $pr["id"] . "'");
                }
            }

            if (is_array($servicios)) {
                foreach ($servicios as $ser) {
                    $proxser = $objconn->consulta_matriz("Select * from servicio_producto where id_servicio = '" . $ser["id_servicio"] . "'");
                    if (is_array($proxser)) {
                        foreach ($proxser as $ps) {
                            $objconn->consulta_simple("Insert into movimiento_producto values(NULL,'" . $ps["id_producto"] . "','" . $ps["id_almacen"] . "','" . $ps["cantidad"] . "',NULL,'VENTA','" . $objventa->getIdUsuario() . "','" . $objventa->getIdTurno() . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','1','','','0',NULL)");
                        }
                    }
                    $objconn->consulta_simple("UPDATE servicio_venta set estado_fila = 2  where id = '" . $ser["id"] . "'");
                }
            }


            $objconn->consulta_simple("UPDATE venta set estado_fila = 2 where id = '" . $_POST["id"] . "'");

            echo json_encode(1);
            break;



        case 'del':
            $objventa->setVar('id', $_POST['id']);
            echo json_encode($objventa->deleteDB());
            break;

        case 'get':
            $res = $objventa->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_turno'] = $objturno->searchDB($res[0]['id_turno'], 'id', 1);
                $res[0]['id_turno'] = $res[0]['id_turno'][0];
                $res[0]['id_usuario'] = $objusuario->searchDB($res[0]['id_usuario'], 'id', 1);
                $res[0]['id_usuario'] = $res[0]['id_usuario'][0];
                $res[0]['id_caja'] = $objcaja->searchDB($res[0]['id_caja'], 'id', 1);
                $res[0]['id_caja'] = $res[0]['id_caja'][0];
                $res[0]['id_cliente'] = $objcliente->searchDB($res[0]['id_cliente'], 'id', 1);
                $res[0]['id_cliente'] = $res[0]['id_cliente'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objventa->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_turno'] = $objturno->searchDB($act['id_turno'], 'id', 1);
                    $act['id_turno'] = $act['id_turno'][0];
                    $act['id_usuario'] = $objusuario->searchDB($act['id_usuario'], 'id', 1);
                    $act['id_usuario'] = $act['id_usuario'][0];
                    $act['id_caja'] = $objcaja->searchDB($act['id_caja'], 'id', 1);
                    $act['id_caja'] = $act['id_caja'][0];
                    $act['id_cliente'] = $objcliente->searchDB($act['id_cliente'], 'id', 1);
                    $act['id_cliente'] = $act['id_cliente'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objventa->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_turno'] = $objturno->searchDB($act['id_turno'], 'id', 1);
                    $act['id_turno'] = $act['id_turno'][0];
                    $act['id_usuario'] = $objusuario->searchDB($act['id_usuario'], 'id', 1);
                    $act['id_usuario'] = $act['id_usuario'][0];
                    $act['id_caja'] = $objcaja->searchDB($act['id_caja'], 'id', 1);
                    $act['id_caja'] = $act['id_caja'][0];
                    $act['id_cliente'] = $objcliente->searchDB($act['id_cliente'], 'id', 1);
                    $act['id_cliente'] = $act['id_cliente'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'convertir_comprobante_subir':
            //Obtenemos serie de boleta y factura

            /*
            $series = $objventa->consulta_arreglo("Select * from configuracion where id = 1");

            switch($_POST["tipo_comprobante"]){

                case 1:
                    $objventa->consulta_simple("INSERT into boleta values(NULL,'".$_POST['id']."',NULL,'".$series["serie_boleta"]."','1')");
                    $tipo = 'B';
                break;

                case 2:
                    $objventa->consulta_simple("INSERT into factura values(NULL,'".$_POST['id']."',NULL,'".$series["serie_factura"]."','1')");
                    $tipo = 'F';
                break;

            }
            */

            $res = $objventa->consulta_arreglo("SELECT * FROM venta WHERE id = " . $_POST["id"]);

            $config = $objventa->consulta_arreglo("Select * from configuracion");

            $cliente = $objventa->consulta_arreglo("SELECT * FROM cliente WHERE id =" . $res['id_cliente']);

            if ($_POST["tipo_comprobante"] == 1) {

                $serie = $config["serie_boleta"];
                $boleta = $objventa->consulta_arreglo("SELECT * FROM boleta order by id desc limit 1");

                $numero = $boleta['id'] + 1;
                $tipDoc = 2;
                if (empty($cliente["documento"])) {
                    $tipoAdq = '-';
                    $tipoDoc = '-';
                    $tipoNom = '----';
                    $tipoEmail = '';
                } else {
                    $tipoAdq = '1';
                    $tipoDoc = $cliente["documento"];
                    $tipoNom = $cliente["nombre"];
                    $tipoEmail = $cliente["correo"];
                }
            } else {
                $serie = $config["serie_factura"];
                $factura = $objventa->consulta_arreglo("SELECT * from factura order by id desc limit 1");
                $numero = $factura['id'] + 1;

                $tipoAdq = '6';
                $tipDoc = 1;

                $tipoDoc = $cliente["documento"];
                $tipoNom = $cliente["nombre"];
                $tipoEmail = $cliente["correo"];
            }

            $cabecera = array();


            if (is_array($res)) {

                $cabecera["operacion"] = "generar_comprobante";
                $cabecera["tipo_de_comprobante"] = $tipDoc;
                $cabecera["serie"] = $serie;
                $cabecera["numero"] = str_pad($numero, 8, "0", STR_PAD_LEFT);
                $cabecera["sunat_transaction"] = 1;
                $cabecera["cliente_tipo_de_documento"] = $tipoAdq;
                $cabecera["cliente_numero_de_documento"] = $tipoDoc;
                $cabecera["cliente_denominacion"] = $tipoNom;
                $cabecera["cliente_email"] = $tipoEmail;
                $cabecera["cliente_email_1"] = "";
                $cabecera["cliente_email_2"] = "";
                $cabecera["fecha_de_emision"] = date("d-m-Y");
                $cabecera["fecha_de_vencimiento"] = "";
                $cabecera["moneda"] = 1;
                $cabecera["tipo_de_cambio"] = "";
                $cabecera["porcentaje_de_igv"] = "18.00";
                $cabecera["descuento_global"] =  floatval(0.00);
                $cabecera["total_descuento"] =  floatval(0.00);
                $cabecera["total_anticipo"] = "";
                $cabecera["total_anticipo"] = "";
                $cabecera["total_gravada"] = number_format(floatval($res['subtotal']), 2);
                $cabecera["total_inafecta"] = "";
                $cabecera["total_exonerada"] = "";
                $cabecera["total_igv"] = number_format(floatval($res['total_impuestos']), 2);
                $cabecera["total_gratuita"] = "";
                $cabecera["total_otros_cargos"] = "";
                $cabecera["total"] = number_format(floatval($res['total']), 2);
                $cabecera["percepcion_tipo"] = "";
                $cabecera["percepcion_base_imponible"] = "";
                $cabecera["total_percepcion"] = "";
                $cabecera["total_incluido_percepcion"] = "";
                $cabecera["detraccion"] = "false";
                $cabecera["observaciones"] = "";
                $cabecera["documento_que_se_modifica_tipo"] = "";
                $cabecera["documento_que_se_modifica_serie"] = "";
                $cabecera["documento_que_se_modifica_numero"] = "";
                $cabecera["tipo_de_nota_de_credito"] = "";
                $cabecera["tipo_de_nota_de_debito"] = "";
                $cabecera["enviar_automaticamente_a_la_sunat"] = "true";
                $cabecera["enviar_automaticamente_al_cliente"] = "true";
                $cabecera["codigo_unico"] = "";
                $cabecera["condiciones_de_pago"] = "";
                $cabecera["medio_de_pago"] = "";
                $cabecera["placa_vehiculo"] = "";
                $cabecera["orden_compra_servicio"] = "";
                $cabecera["tabla_personalizada_codigo"] = "";
                $cabecera["formato_de_pdf"] = "TICKET";

                $items = array();

                $detalles = $objventa->consulta_matriz("SELECT pv.id, nombre, cantidad, precio, pv.opc FROM producto_venta pv, producto p WHERE pv.estado_fila= 1 AND  pv.id_venta = " . $res["id"] . " AND pv.id_producto = p.id UNION SELECT s.id, nombre, cantidad, precio, sv.opc FROM servicio_venta sv, servicio s WHERE sv.id_venta = " . $res["id"] . " AND sv.id_servicio = s.id");

                for ($i = 0; $i < count($detalles); $i++) {
                    $item = array();
                    if ($detalles[$i]['opc'] == 'p')
                        $unidadMedida = 'NIU';
                    else
                        $unidadMedida = 'ZZ';

                    $valor_unitario = floatval($detalles[$i]['precio'] / 1.18);
                    $subtotal = floatval($valor_unitario * $detalles[$i]['cantidad']);

                    $item["unidad_de_medida"] = $unidadMedida;
                    $item["codigo"] = $detalles[$i]['id'];
                    $item["descripcion"] =  $detalles[$i]['nombre'];
                    $item["cantidad"] = $detalles[$i]['cantidad'];
                    $item["valor_unitario"] = number_format($valor_unitario, 2);
                    $item["precio_unitario"] = $detalles[$i]['precio'];
                    $item["descuento"] = "";
                    $item["subtotal"] = number_format($subtotal, 2);
                    $item["tipo_de_igv"] = '1';
                    $item["igv"] = number_format(floatval((($detalles[$i]['precio'] - $valor_unitario) * $detalles[$i]['cantidad'])), 2);
                    $item["total"] = number_format(floatval(($detalles[$i]['precio'] * $detalles[$i]['cantidad'])), 2);
                    $item["anticipo_regularizacion"] = false;
                    $item["anticipo_documento_serie"] = "";

                    $items[] = $item;
                }
       
                $cabecera["items"] = $items;

                // echo print_r($cabecera);
                $data_json = json_encode($cabecera);

                $ruta = $config["ruta"];
                $token = $config["token"];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ruta);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Authorization: Token token="' . $token . '"',
                        'Content-Type: application/json',
                    )
                );
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $respuesta  = curl_exec($ch);
                //echo "Curl ".$respuesta;
                if (intval(curl_errno($ch)) === 0) {
                    curl_close($ch);
                    //Verificamos respuesta
                    //print_r($respuesta);
                    $leer_respuesta = json_decode($respuesta, true);
                    if (isset($leer_respuesta['errors'])) {
                        $qr = "Insert into comprobante_hash values('" . $numero . "','NO',' ',' ','" . $leer_respuesta['errors'] . "')";
                        $objventa->consulta_simple($qr);
                        //Mostramos errores
                        //echo json_encode($leer_respuesta['errors']);
                    } else {
                        $aceptada = "NO";
                        if (boolval($leer_respuesta["aceptada_por_sunat"])) {
                            $aceptada = "SI";
                        }
                        $qr = "Insert into comprobante_hash values('" . $numero . "','" . $aceptada . "','" . $leer_respuesta["codigo_hash"] . "','" . $leer_respuesta["cadena_para_codigo_qr"] . "','" . $leer_respuesta['sunat_description'] . "')";
                        $objventa->consulta_simple($qr);

                        $series = $objventa->consulta_arreglo("Select * from configuracion where id = 1");

                        switch ($_POST["tipo_comprobante"]) {

                            case 1:
                                $objventa->consulta_simple("INSERT into boleta values(" . $numero . ",'" . $_POST['id'] . "',NULL,'" . $series["serie_boleta"] . "','1')");
                                $tipo = 'B';
                                break;

                            case 2:
                                $objventa->consulta_simple("INSERT into factura values(" . $numero . ",'" . $_POST['id'] . "',NULL,'" . $series["serie_factura"] . "','1')");
                                $tipo = 'F';
                                break;
                        }
                        //Mostramos Respuesta
                        //echo $aceptada;
                    }
                } else {
                    curl_close($ch);
                    $qr = "Insert into comprobante_hash values('" . $numero . "','NE',' ',' ','')";
                    $objventa->consulta_simple($qr);
                    //Mostramos errores
                    echo "NE";
                }

                echo  $respuesta;
            } else {
                echo json_encode(0);
            }


            break;
        case 'cargar_sunat':

            $res = $objventa->consulta_arreglo("SELECT * FROM venta WHERE id = " . $_POST["id"]);

            $config = $objventa->consulta_arreglo("Select * from configuracion");

            $cliente = $objventa->consulta_arreglo("SELECT * FROM cliente WHERE id =" . $res['id_cliente']);

            if ($res['tipo_comprobante'] == 1) {
                $serie = $config["serie_boleta"];
                $boleta = $objventa->consulta_arreglo("Select * from boleta WHERE id_venta =" . $res["id"]);
                $numero = $boleta['id'];
                $tipDoc = 2;
                if (empty($cliente["documento"])) {
                    $tipoAdq = '-';
                    $tipoDoc = '-';
                    $tipoNom = '-';
                    $tipoEmail = '';
                } else {
                    $tipoAdq = '1';
                    $tipoDoc = $cliente["documento"];
                    $tipoNom = $cliente["nombre"];
                    $tipoEmail = $cliente["correo"];
                }
            } else {
                $serie = $config["serie_factura"];
                $factura = $objventa->consulta_arreglo("Select * from factura WHERE id_venta =" . $res["id"]);
                $numero = $factura['id'];

                $tipoAdq = '6';
                $tipDoc = 1;

                $tipoDoc = $cliente["documento"];
                $tipoNom = $cliente["nombre"];
                $tipoEmail = $cliente["correo"];
            }

            $cabecera = array();


            if (is_array($res)) {

                $cabecera["operacion"] = "generar_comprobante";
                $cabecera["tipo_de_comprobante"] = $tipDoc;
                $cabecera["serie"] = $serie;
                $cabecera["numero"] = str_pad($numero, 8, "0", STR_PAD_LEFT);
                $cabecera["sunat_transaction"] = 1;
                $cabecera["cliente_tipo_de_documento"] = $tipoAdq;
                $cabecera["cliente_numero_de_documento"] = $tipoDoc;
                $cabecera["cliente_denominacion"] = $tipoNom;
                $cabecera["cliente_email"] = $tipoEmail;
                $cabecera["cliente_email_1"] = "";
                $cabecera["cliente_email_2"] = "";
                $cabecera["fecha_de_emision"] = date("d-m-Y");
                $cabecera["fecha_de_vencimiento"] = "";
                $cabecera["moneda"] = 1;
                $cabecera["tipo_de_cambio"] = "";
                $cabecera["porcentaje_de_igv"] = "18.00";
                $cabecera["descuento_global"] =  floatval(0.00);
                $cabecera["total_descuento"] =  floatval(0.00);
                $cabecera["total_anticipo"] = "";
                $cabecera["total_anticipo"] = "";
                $cabecera["total_gravada"] = number_format(floatval($res['subtotal']), 2);
                $cabecera["total_inafecta"] = "";
                $cabecera["total_exonerada"] = "";
                $cabecera["total_igv"] = number_format(floatval($res['total_impuestos']), 2);
                $cabecera["total_gratuita"] = "";
                $cabecera["total_otros_cargos"] = "";
                $cabecera["total"] = number_format(floatval($res['total']), 2);
                $cabecera["percepcion_tipo"] = "";
                $cabecera["percepcion_base_imponible"] = "";
                $cabecera["total_percepcion"] = "";
                $cabecera["total_incluido_percepcion"] = "";
                $cabecera["detraccion"] = "false";
                $cabecera["observaciones"] = "";
                $cabecera["documento_que_se_modifica_tipo"] = "";
                $cabecera["documento_que_se_modifica_serie"] = "";
                $cabecera["documento_que_se_modifica_numero"] = "";
                $cabecera["tipo_de_nota_de_credito"] = "";
                $cabecera["tipo_de_nota_de_debito"] = "";
                $cabecera["enviar_automaticamente_a_la_sunat"] = "true";
                $cabecera["enviar_automaticamente_al_cliente"] = "true";
                $cabecera["codigo_unico"] = "";
                $cabecera["condiciones_de_pago"] = "";
                $cabecera["medio_de_pago"] = "";
                $cabecera["placa_vehiculo"] = "";
                $cabecera["orden_compra_servicio"] = "";
                $cabecera["tabla_personalizada_codigo"] = "";
                $cabecera["formato_de_pdf"] = "TICKET";

                $items = array();

                $detalles = $objventa->consulta_matriz("SELECT pv.id, nombre, cantidad, precio, pv.opc FROM producto_venta pv, producto p WHERE pv.estado_fila= 1 AND pv.id_venta = " . $res["id"] . " AND pv.id_producto = p.id UNION SELECT s.id, nombre, cantidad, precio, sv.opc FROM servicio_venta sv, servicio s WHERE sv.id_venta = " . $res["id"] . " AND sv.id_servicio = s.id");

                for ($i = 0; $i < count($detalles); $i++) {
                    $item = array();
                    if ($detalles[$i]['opc'] == 'p')
                        $unidadMedida = 'NIU';
                    else
                        $unidadMedida = 'ZZ';

                    $valor_unitario = floatval($detalles[$i]['precio'] / 1.18);
                    $subtotal = floatval($valor_unitario * $detalles[$i]['cantidad']);

                    $item["unidad_de_medida"] = $unidadMedida;
                    $item["codigo"] = $detalles[$i]['id'];
                    $item["descripcion"] =  $detalles[$i]['nombre'];
                    $item["cantidad"] = $detalles[$i]['cantidad'];
                    $item["valor_unitario"] = number_format($valor_unitario, 2);
                    $item["precio_unitario"] = $detalles[$i]['precio'];
                    $item["descuento"] = "";
                    $item["subtotal"] = number_format($subtotal, 2);
                    $item["tipo_de_igv"] = '1';
                    $item["igv"] = number_format(floatval((($detalles[$i]['precio'] - $valor_unitario) * $detalles[$i]['cantidad'])), 2);
                    $item["total"] = number_format(floatval(($detalles[$i]['precio'] * $detalles[$i]['cantidad'])), 2);
                    $item["anticipo_regularizacion"] = false;
                    $item["anticipo_documento_serie"] = "";

                    $items[] = $item;
                }
   
    
                $cabecera["items"] = $items;

                //echo print_r($cabecera);
                $data_json = json_encode($cabecera);

                $ruta = $config["ruta"];
                $token = $config["token"];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ruta);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Authorization: Token token="' . $token . '"',
                        'Content-Type: application/json',
                    )
                );
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $respuesta  = curl_exec($ch);
                // echo "Curl ".$respuesta;
                if (intval(curl_errno($ch)) === 0) {
                    curl_close($ch);
                    //Verificamos respuesta
                    //print_r($respuesta);
                    $leer_respuesta = json_decode($respuesta, true);
                    if (isset($leer_respuesta['errors'])) {
                        $qr = "Insert into comprobante_hash values('" . $numero . "','NO',' ',' ','" . $leer_respuesta['errors'] . "')";
                        $objventa->consulta_simple($qr);
                        //Mostramos errores
                        // echo json_encode($leer_respuesta['errors']);
                    } else {
                        $aceptada = "NO";
                        if (boolval($leer_respuesta["aceptada_por_sunat"])) {
                            $aceptada = "SI";
                        }
                        $qr = "Insert into comprobante_hash values('" . $numero . "','" . $aceptada . "','" . $leer_respuesta["codigo_hash"] . "','" . $leer_respuesta["cadena_para_codigo_qr"] . "','" . $leer_respuesta['sunat_description'] . "')";
                        $objventa->consulta_simple($qr);
                        //Mostramos Respuesta
                        // echo $aceptada;
                    }
                } else {
                    curl_close($ch);
                    $qr = "Insert into comprobante_hash values('" . $numero . "','NE',' ',' ','')";
                    $objventa->consulta_simple($qr);
                    //Mostramos errores
                    echo "NE";
                }

                echo $respuesta;
            } else {
                echo json_encode(0);
            }
            break;

        case 'buscarCliente':
            $res = $objventa->consulta_arreglo("SELECT * FROM venta WHERE id = " . $_POST["id"]);

            $cliente = $objventa->consulta_arreglo("SELECT * FROM cliente WHERE id =" . $res['id_cliente']);

            if (is_array($cliente)) {
                echo json_encode($cliente);
            } else {
                echo json_encode(0);
            }

            break;

        case 'CambiaCredito':

            $vmp = $objventa->consulta_matriz("SELECT * FROM venta_medio_pago WHERE id_venta = " . $_POST['id']);

            $objventa->setVar('id', $_POST['id']);
            $objventa->getDB();

            if (count($vmp) == 1) {
                if ($vmp[0]["medio"] == "DESCUENTO") {
                    $venta =  $objventa->consulta_arreglo("SELECT * FROM venta WHERE id = " . $_POST['id']);

                    $objventa_medio_pago = new venta_medio_pago();
                    $objventa_medio_pago->setVar('id_venta', $_POST['id']);
                    $objventa_medio_pago->setVar('medio', "CREDITO");
                    $objventa_medio_pago->setVar('monto', $venta['total'] - $vmp[0]["monto"]);
                    $objventa_medio_pago->setVar('moneda', 'PEN');
                    $objventa_medio_pago->setVar('vuelto', 0);
                    $objventa_medio_pago->setVar('estado_fila', "1");
                    $idi = $objventa_medio_pago->insertDB();
                    $monto = $venta['total'] - $vmp[0]["monto"];
                    $series = $objventa->consulta_arreglo("Select * from configuracion where id = 1");


                    $objventa->consulta_simple("Insert into movimiento_caja values(NULL,{$objventa->getIdCaja()},{$monto},'SELL|PEN|CREDITO|{$idi}','" . date("Y-m-d H:i:s") . "','" . $series['fecha_cierre'] . "','{$objventa->getIdTurno()}','{$objventa->getIdUsuario()}','1')");
                }
            }
            $qr = "UPDATE venta SET tipo_comprobante = -1 WHERE id = '" . $_POST['id'] . "'";

            echo $objventa->consulta_simple($qr);

            break;

        case 'verificar':

            $qrBol = $objventa->consulta_matriz("SELECT id FROM boleta WHERE id_venta = " . $_POST['id']);

            if (is_array($qrBol)) {
                echo json_encode(0);
            } else {
                $qrFact = $objventa->consulta_matriz("SELECT id FROM factura WHERE id_venta = " . $_POST['id']);

                if (is_array($qrFact)) {
                    echo json_encode(0);
                } else {
                    echo json_encode(1);
                }
            }



            $qr = $objventa->consulta_arreglo("SELECT b.id FROM boleta b WHERE b.id_venta = '" . $_POST['id'] . "' AND b.id_venta = v.id
                    UNION
                    SELECT f.id FROM factura f WHERE f.id_venta = '" . $_POST['id'] . "' AND f.id_venta");

            echo $qr["id"];


            break;

        case 'deleteComprobantes':

            if ($_POST["comprobante"] == 2) {
                $res = $objventa->consulta_simple("DELETE FROM factura WHERE id_venta = '" . $_POST["id"] . "'");
            } else {
                $res = $objventa->consulta_simple("DELETE FROM boleta WHERE id_venta = '" . $_POST["id"] . "'");
            }

            break;

        case 'foul':

            if ($_POST["comprobante"] == 2) {
                $res = $objventa->consulta_simple("DELETE FROM factura WHERE id_venta = '" . $_POST["id"] . "'");
            } else {
                $res = $objventa->consulta_simple("DELETE FROM boleta WHERE id_venta = '" . $_POST["id"] . "'");
            }

            $res = $objventa->consulta_simple("DELETE FROM entregas WHERE id_venta = '" . $_POST["id"] . "'");

            $objventa = new venta();
            $objventa->setVar('id', $_POST['id']);
            $objventa->getDB();

            $medio_de_pago = $objventa->consulta_matriz("SELECT * FROM venta_medio_pago WHERE id_venta = " . $_POST['id']);

            for ($i = 0; $i < count($medio_de_pago); $i++) {

                $monto = $medio_de_pago[$i]["monto"] - $medio_de_pago[$i]["vuelto"];

                $objventa->consulta_simple("Insert into movimiento_caja values(NULL,'" . $objventa->getIdCaja() . "','-" . $monto . "','SELL|PEN|" . $medio_de_pago[$i]["medio"] . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','" . $objventa->getIdTurno() . "','" . $objventa->getIdUsuario() . "','1')");
            }

            $productos = $objventa->consulta_matriz("Select * from producto_venta where estado_fila= 1 AND id_venta = '" . $_POST["id"] . "'");
            $servicios = $objventa->consulta_matriz("Select * from servicio_venta where id_venta = '" . $_POST["id"] . "'");

            if (is_array($productos)) {
                foreach ($productos as $pr) {
                    $objventa->consulta_simple("Insert into movimiento_producto values(NULL,'" . $pr["id_producto"] . "','1','" . $pr["cantidad"] . "',NULL,'VENTA','" . $objventa->getIdUsuario() . "','" . $objventa->getIdTurno() . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','1','','','1',NULL)");
                    $objventa->consulta_simple("UPDATE producto_venta where estado_fila = 2 id = '" . $pr["id"] . "'");
                }
            }

            if (is_array($servicios)) {
                foreach ($servicios as $ser) {
                    $proxser = $objventa->consulta_matriz("Select * from servicio_producto where id_servicio = '" . $ser["id_servicio"] . "'");
                    if (is_array($proxser)) {
                        foreach ($proxser as $ps) {
                            $objventa->consulta_simple("Insert into movimiento_producto values(NULL,'" . $ps["id_producto"] . "','" . $ps["id_almacen"] . "','" . $ps["cantidad"] . "',NULL,'VENTA','" . $objventa->getIdUsuario() . "','" . $objventa->getIdTurno() . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','1','','','0',NULL)");
                        }
                    }
                    $objventa->consulta_simple("UPDATE servicio_venta set estado_fila = 2  where id = '" . $ser["id"] . "'");
                }
            }

            $objventa->consulta_simple("UPDATE venta_medio_pago SET estado_fila = 2 WHERE id_venta = " . $_POST['id']);

            $objventa->consulta_simple("DELETE FROM ventas_notas WHERE id_venta = " . $_POST['id']);

            $ya = $objventa->consulta_simple("UPDATE venta SET estado_fila = 2 WHERE id = " . $_POST['id']);

            echo $ya;
            break;

        case 'RestaConsumo':

            $objventa = new venta();
            $objventa->setVar('id', $_POST['id']);
            $objventa->getDB();

            $getConsumo = $objventa->consulta_arreglo("
                SELECT * FROM cliente_credito WHERE IdCliente = " . $objventa->getIdCliente() . " AND Estado = 1");
            $consumo = $getConsumo["Consumo"] - $objventa->getTotal();

            $objventa->consulta_simple("
                        UPDATE  cliente_credito  SET Consumo = " . $consumo . " WHERE IdCliente = " . $objventa->getIdCliente() . " AND Estado = 1");
            break;

        case 'AnulaCredito':

            $qrBol = $objventa->consulta_matriz("SELECT id FROM boleta WHERE id_venta = " . $_POST['id']);

            if (is_array($qrBol)) {
                $res = $objventa->consulta_simple("UPDATE boleta SET estado_fila = 2 WHERE id_venta = '" . $_POST["id"] . "'");
            } else {
                $qrFact = $objventa->consulta_matriz("SELECT id FROM factura WHERE id_venta = " . $_POST['id']);

                if (is_array($qrFact)) {
                    $res = $objventa->consulta_simple("UPDATE factura SET estado_fila = 2 WHERE id_venta = '" . $_POST["id"] . "'");
                }
            }

            $objventa = new venta();
            $objventa->setVar('id', $_POST['id']);
            $objventa->getDB();

            $medio_de_pago = $objventa->consulta_matriz("SELECT * FROM venta_medio_pago WHERE id_venta = " . $_POST['id']);


            for ($i = 0; $i < count($medio_de_pago); $i++) {

                $objventa->consulta_simple("Insert into movimiento_caja values(NULL,'" . $objventa->getIdCaja() . "','-" . $medio_de_pago[$i]["monto"] . "','EXT|PEN|" . $medio_de_pago[$i]["medio"] . "_COBRO','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','" . $objventa->getIdTurno() . "','" . $objventa->getIdUsuario() . "','1')");
            }

            $productos = $objventa->consulta_matriz("Select * from producto_venta where estado_fila= 1 AND id_venta = '" . $_POST["id"] . "'");
            $servicios = $objventa->consulta_matriz("Select * from servicio_venta where id_venta = '" . $_POST["id"] . "'");

            if (is_array($productos)) {
                foreach ($productos as $pr) {
                    $objventa->consulta_simple("Insert into movimiento_producto values(NULL,'" . $pr["id_producto"] . "','1','" . $pr["cantidad"] . "',NULL,'VENTA','" . $objventa->getIdUsuario() . "','" . $objventa->getIdTurno() . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','1','','','1',NULL)");
                    $objventa->consulta_simple("UPDATE producto_venta where estado_fila = 2 id = '" . $pr["id"] . "'");
                }
            }

            if (is_array($servicios)) {
                foreach ($servicios as $ser) {
                    $proxser = $objventa->consulta_matriz("Select * from servicio_producto where id_servicio = '" . $ser["id_servicio"] . "'");
                    if (is_array($proxser)) {
                        foreach ($proxser as $ps) {
                            $objventa->consulta_simple("Insert into movimiento_producto values(NULL,'" . $ps["id_producto"] . "','" . $ps["id_almacen"] . "','" . $ps["cantidad"] . "',NULL,'VENTA','" . $objventa->getIdUsuario() . "','" . $objventa->getIdTurno() . "','" . date("Y-m-d H:i:s") . "','" . $objventa->getFechaCierre() . "','1','','','0',NULL)");
                        }
                    }
                    $objventa->consulta_simple("UPDATE servicio_venta set estado_fila = 2  where id = '" . $ser["id"] . "'");
                }
            }

            $objventa->consulta_simple("UPDATE venta_medio_pago SET estado_fila = 2 WHERE id_venta = " . $_POST['id']);
            $ya = $objventa->consulta_simple("UPDATE venta SET estado_fila = 2 WHERE id = " . $_POST['id']);

            $res = $objventa->consulta_arreglo("SELECT * FROM venta WHERE id = " . $_POST["id"]);
            $respuesta = 1;
            if (is_array($qrBol) || is_array($qrFact)) {

                if (is_array($qrBol))
                    $tipo = '2';
                else
                    $tipo = '1';

                $anul = $objventa->consulta_arreglo("
                    SELECT * FROM boleta where id_venta = " . $_POST["id"] . "
                    UNION
                    SELECT * FROM factura where id_venta = " . $_POST["id"] . "");

                $cabecera = array();
                $cabecera["operacion"] = "generar_anulacion";
                $cabecera["tipo_de_comprobante"] = $tipo;
                $cabecera["serie"] = $anul["serie"];
                $cabecera["numero"] = $anul["id"];
                $cabecera["motivo"] = "ERROR DE SISTEMA";
                // print_r($cabecera);
                $data_json = json_encode($cabecera);

                $config = $objventa->consulta_arreglo("Select * from configuracion");
                $ruta = $config["ruta"];
                $token = $config["token"];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ruta);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Authorization: Token token="' . $token . '"',
                        'Content-Type: application/json',
                    )
                );
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $respuesta  = curl_exec($ch);
            }

            echo $respuesta;
            break;

        case 'reporte':

            $fechaInicio = $_POST['fecha_inicio'];
            $fechaFin = $_POST['fecha_fin'];

            $objs = $conn->consulta_matriz("SELECT pv.id_producto AS id_producto, p.nombre as nombre, p.precio_compra as precio_compra,
            p.precio_venta as precio_venta,SUM(pv.cantidad) AS cantidad, SUM(pv.total) AS totalventa, v.fecha_cierre
            FROM producto_venta pv
            INNER JOIN venta v ON v.id = pv.id_venta 
            INNER JOIN producto p ON pv.id_producto = p.id
            WHERE v.estado_fila = 1 AND v.fecha_cierre BETWEEN '{$fechaInicio}' AND '{$fechaFin}'
            GROUP BY pv.id_producto
            ORDER BY totalventa desc");

            echo json_encode($objs);

            break;


        case 'reporte_venta_totales':

            $fechaInicio = $_POST['fecha_inicio'];
            $fechaFin = $_POST['fecha_fin'];

            $objs = $conn->consulta_matriz("SELECT pv.id_producto AS id_producto, p.nombre as nombre, p.precio_compra as precio_compra,
            p.precio_venta as precio_venta,SUM(pv.cantidad) AS cantidad, SUM(pv.total) AS totalventa, v.fecha_cierre
            FROM producto_venta pv
            INNER JOIN venta v ON v.id = pv.id_venta 
            INNER JOIN producto p ON pv.id_producto = p.id
            WHERE v.estado_fila = 1 AND v.fecha_cierre BETWEEN '{$fechaInicio}' AND '{$fechaFin}'
            GROUP BY pv.id_producto
            ORDER BY totalventa desc");

            echo json_encode($objs);

            break;

        case 'cliente_nota':
            $res = $objventa->consulta_arreglo("SELECT * FROM nota_cliente WHERE id_venta = " . $_POST['id_venta']);

            // echo "SELECT * FROM nota_cliente WHERE id_venta = ".$_POST['id_venta'];

            if (!is_array($res)) {
                $objventa->consulta_simple("INSERT INTO nota_cliente(id, id_venta, cliente) VALUES (NULL, " . $_POST['id_venta'] . ",'" . $_POST['cliente'] . "')");
            } else {
                $objventa->consulta_simple("UPDATE nota_cliente SET cliente = '" . $_POST['cliente'] . "' WHERE id_venta = " . $_POST["id_venta"]);
            }
            break;

        case 'ventas_cliente_credito':
            $res = $objventa->consulta_matriz("SELECT v.*, (SELECT COUNT(id) from boleta where id_venta = v.id) as 'boleta', (SELECT COUNT(id) from factura where id_venta = v.id) as 'factura', (SELECT SUM(monto) from venta_medio_pago where id_venta = v.id and medio <> 'CREDITO') AS 'Pagado'
                                                FROM venta v
                                                where v.id_cliente = {$_POST['id_cliente']} AND v.tipo_comprobante = -1 AND estado_fila = 1");
            echo json_encode($res);
            break;

        case 'DetallesUnion':
            $res = $objventa->consulta_matriz("SELECT pv.id, nombre, cantidad, precio, pv.opc FROM producto_venta pv, producto p WHERE pv.estado_fila= 1 AND pv.id_venta = '" . $_POST["id"] . "' AND pv.id_producto = p.id UNION SELECT s.id, nombre, cantidad, precio, sv.opc FROM servicio_venta sv, servicio s WHERE sv.id_venta = '" . $_POST["id"] . "' AND sv.id_servicio = s.id");
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'Free':
            $config = $objventa->consulta_arreglo("Select * from configuracion");

            $cliente["id"] = 0;
            if ($_POST["doc"] != "") {
                $cliente = $objventa->consulta_arreglo("SELECT * FROM cliente WHERE documento =" . $_POST['doc']);
                if ($cliente == 0) {
                    if ($_POST["tipo"] == 1) $tipo_cliente = 1;
                    else $tipo_cliente = 2;

                    $objventa->consulta_simple("INSERT INTO cliente(id, nombre, documento, direccion, correo, tipo_cliente, fecha_nacimiento, estado_fila) VALUES ('','" . $_POST['nombre'] . "','" . $_POST['doc'] . "','" . $_POST['direccion'] . "','" . $_POST['correo'] . "','" . $tipo_cliente . "',NULL,'1')");

                    $cliente = $objventa->consulta_arreglo("SELECT * FROM cliente WHERE documento =" . $_POST['doc']);
                }
            }

            $venta = $objventa->consulta_simple("INSERT INTO venta(id, subtotal, total_impuestos, total, tipo_comprobante, fecha_hora, fecha_cierre,
                id_turno, id_usuario, id_caja, id_cliente, estado_fila) VALUES ('',
                '" . $_POST["SubTotal"] . "',
                '" . $_POST['IGV'] . "',
                '" . $_POST['Total'] . "',
                '" . $_POST["tipo"] . "',
                '" . date("Y-m-d H:i:s") . "',
                '" . $config["fecha_cierre"] . "',
                '1',
                '" . $_POST['idUsuario'] . "',
                '" . $_POST["idCaja"] . "',
                '" . $cliente["id"] . "',
                '10')");


            $ultima = $objventa->consulta_arreglo("SELECT * FROM venta ORDER BY id DESC limit 1");

            if ($_POST["tipo"] == 1) {

                $serie = $config["serie_boleta"];
                $boleta = $objventa->consulta_arreglo("SELECT * FROM boleta order by id desc limit 1");
                $numero = $boleta['id'] + 1;
                $tipDoc = 2;
                if (empty($_POST["doc"])) {
                    $tipoAdq = '-';
                    $tipoDoc = '-';
                    $tipoNom = '----';
                    $tipoEmail = '';
                } else {
                    $tipoAdq = '1';
                    $tipoDoc = $_POST["doc"];
                    $tipoNom = $_POST["nombre"];
                    $tipoEmail = $_POST["correo"];
                }

                $objventa->consulta_simple("INSERT INTO boleta (id, id_venta, token, serie, estado_fila) VALUES ('$numero', '" . $ultima['id'] . "','','$serie','1')");
                // echo "INSERT INTO boleta (id, id_venta, token, serie, estado_fila) VALUES ('', '0','',$serie','1')";
            } else {
                $serie = $config["serie_factura"];
                $factura = $objventa->consulta_arreglo("SELECT * from factura order by id desc limit 1");
                $numero = $factura['id'] + 1;

                $tipoAdq = '6';
                $tipDoc = 1;


                $tipoDoc = $_POST["doc"];
                $tipoNom = $_POST["nombre"];
                $tipoEmail = $_POST["correo"];

                // echo "INSERT INTO factura (id, id_venta, token, serie, estado_fila) VALUES ('$numero', '".$ultima['id']."','','$serie','1')";

                $objventa->consulta_simple("INSERT INTO factura (id, id_venta, token, serie, estado_fila) VALUES ('$numero', '" . $ultima['id'] . "','','$serie','1')");
            }

            $cabecera = array();

            // echo $numero;

            $cabecera["operacion"] = "generar_comprobante";
            $cabecera["tipo_de_comprobante"] = $tipDoc;
            $cabecera["serie"] = $serie;
            $cabecera["numero"] = str_pad($numero, 8, "0", STR_PAD_LEFT);
            $cabecera["sunat_transaction"] = 1;
            $cabecera["cliente_tipo_de_documento"] = $tipoAdq;
            $cabecera["cliente_numero_de_documento"] = $tipoDoc;
            $cabecera["cliente_denominacion"] = $tipoNom;
            $cabecera["cliente_email"] = $tipoEmail;
            $cabecera["cliente_email_1"] = "";
            $cabecera["cliente_email_2"] = "";
            $cabecera["fecha_de_emision"] = date("d-m-Y");
            $cabecera["fecha_de_vencimiento"] = "";
            $cabecera["moneda"] = 1;
            $cabecera["tipo_de_cambio"] = "";
            $cabecera["porcentaje_de_igv"] = "18.00";
            $cabecera["descuento_global"] =  floatval(0.00);
            $cabecera["total_descuento"] =  floatval(0.00);
            $cabecera["total_anticipo"] = "";
            $cabecera["total_anticipo"] = "";
            $cabecera["total_gravada"] = number_format(floatval($_POST['SubTotal']), 3, ".", "");
            $cabecera["total_inafecta"] = "";
            $cabecera["total_exonerada"] = "";
            $cabecera["total_igv"] = number_format(floatval($_POST['IGV']), 3, ".", "");
            $cabecera["total_gratuita"] = "";
            $cabecera["total_otros_cargos"] = "";
            $cabecera["total"] = number_format(floatval($_POST['Total']), 3, ".", "");
            $cabecera["percepcion_tipo"] = "";
            $cabecera["percepcion_base_imponible"] = "";
            $cabecera["total_percepcion"] = "";
            $cabecera["total_incluido_percepcion"] = "";
            $cabecera["detraccion"] = "false";
            $cabecera["observaciones"] = "";
            $cabecera["documento_que_se_modifica_tipo"] = "";
            $cabecera["documento_que_se_modifica_serie"] = "";
            $cabecera["documento_que_se_modifica_numero"] = "";
            $cabecera["tipo_de_nota_de_credito"] = "";
            $cabecera["tipo_de_nota_de_debito"] = "";
            $cabecera["enviar_automaticamente_a_la_sunat"] = "true";
            $cabecera["enviar_automaticamente_al_cliente"] = "true";
            $cabecera["codigo_unico"] = "";
            $cabecera["condiciones_de_pago"] = "";
            $cabecera["medio_de_pago"] = "";
            $cabecera["placa_vehiculo"] = "";
            $cabecera["orden_compra_servicio"] = "";
            $cabecera["tabla_personalizada_codigo"] = "";
            $cabecera["formato_de_pdf"] = "TICKET";

            $items = array();
            $details = json_decode($_POST['data']);
            foreach ($details as $value) {
                $item = array();
                $objConn = new venta();
                $producto_taxonomia = $objConn->consulta_arreglo("SELECT * FROM producto_taxonomiap WHERE id_producto = {$value->idProducto} AND id_taxonomiap = -1");
                $valor_sunat = $producto_taxonomia['valor'];
                $separar = explode("_", $valor_sunat);
                $codigo = $separar[0];

                $neto = $value->precio / 1.18;
                $igv = ($value->precio - $neto) * $value->cantidad;

                $item["unidad_de_medida"] = 'NIU';
                $item["codigo"] = $value->idProducto;
                $item["descripcion"] = $value->nombre;
                $item["cantidad"] = $value->cantidad;
                $item["codigo_producto_sunat"] = $codigo;
                $item["valor_unitario"] = number_format(floatval($neto), 3, ".", "");
                $item["precio_unitario"] = number_format(floatval($value->precio), 3, ".", "");
                $item["descuento"] = '';
                $item["subtotal"] =  number_format(floatval(($value->precio / 1.18) * $value->cantidad), 3, ".", "");
                $item["tipo_de_igv"] = 1;
                $item["igv"] = number_format($igv, 3, ".", "");
                $item["total"] = number_format(floatval($value->precio * $value->cantidad), 3, ".", "");
                $item["anticipo_regularizacion"] = false;
                $item["anticipo_documento_serie"] = '';

                $objConn->consulta_simple("INSERT INTO DetallesFree (id, id_venta, producto, cantidad, precio)
                    VALUES('','" . $ultima['id'] . "','" . $value->nombre . "','$value->cantidad','$value->precio')");


                $items[] = $item;
            }
       

            $cabecera["items"] = $items;
            $data_json = json_encode($cabecera);
            $config = $objventa->consulta_arreglo("Select * from configuracion");
            $ruta = $config["ruta"];
            $token = $config["token"];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ruta);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Authorization: Token token="' . $token . '"',
                    'Content-Type: application/json',
                )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuesta  = curl_exec($ch);
            if (intval(curl_errno($ch)) === 0) {
                curl_close($ch);
                //Verificamos respuesta
                //print_r($respuesta);
                $leer_respuesta = json_decode($respuesta, true);
                if (isset($leer_respuesta['errors'])) {
                    $qr = "Insert into comprobante_hash values('" . str_pad($numero, 8, "0", STR_PAD_LEFT) . "','NO',' ',' ','" . $leer_respuesta['errors'] . "')";
                    $objventa->consulta_simple($qr);
                    // echo "UPDATE venta SET estado_fila = '-1' where id = $numero";
                    $objventa->consulta_simple("UPDATE venta SET estado_fila = '9' where id = " . $ultima['id'] . "");
                    if ($_POST["tipo"] == 1) {
                        $objventa->consulta_simple("DELETE FROM boleta where id = $numero");
                    } else {
                        $objventa->consulta_simple("DELETE FROM factura where id = $numero");
                    }
                    //Mostramos errores
                    // echo json_encode($leer_respuesta['errors']);
                } else {
                    $aceptada = "NO";
                    if (boolval($leer_respuesta["aceptada_por_sunat"])) {
                        $aceptada = "SI";
                    }
                    $qr = "Insert into comprobante_hash values('" . str_pad($numero, 8, "0", STR_PAD_LEFT) . "','" . $aceptada . "','" . $leer_respuesta["codigo_hash"] . "','" . $leer_respuesta["cadena_para_codigo_qr"] . "','" . $leer_respuesta['sunat_description'] . "')";
                    $objventa->consulta_simple($qr);

                    switch ($_POST["tipo"]) {
                        case 0:
                            $tipoImprime = 'NOT';
                            break;
                        case 1:
                            $tipoImprime = 'BOL';
                            break;
                        case 2:
                            $tipoImprime = 'FAC';
                            break;
                    }

                    // echo "Insert into cola_impresion values(NULL, {$_POST['id']}, '{$tipoImprime}', {$_POST["id_caja"]}, '', 1)";
                    $objventa->consulta_simple("Insert into cola_impresion values(NULL, '" . $ultima['id'] . "', '{$tipoImprime}', {$_POST["idCaja"]}, '', 1)");
                    //Mostramos Respuesta
                    // echo json_encode($aceptada);
                }
            } else {
                curl_close($ch);
                $qr = "Insert into comprobante_hash values('" . str_pad($numero, 8, "0", STR_PAD_LEFT) . "','NE',' ',' ','')";
                $objventa->consulta_simple($qr);
                //Mostramos errores
                echo "NE";
            }

            echo $respuesta;
            break;

        case 'Cotizador':
            $config = $objventa->consulta_arreglo("Select * from configuracion");

            $cliente["id"] = 0;
            if ($_POST["doc"] != "") {
                $cliente = $objventa->consulta_arreglo("SELECT * FROM cliente WHERE documento =" . $_POST['doc']);
                if ($cliente == 0) {
                    if ($_POST["tipo"] == 1) $tipo_cliente = 1;
                    else $tipo_cliente = 2;

                    $objventa->consulta_simple("INSERT INTO cliente(id, nombre, documento, direccion, correo, tipo_cliente, fecha_nacimiento, estado_fila) VALUES ('','" . $_POST['nombre'] . "','" . $_POST['doc'] . "','" . $_POST['direccion'] . "','" . $_POST['correo'] . "','" . $tipo_cliente . "',NULL,'1')");

                    $cliente = $objventa->consulta_arreglo("SELECT * FROM cliente WHERE documento =" . $_POST['doc']);
                }
            }
            $coti = $objventa->consulta_simple("INSERT INTO cotizacion(id, subtotal, total_impuestos, total, fecha_hora, 
                id_usuario, id_caja, id_cliente, estado_fila) VALUES ('',
                '" . $_POST["SubTotal"] . "',
                '" . $_POST['IGV'] . "',
                '" . $_POST['Total'] . "',
                '" . date("Y-m-d H:i:s") . "',
                '" . $_POST['idUsuario'] . "',
                '" . $_POST["idCaja"] . "',
                '" . $cliente["id"] . "',
                '1')");

            $ultima = $objventa->consulta_arreglo("SELECT * FROM cotizacion ORDER BY id DESC limit 1");
            $details = json_decode($_POST['data']);
            foreach ($details as $value) {
                $objventa->consulta_simple("INSERT INTO detalles_cotizacion (id, id_coti, id_producto, cantidad, precio)
                    VALUES('','" . $ultima['id'] . "','" . $value->idProducto . "','$value->cantidad','$value->precio')");
            }

            echo json_encode($ultima['id']);
            break;

        case 'notas':
            $res = $objventa->consulta_arreglo("SELECT * FROM venta WHERE id = " . $_POST["id"]);

            $config = $objventa->consulta_arreglo("SELECT * from configuracion");

            $cliente = $objventa->consulta_arreglo("SELECT * FROM cliente WHERE id =" . $res['id_cliente']);

            if (intval($_POST['nota'] == 1)) {
                $tipDoc = 4;
                $tipo_de_nota_de_credito = "";
                $tipo_de_nota_de_debito = 1;
            } else {
                $tipDoc = 3;
                $tipo_de_nota_de_credito = 1;
                $tipo_de_nota_de_debito = "";
            }

            if (intval($_POST["tipo"]) == 1) {
                $serie = $config["serie_boleta"];
                $boleta = $objventa->consulta_arreglo("Select * from boleta WHERE id_venta =" . $res["id"]);
                $numero = $boleta['id'];

                $tipDocMod = 2;
                if (empty($cliente["documento"])) {
                    $tipoAdq = '-';
                    $tipoDoc = '-';
                    $tipoNom = '---';
                    $tipoEmail = '';
                } else {
                    $tipoAdq = '1';
                    $tipoDoc = $cliente["documento"];
                    $tipoNom = $cliente["nombre"];
                    $tipoEmail = $cliente["correo"];
                }
            } else {
                $serie = $config["serie_factura"];
                $factura = $objventa->consulta_arreglo("Select * from factura WHERE id_venta =" . $res["id"]);
                $numero = $factura['id'];

                $tipoAdq = '6';
                $tipDocMod = 1;

                $tipoDoc = $cliente["documento"];
                $tipoNom = $cliente["nombre"];
                $tipoEmail = $cliente["correo"];
            }

            $cabecera = array();


            if (is_array($res)) {

                $cabecera["operacion"] = "generar_comprobante";
                $cabecera["tipo_de_comprobante"] = $tipDoc;
                $cabecera["serie"] = $serie;
                $cabecera["numero"] = str_pad($numero, 8, "0", STR_PAD_LEFT);
                $cabecera["sunat_transaction"] = 1;
                $cabecera["cliente_tipo_de_documento"] = $tipoAdq;
                $cabecera["cliente_numero_de_documento"] = $tipoDoc;
                $cabecera["cliente_denominacion"] = $tipoNom;
                $cabecera["cliente_email"] = $tipoEmail;
                $cabecera["cliente_email_1"] = "";
                $cabecera["cliente_email_2"] = "";
                $cabecera["fecha_de_emision"] = date("d-m-Y");
                $cabecera["fecha_de_vencimiento"] = "";
                $cabecera["moneda"] = 1;
                $cabecera["tipo_de_cambio"] = "";
                $cabecera["porcentaje_de_igv"] = "18.00";
                $cabecera["descuento_global"] =  floatval(0.00);
                $cabecera["total_descuento"] =  floatval(0.00);
                $cabecera["total_anticipo"] = "";
                $cabecera["total_anticipo"] = "";
                $cabecera["total_gravada"] = number_format(floatval($res['subtotal']), 4, ".", "");
                $cabecera["total_inafecta"] = "";
                $cabecera["total_exonerada"] = "";
                $cabecera["total_igv"] = number_format(floatval($res['total_impuestos']), 4, ".", "");
                $cabecera["total_gratuita"] = "";
                $cabecera["total_otros_cargos"] = "";
                $cabecera["total"] = number_format(floatval($res['total']), 4, ".", "");
                $cabecera["percepcion_tipo"] = "";
                $cabecera["percepcion_base_imponible"] = "";
                $cabecera["total_percepcion"] = "";
                $cabecera["total_incluido_percepcion"] = "";
                $cabecera["detraccion"] = "false";
                $cabecera["observaciones"] = "";
                $cabecera["documento_que_se_modifica_tipo"] = $tipDocMod;
                $cabecera["documento_que_se_modifica_serie"] = $serie;
                $cabecera["documento_que_se_modifica_numero"] = str_pad($numero, 8, "0", STR_PAD_LEFT);
                $cabecera["tipo_de_nota_de_credito"] = $tipo_de_nota_de_credito;
                $cabecera["tipo_de_nota_de_debito"] = $tipo_de_nota_de_debito;
                $cabecera["enviar_automaticamente_a_la_sunat"] = "true";
                $cabecera["enviar_automaticamente_al_cliente"] = "true";
                $cabecera["codigo_unico"] = "";
                $cabecera["condiciones_de_pago"] = "";
                $cabecera["medio_de_pago"] = "";
                $cabecera["placa_vehiculo"] = "";
                $cabecera["orden_compra_servicio"] = "";
                $cabecera["tabla_personalizada_codigo"] = "";
                $cabecera["formato_de_pdf"] = "TICKET";

                $items = array();

                $detalles = $objventa->consulta_matriz("SELECT pv.id, nombre, cantidad, precio, pv.opc FROM producto_venta pv, producto p WHERE pv.estado_fila= 1 AND pv.id_venta = " . $res["id"] . " AND pv.id_producto = p.id UNION SELECT s.id, nombre, cantidad, precio, sv.opc FROM servicio_venta sv, servicio s WHERE sv.id_venta = " . $res["id"] . " AND sv.id_servicio = s.id");

                for ($i = 0; $i < count($detalles); $i++) {
                    $item = array();
                    if ($detalles[$i]['opc'] == 'p')
                        $unidadMedida = 'NIU';
                    else
                        $unidadMedida = 'ZZ';


                    $objConn = new venta();
                    $producto_taxonomia = $objConn->consulta_arreglo("SELECT * FROM producto_taxonomiap WHERE id_producto = " . $detalles[$i]['id'] . " AND id_taxonomiap = -1");
                    $valor_sunat = $producto_taxonomia['valor'];
                    $separar = explode("_", $valor_sunat);
                    $codigo = $separar[0];
                    //----------------------------

                    $valor_unitario = floatval($detalles[$i]['precio'] / 1.18);
                    $subtotal = floatval($valor_unitario * $detalles[$i]['cantidad']);

                    $item["unidad_de_medida"] = $unidadMedida;
                    $item["codigo"] = $detalles[$i]['id'];
                    $item["descripcion"] =  $detalles[$i]['nombre'];
                    $item["cantidad"] = $detalles[$i]['cantidad'];
                    $item["codigo_producto_sunat"] = $codigo;
                    $item["valor_unitario"] = number_format($valor_unitario, 4, ".", "");
                    $item["precio_unitario"] = $detalles[$i]['precio'];
                    $item["descuento"] = "";
                    $item["subtotal"] = number_format($subtotal, 4, ".", "");
                    $item["tipo_de_igv"] = '1';
                    $item["igv"] = number_format(floatval((($detalles[$i]['precio'] - $valor_unitario) * $detalles[$i]['cantidad'])), 4, ".", "");
                    $item["total"] = number_format(floatval(($detalles[$i]['precio'] * $detalles[$i]['cantidad'])), 4, ".", "");
                    $item["anticipo_regularizacion"] = false;
                    $item["anticipo_documento_serie"] = "";

                    $items[] = $item;
                }
              
                $cabecera["items"] = $items;
                // print_r($cabecera);
                $data_json = json_encode($cabecera);

                $ruta = $config["ruta"];
                $token = $config["token"];

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $ruta);
                curl_setopt(
                    $ch,
                    CURLOPT_HTTPHEADER,
                    array(
                        'Authorization: Token token="' . $token . '"',
                        'Content-Type: application/json',
                    )
                );
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $respuesta  = curl_exec($ch);
                if (intval(curl_errno($ch)) === 0) {
                    curl_close($ch);
                    //Verificamos respuesta
                    //print_r($respuesta);
                    $leer_respuesta = json_decode($respuesta, true);
                    if (isset($leer_respuesta['errors'])) {
                        //Mostramos errores
                        //echo json_encode($leer_respuesta['errors']);
                    } else {
                        if (intval($_POST['nota']) == 2) {
                            $qr = "UPDATE venta SET estado_fila = 3 WHERE id = " . $_POST['id'];
                            $objventa->consulta_simple($qr);
                        } else {
                            $qr = "UPDATE venta SET estado_fila = 4 WHERE id = " . $_POST['id'];
                            $objventa->consulta_simple($qr);
                        }
                    }
                } else {
                    // curl_close($ch);
                    // $qr = "Insert into comprobante_hash values('".$numero."','NE',' ',' ','')";
                    // $objventa->consulta_simple($qr);
                    //Mostramos errores
                    echo "NE";
                }

                echo $respuesta;
            } else {
                echo json_encode(0);
            }
            break;
            case 'notaCreditoDebito':
              
              //  $details = json_decode($_POST['data']);
                $itemsNotaCreditoDebito= json_decode($_POST["itemsNotaCreditoDebito"]);
                $subtotal=  $_POST["subTotal"];
                $total=  $_POST["total"];
                $igv=  $_POST["igv"];
                $res = $objventa->consulta_arreglo("SELECT * FROM venta WHERE id = " . $_POST["idVenta"]);
    
                $config = $objventa->consulta_arreglo("SELECT * from configuracion");
    
                $cliente = $objventa->consulta_arreglo("SELECT * FROM cliente WHERE id =" . $_POST["idCliente"]);
                
              
                if (intval($_POST['nota'] == 2)) {
                    $tipDoc = 4;
                    $tipo_de_nota_de_credito = "";
                    $tipo_de_nota_de_debito =  $_POST["motivoEmision"];
                } else {
                    $tipDoc = 3;
                    $tipo_de_nota_de_credito =  $_POST["motivoEmision"];
                    $tipo_de_nota_de_debito = "";
                }
               
                if (intval($_POST["tipo"]) == 1) {
                    $serie = $config["serie_boleta"];
                    $boleta = $objventa->consulta_arreglo("Select * from boleta WHERE id_venta =" . $res["id"]);
                    $numero = $boleta['id'];
    
                    $tipDocMod = 2;
                    if (empty($cliente["documento"])) {
                        $tipoAdq = '-';
                        $tipoDoc = '-';
                        $tipoNom = '---';
                        $tipoEmail = '';
                    } else {
                        $tipoAdq = '1';
                        $tipoDoc = $cliente["documento"];
                        $tipoNom = $cliente["nombre"];
                        $tipoEmail = $cliente["correo"];
                    }
                } else {
                    $serie = $config["serie_factura"];
                    $factura = $objventa->consulta_arreglo("Select * from factura WHERE id_venta =" . $res["id"]);
                    $numero = $factura['id'];
    
                    $tipoAdq = '6';
                    $tipDocMod = 1;
    
                    $tipoDoc = $cliente["documento"];
                    $tipoNom = $cliente["nombre"];
                    $tipoEmail = $cliente["correo"];
                }
                
                $cabecera = array();
    
    
                if (is_array($res)) {
    
                    $cabecera["operacion"] = "generar_comprobante";
                    $cabecera["tipo_de_comprobante"] = $tipDoc;
                    $cabecera["serie"] = $serie;
                    $cabecera["numero"] = str_pad($numero, 8, "0", STR_PAD_LEFT);
                    $cabecera["sunat_transaction"] = 1;
                    $cabecera["cliente_tipo_de_documento"] = $tipoAdq;
                    $cabecera["cliente_numero_de_documento"] = $tipoDoc;
                    $cabecera["cliente_denominacion"] = $tipoNom;
                    $cabecera["cliente_email"] = $tipoEmail;
                    $cabecera["cliente_email_1"] = "";
                    $cabecera["cliente_email_2"] = "";
                    $cabecera["fecha_de_emision"] = date("d-m-Y");
                    $cabecera["fecha_de_vencimiento"] = "";
                    $cabecera["moneda"] = 1;
                    $cabecera["tipo_de_cambio"] = "";
                    $cabecera["porcentaje_de_igv"] = "18.00";
                    $cabecera["descuento_global"] =  floatval(0.00);
                    $cabecera["total_descuento"] =  floatval(0.00);
                    $cabecera["total_anticipo"] = "";
                    $cabecera["total_anticipo"] = "";
                    $cabecera["total_gravada"] = number_format(floatval($subtotal), 4, ".", "");
                    $cabecera["total_inafecta"] = "";
                    $cabecera["total_exonerada"] = "";
                    $cabecera["total_igv"] = number_format(floatval($igv), 4, ".", "");
                    $cabecera["total_gratuita"] = "";
                    $cabecera["total_otros_cargos"] = "";
                    $cabecera["total"] = number_format(floatval($total), 4, ".", "");
                    $cabecera["percepcion_tipo"] = "";
                    $cabecera["percepcion_base_imponible"] = "";
                    $cabecera["total_percepcion"] = "";
                    $cabecera["total_incluido_percepcion"] = "";
                    $cabecera["detraccion"] = "false";
                    $cabecera["observaciones"] = "";
                    $cabecera["documento_que_se_modifica_tipo"] = $tipDocMod;
                    $cabecera["documento_que_se_modifica_serie"] = $serie;
                    $cabecera["documento_que_se_modifica_numero"] = str_pad($numero, 8, "0", STR_PAD_LEFT);
                    $cabecera["tipo_de_nota_de_credito"] = $tipo_de_nota_de_credito;
                    $cabecera["tipo_de_nota_de_debito"] = $tipo_de_nota_de_debito;
                    $cabecera["enviar_automaticamente_a_la_sunat"] = "true";
                    $cabecera["enviar_automaticamente_al_cliente"] = "true";
                    $cabecera["codigo_unico"] = "";
                    $cabecera["condiciones_de_pago"] = "";
                    $cabecera["medio_de_pago"] = "";
                    $cabecera["placa_vehiculo"] = "";
                    $cabecera["orden_compra_servicio"] = "";
                    $cabecera["tabla_personalizada_codigo"] = "";
                    $cabecera["formato_de_pdf"] = "TICKET";
    
                    $items = array();
                  
                   /* foreach ($details as $value) {
                        $objventa->consulta_simple("INSERT INTO detalles_cotizacion (id, id_coti, id_producto, cantidad, precio)
                            VALUES('','" . $ultima['id'] . "','" . $value->idProducto . "','$value->cantidad','$value->precio')");
                    }*/

                    if (isset($itemsNotaCreditoDebito))
                    {
                        //$detalles = $objventa->consulta_matriz("SELECT pv.id, nombre, cantidad, precio, pv.opc FROM producto_venta pv, producto p WHERE id_venta = " . $res["id"] . " AND pv.id_producto = p.id UNION SELECT s.id, nombre, cantidad, precio, sv.opc FROM servicio_venta sv, servicio s WHERE sv.id_venta = " . $res["id"] . " AND sv.id_servicio = s.id");
                        foreach($itemsNotaCreditoDebito as $itemsNota){
                          
                                $item = array();
                            // if ($detalles[$i]['opc'] == 'p')
                                    $unidadMedida = 'NIU';
                            /*  else
                                    $unidadMedida = 'ZZ';*/
            
                                $objConn = new venta();
                                $producto_taxonomia = $objConn->consulta_arreglo("SELECT * FROM producto_taxonomiap WHERE id_producto = " . $itemsNota->idProducto . " AND id_taxonomiap = -1");
                                $valor_sunat = $producto_taxonomia['valor'];
                                $separar = explode("_", $valor_sunat);
                                $codigo = $separar[0];
                                //----------------------------
            
                                $valor_unitario = floatval($itemsNota->precio / 1.18);
                                $subtotal = floatval($valor_unitario * $itemsNota->cantidad);
                
                    
                                $item["unidad_de_medida"] = $unidadMedida;
                                $item["codigo"] = $itemsNota->idProducto;
                                $item["descripcion"] =  $itemsNota->nombre;
                                $item["cantidad"] = $itemsNota->cantidad;
                                $item["codigo_producto_sunat"] = $codigo;
                                $item["valor_unitario"] = number_format($valor_unitario, 4, ".", "");
                                $item["precio_unitario"] = $itemsNota->precio;
                                $item["descuento"] = "";
                                $item["subtotal"] = number_format($subtotal, 4, ".", "");
                                $item["tipo_de_igv"] = '1';
                                $item["igv"] = number_format(floatval((($itemsNota->precio - $valor_unitario) *  $itemsNota->cantidad)), 4, ".", "");
                                $item["total"] = number_format(floatval(($itemsNota->precio * $itemsNota->cantidad)), 4, ".", "");
                                $item["anticipo_regularizacion"] = false;
                                $item["anticipo_documento_serie"] = "";
            
                                $items[] = $item;
                            }
                        }
                  
                    $cabecera["items"] = $items;
                    // print_r($cabecera);
                    $data_json = json_encode($cabecera);
    
                    $ruta = $config["ruta"];
                    $token = $config["token"];
    
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $ruta);
                    curl_setopt(
                        $ch,
                        CURLOPT_HTTPHEADER,
                        array(
                            'Authorization: Token token="' . $token . '"',
                            'Content-Type: application/json',
                        )
                    );
                    curl_setopt($ch, CURLOPT_POST, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $respuesta  = curl_exec($ch);
                    if (intval(curl_errno($ch)) === 0) {
                        curl_close($ch);
                        //Verificamos respuesta
                        //print_r($respuesta);
                        $leer_respuesta = json_decode($respuesta, true);
                        if (isset($leer_respuesta['errors'])) {
                            //Mostramos errores
                            //echo json_encode($leer_respuesta['errors']);
                        } else {
                            if (intval($_POST['nota']) == 1) {
                                $qr = "UPDATE venta SET estado_fila = 3 WHERE id = " . $_POST['idVenta'];
                                $objventa->consulta_simple($qr);
                            } else {
                                $qr = "UPDATE venta SET estado_fila = 4 WHERE id = " . $_POST['idVenta'];
                                $objventa->consulta_simple($qr);
                            }
                        }
                    } else {
                        // curl_close($ch);
                        // $qr = "Insert into comprobante_hash values('".$numero."','NE',' ',' ','')";
                        // $objventa->consulta_simple($qr);
                        //Mostramos errores
                        echo "NE";
                    }
    
                    echo $respuesta;
                } else {
                    echo json_encode(0);
                }
                break;

        case 'totalNotaCredito':

            $objs = $conn->consulta_arreglo("SELECT * FROM venta WHERE id = " . $_POST["id"]);

            echo json_encode($objs);
            break;

        case 'VentaNotaCredito':
            $sum = 0;

            $objs = $conn->consulta_simple("INSERT INTO ventas_notas(id, id_venta, id_venta_nota, subtotal, total_impuestos, total) VALUES (''," . $_POST["id"] . "," . $_POST["id_nota"] . ",'0','0','0')");

            $boleta = $conn->consulta_arreglo("SELECT * FROM boleta where id_venta = " . $_POST["id_nota"]);;
            if (is_array($boleta)) {
                $objs = $conn->consulta_simple("UPDATE boleta SET estado_fila = 3 WHERE id_venta = " . $_POST["id_nota"]);
            } else {
                $factura = $conn->consulta_arreglo("SELECT * FROM factura where id_venta = " . $_POST["id_nota"]);

                if (is_array($factura)) {

                    $objs = $conn->consulta_simple("UPDATE factura SET estado_fila = 3 WHERE id_venta = " . $_POST["id_nota"]);
                }
            }

            echo json_encode($objs);
            break;

        case 'addEntregas':

            $objs = $conn->consulta_simple("INSERT INTO entregas VALUES(null, " . $_POST["venta"] . ", '" . $_POST["cliente"] . "', '" . $_POST["fecha"] . "', " . $_POST["abono"] . ", '1', '" . $_POST["comentario"] . "')");

            if ($_POST["estado"] == 0) {
                $vmp = $conn->consulta_arreglo("SELECT * FROM venta_medio_pago WHERE id_venta = " . $_POST["venta"]);
            } else {
                $vmp = 1;
            }

            echo json_encode($vmp);
            break;

        case 'entregar':

            $objs = $conn->consulta_simple("UPDATE entregas SET estado = 2 WHERE id = " . $_POST["id"]);

            echo json_encode($objs);
            break;

        case 'delentregar':

            $objs = $conn->consulta_simple("DELETE FROM entregas WHERE id_venta = " . $_POST["id"]);

            echo json_encode($objs);
            break;

        case 'delentregar2':

            $objs = $conn->consulta_simple("DELETE FROM entregas WHERE id = " . $_POST["id"]);

            echo json_encode($objs);
            break;

        case 'detallesentrega':

            $objs = $conn->consulta_matriz("
                SELECT nombre, precio, cantidad, total
                FROM entregas e
                INNER JOIN producto_venta pv ON pv.id_venta = e.id_venta
                INNER JOIN producto p ON p.id = pv.id_producto
                WHERE e.id = " . $_POST["id"]);

            echo json_encode($objs);
            break;

        case 'asignarvendedor':

            $objs = $conn->consulta_simple("UPDATE venta SET id_usuario = '" . $_POST["id_usuario"] . "' WHERE id =" . $_POST["id"]);

            echo json_encode($objs);
         break;
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

function _consultar_documento($con, $id_venta, $tipo_documento)
{
    $tipo = '';
    $anul = '';
    $respuesta = [
        'success' => false,
        'message' => "Operacion No Exitosa",
    ];
    if ($tipo_documento == 1) {
        $tipo = '2';
        $anul = $con->consulta_arreglo("SELECT * FROM  boleta WHERE id_venta=" . $id_venta . "");
    } else {
        $tipo = '1';
        $anul = $con->consulta_arreglo("SELECT * FROM  factura WHERE id_venta=" . $id_venta . "");
    }

    $cabecera = array();
    $cabecera["operacion"] = "consultar_comprobante";
    $cabecera["tipo_de_comprobante"] = $tipo;
    $cabecera["serie"] = $anul["serie"];
    $cabecera["numero"] = $anul["id"];

    // print_r($cabecera);
    $data_json = json_encode($cabecera);

    $config = $con->consulta_arreglo("Select * from configuracion");
    $ruta = $config["ruta"];
    $token = $config["token"];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array(
            'Authorization: Token token="' . $token . '"',
            'Content-Type: application/json',
        )
    );
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $consulta  = curl_exec($ch);

    $consulta = (array) json_decode(stripslashes($consulta));
    if ($consulta !== "") {
        if ($consulta['aceptada_por_sunat'] == false) {
            $respuesta['success'] = false;
            $respuesta['message'] = "El documento aun esta siendo cargado a SUNAT, no se puede eliminar";
        } else if ($consulta['aceptada_por_sunat'] == true) {
            $respuesta['success'] = true;
        }
    } else {
        $respuesta['success'] = false;
        $respuesta['message'] = "El documento nose encontro en SUNAT, no se puede eliminar";
    }
    return $respuesta;
}

function _valida_fecha($fechaIn)
{
    $d = new DateTime($fechaIn);
    $fechaIn = $d->format('Y-m-d');
    $respuesta = [
        'success' => false,
        'message' => 'Operacion no Exitosa'
    ];

    $date1 = date_create($fechaIn);
    $date2 = date_create(date('Y-m-d'));
    $diff = date_diff($date2, $date1);
    // var_dump($date2);
    // var_dump($date1);
    if ($diff->days > 7) {
        $respuesta['success'] = false;
        $respuesta['message'] = "El tiempo maximo para poder eliminar una boleta o factura es de 7 dias, posteriormente se debe hacer una nota de Credito ";
    } else {
        $respuesta['success'] = true;
        $respuesta['message'] = "Operacion Exitosa";
    }

    return $respuesta;
}