<?php

require_once('../nucleo/venta_medio_pago.php');
$objventa_medio_pago = new venta_medio_pago();

require_once('../nucleo/venta.php');
$objventa = new venta();

require_once('../nucleo/include/MasterConexion.php');
$conexion = new MasterConexion();
$con = $conexion->getConnection();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':

            $objventa_medio_pago = new venta_medio_pago();
            $objventa_medio_pago->setVar('id_venta', $_POST['id_venta']);
            $objventa_medio_pago->setVar('medio', $_POST['medio']);
            $objventa_medio_pago->setVar('monto', $_POST['monto']);
            $objventa_medio_pago->setVar('moneda', $_POST['moneda']);
            $objventa_medio_pago->setVar('vuelto', $_POST['vuelto']);
            $objventa_medio_pago->setVar('estado_fila', "1");
            $idi = $objventa_medio_pago->insertDB();

            if ($_POST["medio"] <> "DESCUENTO") {
                $objconn = new venta();
                $cambio = $objconn->consulta_arreglo("Select * from tipo_cambio where moneda_origen = 'USD' AND moneda_destino = 'PEN'");

                $objventa = new venta();
                $objventa->setVar('id', $_POST['id_venta']);
                $objventa->getDB();

                $monto = $_POST['monto']-$_POST['vuelto'];
                if ($_POST['moneda'] === "USD") {
                    $monto = floatval($_POST['monto']) * floatval($cambio["compra"]);
                }

                $series = $objconn->consulta_arreglo("Select * from configuracion where id = 1");

                $objconn->consulta_simple("Insert into movimiento_caja values(NULL,{$objventa->getIdCaja()},{$monto},'SELL|{$_POST['moneda']}|{$_POST['medio']}|{$idi}','".date("Y-m-d H:i:s")."','".$series['fecha_cierre']."','{$objventa->getIdTurno()}','{$objventa->getIdUsuario()}','1')");
            }else{
                // $vmp = $objventa_medio_pago->consulta_matriz("SELECT * FROM venta_medio_pago WHERE id_venta = ".$_POST["id_venta"]);

                // if(count($vmp) == 1){
                //     echo "SELECT * FROM venta WHERE id = ".$_POST["id_venta"];
                //     $venta =  $objventa_medio_pago->consulta_arreglo("SELECT * FROM venta WHERE id = ".$_POST["id_venta"]);
                //     echo json_encode($venta);
                // }

            }
            echo json_encode($idi);
            break;

        case 'mod':
        
            $objventa_medio_pago->setVar('id', $_POST['id']);
            $objventa_medio_pago->setVar('id_venta', $_POST['id_venta']);
            $objventa_medio_pago->setVar('medio', $_POST['medio']);
            $objventa_medio_pago->setVar('monto', $_POST['monto']);
            $objventa_medio_pago->setVar('moneda', $_POST['moneda']);
            $objventa_medio_pago->setVar('vuelto', $_POST['vuelto']);
            $objventa_medio_pago->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objventa_medio_pago->updateDB());
            break;

        case 'del':
            $objventa_medio_pago->setVar('id', $_POST['id']);

            $mov_caja = $objventa->consulta_matriz("SELECT * FROM movimiento_caja WHERE tipo_movimiento LIKE '%|{$_POST['id']}'");
            if (is_array($mov_caja)) {
                foreach ($mov_caja as $mov) {
                    $objventa->consulta_simple("DELETE FROM movimiento_caja WHERE id = {$mov['id']}");
                }
            }

            echo json_encode($objventa_medio_pago->deleteDB());
            break;

        case 'get':
            $res = $objventa_medio_pago->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_venta'] = $objventa->searchDB($res[0]['id_venta'], 'id', 1);
                $res[0]['id_venta'] = $res[0]['id_venta'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objventa_medio_pago->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_venta'] = $objventa->searchDB($act['id_venta'], 'id', 1);
                    $act['id_venta'] = $act['id_venta'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'listventa':
            $res = $objventa_medio_pago->consulta_matriz("Select * from venta_medio_pago where id_venta = '".$_POST["id"]."' ORDER BY medio");
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objventa_medio_pago->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_venta'] = $objventa->searchDB($act['id_venta'], 'id', 1);
                    $act['id_venta'] = $act['id_venta'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'ordersHeader':

            if($_POST["comprobante"] == 2){
                $res = $objventa_medio_pago->consulta_arreglo("SELECT ruc, nombre_negocio, moneda, correoEmisor, serie_boleta, serie_factura, subtotal, total_impuestos, total, f.id, documento, nombre FROM venta v, configuracion c, factura f, cliente cl WHERE v.id = '".$_POST["id"]."' AND f.id_venta = v.id AND v.id_cliente = cl.id");
            }else{

                // echo "SELECT ruc, nombre_negocio, moneda, correoEmisor, serie_boleta, serie_factura, subtotal, total_impuestos, total, b.id, documento, nombre FROM venta v, configuracion c, boleta b, cliente cl WHERE v.id = '".$_POST["id"]."' AND b.id_venta = v.id AND v.id_cliente = cl.id";
                $res = $objventa_medio_pago->consulta_arreglo("SELECT ruc, nombre_negocio, moneda, correoEmisor, serie_boleta, serie_factura, subtotal, total_impuestos, total, b.id, documento, nombre FROM venta v, configuracion c, boleta b, cliente cl WHERE v.id = '".$_POST["id"]."' AND b.id_venta = v.id AND v.id_cliente = cl.id");
            }

            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'ordersDetails':
            $res = $objventa_medio_pago->consulta_matriz("SELECT pv.id, nombre, cantidad, precio FROM producto_venta pv, producto p WHERE pv.estado_fila= 1 AND pv.id_venta = '".$_POST["id"]."' AND pv.id_producto = p.id ");
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'ordersDetailsService':
            $res = $objventa_medio_pago->consulta_matriz("SELECT s.id, nombre, precio, cantidad, total FROM servicio_venta sv, servicio s WHERE sv.id_venta = '".$_POST["id"]."' AND sv.id_servicio = s.id ");
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'ordersDetailsUnion':
            $res = $objventa_medio_pago->consulta_matriz("SELECT p.id, nombre, unidad, cantidad, precio, pv.opc FROM producto_venta pv, producto p WHERE pv.estado_fila= 1 AND pv.id_venta = '".$_POST["id"]."' AND pv.id_producto = p.id");
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'addHeader':
            $indicador                       = empty($_POST['header']['indicador']) ? 'NULL' : $_POST['header']['indicador'];
            $tipoDocumentoEmisor             = empty($_POST['header']['tipoDocumentoEmisor']) ? 'NULL' : $_POST['header']['tipoDocumentoEmisor'];
            $numeroDocumentoEmisor           = empty($_POST['header']['numeroDocumentoEmisor']) ? 'NULL' : $_POST['header']['numeroDocumentoEmisor'];
            $razonSocialEmisor               = empty($_POST['header']['razonSocialEmisor']) ? 'NULL' : $_POST['header']['razonSocialEmisor'];
            $tipoDocumento                   = empty($_POST['header']['tipoDocumento']) ? 'NULL' : $_POST['header']['tipoDocumento'];
            $serieNumero                     = empty($_POST['header']['serieNumero']) ? 'NULL' : $_POST['header']['serieNumero'];
            $fechaEmision                    = empty($_POST['header']['fechaEmision']) ? 'NULL' : $_POST['header']['fechaEmision'];
            $correoEmisor                    = empty($_POST['header']['correoEmisor']) ? 'NULL' : $_POST['header']['correoEmisor'];
            $tipoDocumentoAdquiriente        = $_POST['header']['tipoDocumentoAdquiriente'];

            $numeroDocumentoAdquiriente      = empty($_POST['header']['numeroDocumentoAdquiriente']) ? 'NULL' : $_POST['header']['numeroDocumentoAdquiriente'];
            $razonSocialAdquiriente          = empty($_POST['header']['razonSocialAdquiriente']) ? 'NULL' : $_POST['header']['razonSocialAdquiriente'];
            $direccionAdquiriente            = empty($_POST['header']['direccionAdquiriente']) ? 'NULL' : $_POST['header']['direccionAdquiriente'];
            $correoAdquiriente            = empty($_POST['header']['correoAdquiriente']) ? 'NULL' : $_POST['header']['correoAdquiriente'];
            $tipoMoneda                      = empty($_POST['header']['tipoMoneda']) ? 'NULL' : $_POST['header']['tipoMoneda'];
            $totalValorVentaNetoOpGravadas   = empty($_POST['header']['totalValorVentaNetoOpGravadas']) ? 'NULL' : $_POST['header']['totalValorVentaNetoOpGravadas'];
            $totalIgv                        = empty($_POST['header']['totalIgv']) ? 'NULL' : $_POST['header']['totalIgv'];
            $totalVenta                      = empty($_POST['header']['totalVenta']) ? 'NULL' : $_POST['header']['totalVenta'];

            $serie                          = empty($_POST['header']['serie']) ? 'NULL' : $_POST['header']['serie'];
            $numero                         = empty($_POST['header']['numero']) ? 'NULL' : $_POST['header']['numero'];
            $descuento                      = empty($_POST['header']['descuento']) ? 'NULL' : $_POST['header']['descuento'];
            
            $header = $objventa->consulta_simple("
                INSERT INTO cabecera (id, indicador, tipoDocumentoEmisor, numeroDocumentoEmisor, razonSocialEmisor, tipoDocumento, serieNumero, fechaEmision, correoEmisor, tipoDocumentoAdquiriente, numeroDocumentoAdquiriente, razonSocialAdquiriente, direccionAdquiriente, correoAdquiriente, tipoMoneda, totalValorVentaNetoOpGravadas, totalIgv, totalVenta) 

                VALUES (NULL, '$indicador', '$tipoDocumentoEmisor', '$numeroDocumentoEmisor', '$razonSocialEmisor', '$tipoDocumento ', '$serieNumero', Now(), '$correoEmisor', '$tipoDocumentoAdquiriente', '$numeroDocumentoAdquiriente', '$razonSocialAdquiriente', '$direccionAdquiriente', '$correoAdquiriente', '$tipoMoneda', '$totalValorVentaNetoOpGravadas', '$totalIgv', '$totalVenta')"
            );

            echo $header;
            break;

        case 'addDetails':

            $indicador                  = empty($_POST['details']['indicador']) ? 'NULL' : $_POST['details']['indicador'];
            $numeroOrdenItem            = empty($_POST['details']['numeroOrdenItem']) ? 'NULL' : $_POST['details']['numeroOrdenItem'];
            $descripcion                = empty($_POST['details']['descripcion']) ? 'NULL' : $_POST['details']['descripcion'];
            $cantidad                   = empty($_POST['details']['cantidad']) ? 'NULL' : $_POST['details']['cantidad'];
            $unidadMedida               = empty($_POST['details']['unidadMedida']) ? 'NULL' : $_POST['details']['unidadMedida'];
            $importeUnitarioSinImpuesto = empty($_POST['details']['importeUnitarioSinImpuesto']) ? 'NULL' : $_POST['details']['importeUnitarioSinImpuesto'];
            $importeUnitarioConImpuesto = empty($_POST['details']['importeUnitarioConImpuesto']) ? 'NULL' : $_POST['details']['importeUnitarioConImpuesto'];
            $codigoImporteUnitarioConImpuesto = empty($_POST['details']['codigoImporteUnitarioConImpuesto']) ? 'NULL' : $_POST['details']['codigoImporteUnitarioConImpuesto'];
            $importeTotalSinImpuesto     = empty($_POST['details']['importeTotalSinImpuesto']) ? 'NULL' : $_POST['details']['importeTotalSinImpuesto'];
            $codigoRazonExoneracion      = empty($_POST['details']['codigoRazonExoneracion']) ? 'NULL' : $_POST['details']['codigoRazonExoneracion'];
            $importeIgv                  = empty($_POST['details']['importeIgv']) ? 'NULL' : $_POST['details']['importeIgv'];


            $detalles = $objventa->consulta_simple("
                INSERT INTO detalles (id, indicador, numeroOrdenItem, descripcion, cantidad, unidadMedida, importeUnitarioSinImpuesto, importeUnitarioConImpuesto, codigoImporteUnitarioConImpuesto, importeTotalSinImpuesto, codigoRazonExoneracion, importeIgv) 

                VALUES (NULL, '$indicador', '$numeroOrdenItem', '$descripcion', '$cantidad', '$unidadMedida', '$importeUnitarioSinImpuesto', '$importeUnitarioConImpuesto', '$codigoImporteUnitarioConImpuesto', '$importeTotalSinImpuesto', '$codigoRazonExoneracion', '$importeIgv')");
        
            echo $detalles;
            break;

        case 'curl':
            $header = json_decode($_POST['header']);
            $idBoleta = $header->numero;
            $details = json_decode($_POST['details']);

            $objConn = new venta();
            $config = $objConn->consulta_arreglo("SELECT * from configuracion");

            $cabecera = array();

            if($header->fecha_de_vencimiento == "0")
                $fecha_de_vencimiento = date("d-m-Y");
            else if($header->fecha_de_vencimiento == "7")
                $fecha_de_vencimiento = date("d-m-Y",strtotime(date("d-m-Y")."+ 7 days")); 
            else if($header->fecha_de_vencimiento == "15")
                $fecha_de_vencimiento = date("d-m-Y",strtotime(date("d-m-Y")."+ 15 days")); 
            else if($header->fecha_de_vencimiento == "30")
                $fecha_de_vencimiento = date("d-m-Y",strtotime(date("d-m-Y")."+ 30 days")); 
            else if($header->fecha_de_vencimiento == "45")
                $fecha_de_vencimiento = date("d-m-Y",strtotime(date("d-m-Y")."+ 45 days")); 
            else
                $fecha_de_vencimiento = date("d-m-Y",strtotime(date("d-m-Y")."+ 60 days")); 


            $cabecera["operacion"] = $header->operacion;
            $cabecera["tipo_de_comprobante"] = $header->tipo_de_comprobante;
            $cabecera["serie"] = $header->serie;
            $cabecera["numero"] = $header->numero;
            // $cabecera["numero"] = str_pad($header->numero, 4, "0", STR_PAD_LEFT);
            $cabecera["sunat_transaction"] = 1;
            $cabecera["cliente_tipo_de_documento"] = $header->cliente_tipo_de_documento;
            $cabecera["cliente_numero_de_documento"] = $header->cliente_numero_de_documento;
            $cabecera["cliente_denominacion"] = $header->cliente_denominacion;
            $cabecera["cliente_direccion"] = $header->cliente_direccion;
            $cabecera["cliente_email"] = $header->cliente_email;
            $cabecera["cliente_email_1"] = "";
            $cabecera["cliente_email_2"] = "";
            $cabecera["fecha_de_emision"] = date("d-m-Y");
            $cabecera["fecha_de_vencimiento"] = $fecha_de_vencimiento;
            $cabecera["moneda"] = 1;
            $cabecera["tipo_de_cambio"] = "";
            $cabecera["porcentaje_de_igv"] = "18.00";
            $cabecera["descuento_global"] =  floatval($header->descuento_global);
            $cabecera["total_descuento"] =  floatval($header->total_descuento);
            $cabecera["total_anticipo"] = "";

            if(floatval($header->total_gravada)>0){
                $cabecera["total_gravada"] = floatval($header->total_gravada);
            }else{
                $cabecera["total_gravada"] = "";
            }

            if(floatval($header->total_inafecta)>0){
                $cabecera["total_inafecta"] = floatval($header->total_inafecta);
            }else{
                $cabecera["total_inafecta"] = "";
            }

            if(floatval($header->total_exonerada)>0){
                $cabecera["total_exonerada"] = floatval($header->total_exonerada);
            }else{
                $cabecera["total_exonerada"] = "";
            }

            if(floatval($header->total_gratuita)>0){
                $cabecera["total_gratuita"] = floatval($header->total_gratuita);
            }else{
                $cabecera["total_gratuita"] = "";
            }

            if(floatval($header->total_igv)>0){
                $cabecera["total_igv"] = floatval($header->total_igv);
            }else{
                $cabecera["total_igv"] = "";
            }

            $cabecera["total_otros_cargos"] = "";
            $cabecera["total"] = $header->total;
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
            $cabecera["condiciones_de_pago"] = $header->condiciones_de_pago;
            $cabecera["medio_de_pago"] = $header->medio_de_pago;
            $cabecera["placa_vehiculo"] = "";
            $cabecera["orden_compra_servicio"] = $header->orden_compra_servicio;
            $cabecera["tabla_personalizada_codigo"] = "";
            $cabecera["formato_de_pdf"] = "A4";

            $items = array();

            $bolsa = 0;

            foreach ($details as $value) {
                /**
                 * Obtener Código Sunat
                 */
                $producto_taxonomia = $objConn->consulta_arreglo("SELECT * FROM producto_taxonomiap WHERE id_producto = {$value->codigo} AND id_taxonomiap = -1");
                $valor_sunat = $producto_taxonomia['valor'];
                $separar = explode("_",$valor_sunat);
                $codigo = $separar[0];
                $plastico = $objConn->consulta_arreglo("SELECT * FROM ley_plastico WHERE id_producto = {$value->codigo}");
                if(is_array($plastico)){
                    $bolsa += $value->cantidad * $config["impuesto_bolsa"];
                }
                //----------------------------
                $item = array();
                $item["unidad_de_medida"] = $value->unidad_de_medida;
                $item["codigo"] = $value->codigo;
                $item["descripcion"] = $value->descripcion;
                $item["cantidad"] = $value->cantidad;
                $item["codigo_producto_sunat"] = $codigo;
                $item["valor_unitario"] = $value->valor_unitario;
                $item["precio_unitario"] = $value->precio_unitario;
                $item["descuento"] = $value->descuento;
                $item["subtotal"] = $value->subtotal;
                $item["tipo_de_igv"] = $value->tipo_de_igv;
                $item["igv"] = $value->igv;
                $item["total"] = $value->total;
                $item["anticipo_regularizacion"] = $value->anticipo_regularizacion;
                $item["anticipo_documento_serie"] = $value->anticipo_documento_serie;

                $items[] = $item;
            }

            $cabecera["items"] = $items;

            // $guia["guia_serie_numero"]= $header->guia_serie_numero;
            
            // $header->guia_serie_numero= $header->guia_serie_numero;

            if($header->guia_serie_numero != '-'){
                $guias = array();
                $guia = array();                
                $guia["guia_tipo"]= 1;                                
                $guiaNumAux=$config['serie_guia_remision']."-".$header->guia_serie_numero;                
                $guia["guia_serie_numero"]= $guiaNumAux;
                $guias[] = $guia;
                $cabecera["guias"] = $guias;
            }

            $cabecera["total_impuestos_bolsas"] = floatval($bolsa);

            // print_r($cabecera);

            $data_json = json_encode($cabecera);

            $respuesta = _curl($objventa, $data_json, $header, $idBoleta);

            echo $respuesta;
            // var_dump($respuesta);
            break;

        case 'descargaFact':
            $header = json_decode($_POST['header']);
            $cabecera = array();

            $cabecera["operacion"] = $header->operacion;
            $cabecera["tipo_de_comprobante"] = $header->tipo_de_comprobante;
            $cabecera["serie"] = $header->serie;
            $cabecera["numero"] = $header->numero;

            // echo print_r($cabecera);
            $data_json = json_encode($cabecera);


            $config = $objventa->consulta_arreglo("Select * from configuracion");
            $ruta = $config["ruta"];
            $token = $config["token"];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ruta);
            curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, array(
                    'Authorization: Token token="'.$token.'"',
                    'Content-Type: application/json',
                    )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuesta  = curl_exec($ch);
            echo $respuesta;
            break;

        case 'AnulaFactCotos':
            $respuesta=['success'=>false,'message'=>'No se pudo realizar la Operacion'];
            $res='';
            $header = json_decode($_POST['header']);
            if($header->tipo_de_comprobante==2){ // BOLETA
                $documento = $objventa_medio_pago->consulta_arreglo("SELECT * FROM boleta where id=".$header->numero."");
                $res = $objventa_medio_pago->consulta_arreglo("SELECT * FROM venta WHERE id=".$documento['id_venta']."");
            }
            if($header->tipo_de_comprobante==1){ // FACTURA
                $documento = $objventa_medio_pago->consulta_arreglo("SELECT * FROM factura where id=".$header->numero."");
                $res = $objventa_medio_pago->consulta_arreglo("SELECT * FROM venta WHERE id=".$documento['id_venta']."");
            }            
            
            $valida_fecha = _valida_fecha($res['fecha_hora']);
            if ($valida_fecha['success'] == true) {
                $consulta = _consultar_documento($objventa, $res['id'], $res['tipo_comprobante']);
                    if ($consulta['success'] == true) {   

                        $cabecera = array();
            
                        $cabecera["operacion"] = $header->operacion;
                        $cabecera["tipo_de_comprobante"] = $header->tipo_de_comprobante;
                        $cabecera["serie"] = $header->serie;
                        $cabecera["numero"] = $header->numero;
                        $cabecera["motivo"] = "ERROR DE SISTEMA";
            
                        $data_json = json_encode($cabecera);
            
            
                        $config = $objventa->consulta_arreglo("Select * from configuracion");
                        $ruta = $config["ruta"];
                        $token = $config["token"];
            
                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_URL, $ruta);
                        curl_setopt(
                                $ch, CURLOPT_HTTPHEADER, array(
                                'Authorization: Token token="'.$token.'"',
                                'Content-Type: application/json',
                                )
                        );
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                        curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        $respuestaEliminacion  = curl_exec($ch);
                        if ($respuestaEliminacion != "") {
                            $n = (array) json_decode(stripslashes($respuestaEliminacion));
                            if( !isset($n["errors"])){
                                $dd=$objventa->consulta_simple("UPDATE venta SET tipo_comprobante = 0 WHERE id =".$res['id']."");                                
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
                    }else {
                        $respuesta['success'] = false;
                        $respuesta['message'] = $consulta['message'];
                    }                                
            }else{
                $respuesta['success'] = false;
                $respuesta['message'] = $valida_fecha['message'];
            }
            echo json_encode($respuesta);
        break;

         case 'AnulaFact':
            $header = json_decode($_POST['header']);
            $cabecera = array();

            $cabecera["operacion"] = $header->operacion;
            $cabecera["tipo_de_comprobante"] = $header->tipo_de_comprobante;
            $cabecera["serie"] = $header->serie;
            $cabecera["numero"] = $header->numero;
            $cabecera["motivo"] = "ERROR DE SISTEMA";

            $data_json = json_encode($cabecera);


            $config = $objventa->consulta_arreglo("Select * from configuracion");
            $ruta = $config["ruta"];
            $token = $config["token"];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $ruta);
            curl_setopt(
                    $ch, CURLOPT_HTTPHEADER, array(
                    'Authorization: Token token="'.$token.'"',
                    'Content-Type: application/json',
                    )
            );
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $respuesta  = curl_exec($ch);
            
            echo $respuesta;
            break;

        case 'CambiaStatus':

            $qr = "UPDATE venta SET tipo_comprobante = 0 WHERE id = '".$_POST['id_boleta']."'";
            echo $objventa->consulta_simple($qr);

            break;

        case 'editarCantidad':

            $get = "SELECT * FROM producto_venta WHERE estado_fila= 1 AND  id = '".$_POST['prod']."'";
            // echo $get;
            $res = $objventa->consulta_arreglo($get);

            $total =  $res["precio"] * floatval($_POST['cantidad']);

            $qr = "UPDATE producto_venta SET cantidad = '".$_POST['cantidad']."', total = $total WHERE id = '".$_POST['prod']."'";

            // $qr;

            echo $objventa->consulta_simple($qr);

            break;

        case 'editarCantidadServicio':

            $get = "SELECT * FROM servicio_venta WHERE id = '".$_POST['prod']."'";
            // echo $get;
            $res = $objventa->consulta_arreglo($get);

            $total =  $res["precio"] * floatval($_POST['cantidad']);

            $qr = "UPDATE servicio_venta SET cantidad = '".$_POST['cantidad']."', total = $total WHERE id = '".$_POST['prod']."'";

            // $qr;

            echo $objventa->consulta_simple($qr);

            break;

        case 'editarPrecio':

            $get = "SELECT * FROM producto_venta WHERE estado_fila= 1 AND  id = '".$_POST['prod']."'";
            $res = $objventa->consulta_arreglo($get);

            $total =  $res["cantidad"] * floatval($_POST['precio']);

            $qr = "UPDATE producto_venta SET precio = '".$_POST['precio']."', total = $total WHERE id = '".$_POST['prod']."'";

            echo $objventa->consulta_simple($qr);

            break;

        case 'editarPrecioServicio':

            $get = "SELECT * FROM servicio_venta WHERE id = '".$_POST['prod']."'";
            $res = $objventa->consulta_arreglo($get);

            $total =  $res["cantidad"] * floatval($_POST['precio']);

            $qr = "UPDATE servicio_venta SET precio = '".$_POST['precio']."', total = $total WHERE id = '".$_POST['prod']."'";

            echo $objventa->consulta_simple($qr);

            break;
            
        case 'totalItems':

            $get = "SELECT * FROM producto_venta WHERE estado_fila= 1 AND  id = '".$_POST['prod']."'";
            $res = $objventa->consulta_arreglo($get);

            $precio =   floatval($_POST['precio'])/$res["cantidad"];

            $qr = "UPDATE producto_venta SET precio = ".$precio.", total = ".$_POST['precio']." WHERE id = '".$_POST['prod']."'";

            echo $objventa->consulta_simple($qr);

            break;

        case 'endImprime':

            // echo "Insert into cola_impresion values(NULL, {$_POST['id']}, '{$_POST["tipoImprime"]}', {$_POST["caja"]}, '')";
            // $qr = $objventa->consulta_simple("Insert into cola_impresion values(NULL, {$_POST['id']}, '{$_POST["tipoImprime"]}', {$_POST["caja"]}, '')");
            // 

            $qr = $objventa->consulta_simple("Insert into cola_impresion values(NULL, {$_POST['id']}, '{$_POST["tipoImprime"]}', {$_POST["caja"]}, '', 1)");
            
            echo $qr;

        break;

        case 'nuevo_pago':

            $res = $objventa_medio_pago->searchDB($_POST['id'], 'id', 1);
            $ventaPago = $res[0];
            $ventaDelMedioPago = $objventa->searchDB($ventaPago['id_venta'], 'id', 1);
            $venta = $ventaDelMedioPago[0];

            $monto = $_POST['monto'];
            $all_query_ok = true;

            if($monto < $ventaPago['monto']){

                $nuevoMonto = number_format($ventaPago['monto'],2,'.','') - number_format($monto,2,'.','');

                $objventa_medio_pago->setVar('id', $_POST['id']);
                $objventa_medio_pago->setVar('monto', $nuevoMonto);
                $objventa_medio_pago->updateDB() ? null : $all_query_ok = false;

                $objventa_medio_pago_i = new venta_medio_pago();
                $objventa_medio_pago_i->setVar('id_venta', $ventaPago['id_venta']);
                $objventa_medio_pago_i->setVar('medio', $_POST['medio']);
                $objventa_medio_pago_i->setVar('monto', $monto);
                $objventa_medio_pago_i->setVar('moneda', 'PEN');
                $objventa_medio_pago_i->setVar('vuelto', 0);
                $objventa_medio_pago_i->setVar('estado_fila', "1");
                $id = $objventa_medio_pago_i->insertDB();
                $id ? null : $all_query_ok = false;

                $medioPago = $_POST['medio']."_COBRO";

                $objconn = new venta();
                $series = $objconn->consulta_arreglo("Select * from configuracion where id = 1");
                $objconn->consulta_simple("Insert into movimiento_caja values(NULL,{$venta['id_caja']},{$monto},'EXT|PEN|{$medioPago}|{$id}','".date("Y-m-d H:i:s")."','".$series['fecha_cierre']."','{$venta['id_turno']}','{$venta['id_usuario']}','1')") ? null : $all_query_ok = false;
                $objconn->consulta_simple("UPDATE movimiento_caja set monto = {$nuevoMonto} WHERE tipo_movimiento like '%CREDITO|{$ventaPago['id']}%'") ? null : $all_query_ok = false;
            
                $all_query_ok ? $con->commit() : $con->rollback();

                echo json_encode(["respuesta" => $all_query_ok]);

            }else if($monto >= $ventaPago['monto']){
                

                $nuevoMonto = number_format($ventaPago['monto'],2,'.','') - number_format($monto,2,'.','');

                $objventa_medio_pago->setVar('id', $_POST['id']);
                $objventa_medio_pago->setVar('monto', 0);
                $objventa_medio_pago->setVar('medio', $_POST['medio']);
                $objventa_medio_pago->setVar('vuelto', $nuevoMonto * -1);
                $objventa_medio_pago->updateDB() ? null : $all_query_ok = false;

                $medioPago = $_POST['medio']."_COBRO";
                
                $objconn = new venta();
                $series = $objconn->consulta_arreglo("Select * from configuracion where id = 1");
                $movimiento = $objconn->consulta_arreglo("SELECT id FROM movimiento_caja WHERE tipo_movimiento LIKE '%|CREDITO|{$ventaPago['id']}'");
                $objconn->consulta_simple("UPDATE movimiento_caja set tipo_movimiento = 'EXT|PEN|{$medioPago}|{$ventaPago['id']}', fecha_cierre = '{$series['fecha_cierre']}' WHERE id = {$movimiento['id']}") ? null : $all_query_ok = false;
                
                $boleta = null;
                $factura = null;
                /**Verificar si la venta es boleta */
                $boleta = $objconn->consulta_matriz("SELECT id FROM boleta WHERE id_venta = {$ventaPago['id_venta']}");
                
                if(is_array($boleta)){
                    $objconn->consulta_simple("UPDATE venta SET tipo_comprobante = 1/*, fecha_cierre = '{$series['fecha_cierre']}'*/ WHERE id = {$ventaPago['id_venta']}") ? null : $all_query_ok = false;
                }else{
                    $factura = $objconn->consulta_matriz("SELECT id FROM factura WHERE id_venta = ".$ventaPago['id_venta']);
                    if (is_array($factura)){
                        $objconn->consulta_simple("UPDATE venta SET tipo_comprobante = 2/*, fecha_cierre = '{$series['fecha_cierre']}'*/ WHERE id = {$ventaPago['id_venta']}") ? null : $all_query_ok = false ;
                   }
                }

                if(!is_array($boleta) && !is_array($factura)){
                    $objconn->consulta_simple("UPDATE venta SET tipo_comprobante = 0/*, fecha_cierre = '{$series['fecha_cierre']}'*/ WHERE id = {$ventaPago['id_venta']}") ? null : $all_query_ok = false;
                }
                
                $all_query_ok ? $con->commit() : $con->rollback();

                echo json_encode(["respuesta" => $all_query_ok]);
            }
        break;

        case 'updatePagos':
            $objconn = new venta();

            $vmp = $objconn->consulta_arreglo("SELECT * FROM venta_medio_pago WHERE id_venta = ".$_POST["id"]);

            if($vmp["medio"] != "NOTACREDITO"){

                $objconn->consulta_simple("UPDATE venta_medio_pago SET vuelto = ".$_POST['vuelto']." WHERE id_venta = ".$_POST["id"]);

                $monto = $vmp["monto"] - $vmp["vuelto"];
                if(is_array($vmp)){
                    if($vmp['id'] != ""){
                        $objconn->consulta_simple("UPDATE movimiento_caja SET monto = ".$monto." WHERE tipo_movimiento like '%|".$vmp["id"]."%'");
                    }
                }
            }  
        break;

        case 'descuentos_diario':
            $objconn = new venta();

            $vmp = $objconn->consulta_arreglo("SELECT ROUND(SUM(monto),2) as descuento
                FROM venta v
                INNER JOIN venta_medio_pago vm ON vm.id_venta = v.id
                WHERE v.fecha_cierre = '".$_POST['fecha_cierre']."' AND medio = 'DESCUENTO'");

            echo json_encode($vmp);
        break;

        case 'exchange':
            $objconn = new venta();

            // echo "UPDATE venta_medio_pago SET medio = '".$_POST['medio']."' WHERE id = ".$_POST["id"];
            $objconn->consulta_simple("UPDATE venta_medio_pago SET medio = '".$_POST['medio']."' WHERE id = ".$_POST["id"]);

            $objconn->consulta_simple("UPDATE movimiento_caja SET tipo_movimiento = 'SELL|PEN|".$_POST['medio']."|".$_POST["id"]."' WHERE tipo_movimiento like '%|".$_POST["id"]."'");
            echo json_encode(1);
        break;

        case 'consultarfacturacion':
            $objconn = new venta();
            $serie = "";

            $bol =  $objconn->consulta_arreglo("SELECT * FROM boleta WHERE id_venta = ".$_POST["id"]);
            if(is_array($bol)){
                $serie = $bol["serie"]."-".$bol["id"];
            }else{
                $fac =  $objconn->consulta_arreglo("SELECT * FROM factura WHERE id_venta = ".$_POST["id"]);
                if(is_array($fac)){
                    $serie = $fac["serie"]."-".$fac["id"];
                }else{
                    $serie = "Ticket-".$_POST["id"];
                }
            }

            echo json_encode($serie);

        break;
    }
}

//Victor Moreno estuvo aquí
//La tuya por si acaso

function _curl($objventa, $data_json, $header, $idBoleta){
    
    $config = $objventa->consulta_arreglo("Select * from configuracion");
    $ruta = $config["ruta"];
    $token = $config["token"];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $ruta);
    curl_setopt(
        $ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Token token="'.$token.'"',
        'Content-Type: application/json',
        )
    );
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS,$data_json);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $respuesta  = curl_exec($ch);
    $cabecera = json_decode($data_json, true);
    $leer_respuesta = json_decode($respuesta, true);
    if(intval(curl_errno($ch)) === 0){
        curl_close($ch);
        if (isset($leer_respuesta['errors'])) {
            $qr = "Insert into comprobante_hash values('".$header->numero."','NO',' ',' ','".$leer_respuesta['errors']."')";
            $objventa->consulta_simple($qr);
            //Evaluamos si ya existe
            if(json_decode($respuesta, true)['codigo'] === 23){
                //Aumentamos el número y volvemos a intentar hasta el infinito a pedido de gino
                // var_dump($cabecera);
                $cabecera['numero']++;
                $data_json = json_encode($cabecera);
                $respuesta = _curl($objventa, $data_json, $header, $idBoleta);
            }
        } else {
            $aceptada = "NO";
            if(boolval($leer_respuesta["aceptada_por_sunat"])){
                $aceptada = "SI";
            }
            $qr = "Insert into comprobante_hash values('".$header->numero."','".$aceptada."','".$leer_respuesta["codigo_hash"]."','".$leer_respuesta["cadena_para_codigo_qr"]."','".$leer_respuesta['sunat_description']."')";
            $objventa->consulta_simple($qr);

            $queryUpdateBoleta = "Update boleta set id = ".$cabecera['numero']." where id = $idBoleta";
            $objventa->consulta_simple($queryUpdateBoleta);
        }
    }else{
        curl_close($ch);
        $qr = "Insert into comprobante_hash values('".$header->numero."','NE',' ',' ','')";
        $objventa->consulta_simple($qr);
    }

    return $respuesta;
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
?>