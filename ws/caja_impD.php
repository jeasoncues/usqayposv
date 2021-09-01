<?php
require_once('../api/classes/PosPrinter.php');
require_once('../nucleo/caja.php');
$objcaja = new caja();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objcaja->setVar('id', $_POST['id']);
            $objcaja->setVar('nombre', $_POST['nombre']);
            $objcaja->setVar('ubicacion', $_POST['ubicacion']);
            $objcaja->setVar('serie_impresora', $_POST['serie_impresora']);
            $objcaja->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objcaja->insertDB());
            break;

        case 'mod':
            $objcaja->setVar('id', $_POST['id']);
            $objcaja->setVar('nombre', $_POST['nombre']);
            $objcaja->setVar('ubicacion', $_POST['ubicacion']);
            $objcaja->setVar('serie_impresora', $_POST['serie_impresora']);
            $objcaja->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objcaja->updateDB());
            break;

        case 'del':
            $objcaja->setVar('id', $_POST['id']);
            echo json_encode($objcaja->deleteDB());
            break;

        case 'get':
            $res = $objcaja->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objcaja->listDB();
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objcaja->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
       case 'cierreprint':
       
            $objconn = new caja();
        
            //$res = $objconn->consulta_simple("Insert into cola_impresion values(NULL,'".$_POST['fecha']."', 'CIE','".$_POST["id_caja"]."','".$_POST["turno"].",".$_POST["caja"].",".$_POST["id_usuario"]."', 1)");
            $verificaImpresion = $objconn->consulta_arreglo("SELECT * FROM configuracion_impresion WHERE caja='" . $_COOKIE["id_caja"] . "' AND opcion='CIE' ");
            
            if (isset($verificaImpresion['id'])) {
                $res = 1;
                $printer = $objconn->consulta_arreglo("SELECT * FROM impresoras WHERE caja='" . $_COOKIE["id_caja"] . "' AND nombre='" . $verificaImpresion['impresora'] . "'");
                $printerName = $verificaImpresion['impresora'];
                $receipt = "CIERRE" . "\n\n";
                $pos_printer = new PosPrinter(0, $printerName, $receipt);
                $success = true;
                $id_caja = $_COOKIE["id_caja"];
                $msg = "VENTA EXITOSA";

                /*$res_turno = $objconn->consulta_arreglo("Select nombre from turno where id = {$_POST['turno']}");
                $res_caja = $objconn->consulta_arreglo("Select nombre from caja where id = {$_POST['id_caja']}");
                $res_usuario = $objconn->consulta_arreglo("Select nombres_y_apellidos from usuario where id = {$_POST['id_usuario']}");

                $config = $objconn->consulta_arreglo("Select * from configuracion where id=1");
                $fecha_cierre = $config["fecha_cierre"];
                $cambio = $objconn->consulta_arreglo("Select * from tipo_cambio where moneda_origen = 'USD' AND moneda_destino = 'PEN'");

                if (isset($_POST['fecha']) && !empty($_POST['fecha'])) {
                    $fecha_cierre = $_POST['fecha'];
                }*/

                $res_turno = $objconn->consulta_arreglo("Select nombre from turno where id = {$_POST['turno']}");
                $res_caja = $objconn->consulta_arreglo("Select nombre from caja where id = {$_POST['id_caja']}");
                $res_usuario = $objconn->consulta_arreglo("Select nombres_y_apellidos from usuario where id = {$_POST['id_usuario']}");
                      
                    
                $config = $objconn->consulta_arreglo("Select * from configuracion where id=1");
                $fecha_cierre = $config["fecha_cierre"];
                 if (isset($_POST['fecha']) && !empty($_POST['fecha'])) {
                    $fecha_cierre = $_POST['fecha'];
                }
                $cambio = $objconn->consulta_arreglo("Select * from tipo_cambio where moneda_origen = 'USD' AND moneda_destino = 'PEN'");

                
                    $total_vendido = 0;
                    $efectivo = 0;
                    $visa = 0;
                    $master = 0;
                    $inicial = 0;
                    $adicional = 0;
                    $salidas = 0;
                    $soles = 0;
                    $dolares = 0;
                    $caja = 0;
                    $credito = 0;
                    $cobro = 0;
                    $salidas_cobro = 0;
                    $movimientos2 = 0;
                    $notacred = 0;
                    $caja_u = 0;
                    $liquidaciones = 0;
                    $descuentos = 0;
                    $detraccion=0;
                    $detraccion_visa=0;
                    $detraccion_mastercard=0;
                    $detraccion_efectivo=0;
                    $descuento=0;
                    $cobro_visa = 0;
                    $cobro_mastercard = 0;
                    $cobro_deposito = 0;
                    $cobro_efectivo = 0;
                    $inicial = 0;
                    $venta=0;
                    $buy=0;
                    $movimientos = null;
                    $sql = "";
                    $total_boleta=0;
                    $total_factura=0;
                    $total_ticket=0;
                    $total_boleta_discount=0;
                    $total_factura_discount=0;
                    $total_ticket_discount=0;
                    $sql_caja = "Select * from caja";
                    $ResCaja = $objconn->consulta_matriz($sql_caja);
                    $wherein = "(";
                    foreach ($ResCaja as $c) {
                        $wherein .= $c["id"].","; 
                    }
                    $wherein = substr($wherein, 0, -1);
                    $wherein .= ")";

                    $pruebacotos="";
                    // echo $_COOKIE['id_caja'];
                   
                    if(isset($_POST["turno"])){
                        $sqlVenta = "SELECT * from venta where estado_fila=1 AND fecha_cierre = '".$_POST["fecha"]."'";
                        $sql = "SELECT * from movimiento_caja where tipo_movimiento NOT LIKE '%DESCUENTO%' AND estado_fila=1 AND fecha_cierre = '".$_POST["fecha"]."'";
                        $sqlDescuento = "SELECT medio, vm.monto
                                FROM venta v, venta_medio_pago vm
                                WHERE v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND vm.medio = 'DESCUENTO' ";
                        $sqlNull = "SELECT vm.id as conc
                                    FROM venta v
                                    INNER JOIN venta_medio_pago vm ON v.id = vm.id_venta 
                                    WHERE total is null AND v.fecha_cierre = '".$fecha_cierre."'";
                        $sqlticket = 
                                "SELECT v.total
                                FROM venta v
                                WHERE v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 0 AND v.estado_fila=1";
                        $sqlboleta = 
                                "SELECT v.total
                                FROM venta v
                                WHERE v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 1 AND v.estado_fila=1";
                        $sqlfactura = 
                                "SELECT v.total
                                FROM venta v
                                WHERE  v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante =2 AND v.estado_fila=1";
                        $sqlticketDiscount = 
                                "SELECT v.total,vm.monto
                                FROM venta v, venta_medio_pago vm
                                WHERE  v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 0 and v.estado_fila=1 AND vm.medio = 'DESCUENTO'";
                        $sqlboletaDiscount = 
                            "SELECT v.total,vm.monto
                                FROM venta v, venta_medio_pago vm
                                WHERE  v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 1 and v.estado_fila=1 AND vm.medio = 'DESCUENTO'";
                        $sqlfacturaDiscount = 
                                "SELECT v.total,vm.monto
                                FROM venta v, venta_medio_pago vm
                                WHERE  v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 2 and v.estado_fila=1 AND vm.medio = 'DESCUENTO'";

                        $open = "SELECT * FROM movimiento_caja WHERE estado_fila=1 AND tipo_movimiento like '%OPEN|PEN|EFECTIVO%' AND fecha_cierre = '".$fecha_cierre."' ";
                    
                            
                    // }
                        if(intval($_POST["turno"])>0){
                            $sqlDescuento .= " AND v.id_turno = '".$_POST["turno"]."'";
                            $sql .= " AND id_turno = '".$_POST["turno"]."'";
                            $sqlNull .= " AND v.id_turno = '".$_POST["turno"]."'";
                            $open .= " AND id_turno = '".$_POST["turno"]."'";
                            $sqlVenta .= " AND id_turno = '".$_POST["turno"]."'";
                            $sqlticket .= " AND v.id_turno = '".$_POST["turno"]."'";
                            $sqlboleta .= " AND v.id_turno = '".$_POST["turno"]."'";
                            $sqlfactura .= " AND v.id_turno = '".$_POST["turno"]."'";
                            $sqlticketDiscount .= " AND v.id_turno = '".$_POST["turno"]."'";
                            $sqlboletaDiscount .= " AND v.id_turno = '".$_POST["turno"]."'";
                            $sqlfacturaDiscount .= " AND v.id_turno = '".$_POST["turno"]."'";
                        }
                        if(intval($_POST["caja"])>0){
                            $sql .= " AND id_caja = '".$_POST["caja"]."'";
                            $sqlNull .= " AND v.id_caja = '".$_POST["caja"]."'";
                            $sqlDescuento .= " AND v.id_caja = '".$_POST["caja"]."'";
                            $open .= " AND id_caja = '".$_POST["caja"]."'";
                            $sqlVenta .= " AND id_caja = '".$_POST["caja"]."'";
                            $sqlticket .= " AND v.id_caja = '".$_POST["caja"]."'";
                            $sqlboleta .= " AND v.id_caja = '".$_POST["caja"]."'";
                            $sqlfactura .= " AND v.id_caja = '".$_POST["caja"]."'";
                            $sqlticketDiscount .= " AND v.id_caja = '".$_POST["caja"]."'";
                            $sqlboletaDiscount .= " AND v.id_caja = '".$_POST["caja"]."'";
                            $sqlfacturaDiscount .= " AND v.id_caja = '".$_POST["caja"]."'";

                        }
                        $sql.=" GROUP BY fecha";

                        $movimientos = $objconn->consulta_matriz($sql);
                        $movimientosNull = $objconn->consulta_matriz($sqlNull);
                        $movimientosVenta = $objconn->consulta_matriz($sqlVenta);
                        $movimientosDescuento = $objconn->consulta_matriz($sqlDescuento);
                        $movimientosTotalTicket = $objconn->consulta_matriz($sqlticket);
                        $movimientosTotalBoleta = $objconn->consulta_matriz($sqlboleta);
                        $movimientosTotalFactura = $objconn->consulta_matriz($sqlfactura);
                        $movimientosTotalTicketDiscount = $objconn->consulta_matriz($sqlticketDiscount);
                        $movimientosTotalBoletaDiscount = $objconn->consulta_matriz($sqlboletaDiscount);
                        $movimientosTotalFacturaDiscount = $objconn->consulta_matriz($sqlfacturaDiscount);
                        $montoOpen = $objconn->consulta_arreglo($open);

                    /* $sql2 = "SELECT * from movimiento_caja where estado_fila=1 AND fecha_cierre = '".$fecha_cierre."' AND id_caja = 0" ;
                        $sql2.=" GROUP BY fecha";
                        $movimientos2 = $objconn->consulta_matriz($sql2);*/
                    }else{
                        $sqlVenta = "SELECT * from venta where estado_fila=1 AND fecha_cierre = '".$fecha_cierre."'";
                        $sql = "SELECT * from movimiento_caja where estado_fila=1 AND fecha_cierre = '".$fecha_cierre."'";
                        $sqlNull = "SELECT vm.id as conc
                                    FROM venta v
                                    INNER JOIN venta_medio_pago vm ON v.id = vm.id_venta 
                                    WHERE total is null AND v.fecha_cierre = '".$fecha_cierre."' ";
                        $sqlDescuento = 
                                "SELECT medio, vm.monto
                                FROM venta v, venta_medio_pago vm
                                WHERE v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND vm.medio = 'DESCUENTO' ";
                        $sqlticket = 
                                "SELECT v.total
                                FROM venta v
                                WHERE  v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 0 and v.estado_fila=1";
                        $sqlboleta = 
                                "SELECT v.total
                                FROM venta v
                                WHERE  v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 1 and v.estado_fila=1";
                        $sqlfactura = 
                                "SELECT v.total
                                FROM venta v
                                WHERE v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante =2 and v.estado_fila=1";

                        $sqlticketDiscount = 
                                "SELECT v.total,vm.monto
                                FROM venta v, venta_medio_pago vm
                                WHERE  v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 0 and v.estado_fila=1 AND vm.medio = 'DESCUENTO'";
                        $sqlboletaDiscount = 
                            "SELECT v.total,vm.monto
                                FROM venta v, venta_medio_pago vm
                                WHERE  v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 1 and v.estado_fila=1 AND vm.medio = 'DESCUENTO'";
                        $sqlfacturaDiscount = 
                                "SELECT v.total,vm.monto
                                FROM venta v, venta_medio_pago vm
                                WHERE  v.id = vm.id_venta AND v.fecha_cierre = '".$fecha_cierre."' AND v.tipo_comprobante = 2 and v.estado_fila=1 AND vm.medio = 'DESCUENTO'";
                         $open = "SELECT * FROM movimiento_caja WHERE estado_fila=1 AND tipo_movimiento like '%OPEN|PEN|EFECTIVO%' AND fecha_cierre = '".$fecha_cierre."' ";

                        if(intval($_POST["caja"])>0){
                            $sql .= " AND id_caja = '".$_POST["caja"]."'";
                            $sqlNull .= " AND v.id_caja = '".$_POST["caja"]."'";
                            $sqlDescuento .= " AND v.id_caja = '".$_POST["caja"]."'";
                            $open .= " AND id_caja = '".$_POST["id_caja"]."'";
                            $sqlticket .= " AND id_caja = '".$_POST["id_caja"]."'";
                            $sqlboleta .= "AND id_caja = '".$_POST["id_caja"]."'";
                            $sqlfactura .= " AND id_caja = '".$_POST["id_caja"]."'";
                            $sqlticketDiscount .= " AND id_caja = '".$_POST["caja"]."'";
                            $sqlboletaDiscount .= " AND id_caja = '".$_POST["caja"]."'";
                            $sqlfacturaDiscount .= " AND id_caja = '".$_POST["caja"]."'";
                            $sqlVenta .= " AND id_caja = '".$_POST["caja"]."'";
                        }
                        $sql.=" GROUP BY fecha";      

                        $movimientos = $objconn->consulta_matriz($sql);
                        $movimientosNull = $objconn->consulta_matriz($sqlNull);
                        $movimientosVenta = $objconn->consulta_matriz($sqlVenta);
                        $movimientosDescuento = $objconn->consulta_matriz($sqlDescuento);
                        $movimientosTotalTicket = $objconn->consulta_matriz($sqlticket);
                        $movimientosTotalBoleta = $objconn->consulta_matriz($sqlboleta);
                        $movimientosTotalFactura = $objconn->consulta_matriz($sqlfactura);
                        $movimientosTotalTicketDiscount = $objconn->consulta_matriz($sqlticketDiscount);
                        $movimientosTotalBoletaDiscount = $objconn->consulta_matriz($sqlboletaDiscount);
                        $movimientosTotalFacturaDiscount = $objconn->consulta_matriz($sqlfacturaDiscount);
                        $montoOpen = $objconn->consulta_arreglo($open);


                        // $sqlCre = "SELECT vm.id, medio, monto
                        //     FROM venta v, venta_medio_pago vm
                        //     WHERE tipo_comprobante = -1 AND fecha_cierre = '".$fecha_cierre."' AND v.id = vm.id_venta";

                        // $movimientosCreditos = $objconn->consulta_matriz($sqlCre);

                        // for($i=0; $i<count($movimientosCreditos); $i++){
                        //     if($movimientosCreditos[$i]["medio"] != 'CREDITO'){
                        //         $monto += $movimientosCreditos[$i]["monto"];
                        //     }
                        // }

                       /* $sql2 = "SELECT * from movimiento_caja where estado_fila=1 AND fecha_cierre = '".$fecha_cierre."' AND id_caja = 0" ;
                        $sql2.=" GROUP BY fecha"; 
                        $movimientos2 = $objconn->consulta_matriz($sql2);*/

                    }
                    if(!empty($montoOpen["monto"])){
                        $inicial = $montoOpen["monto"];
                    }

                        if(intval($_POST["turno"]) > 0){
                            $turno = " AND id_turno = ".$_POST["turno"];
                        }else{
                            $turno = " ";
                        }

                        if(intval($_POST["caja"]) > 0){
                            $cajasql = " AND mc.monto < 0 AND mc.id_caja = ".$_POST["caja"];
                        }else{
                            $cajasql = " AND mc.monto < 0";
                        }
                    // $inicial = $MvMount["monto"];

                    if(!empty($montoOpen["monto"])){
                        $inicial = $montoOpen["monto"];
                    }
                        
                    $query_liquidado = "SELECT SUM(ROUND(monto,2)) as liquidaciones from movimiento_caja where estado_fila=1 AND fecha_cierre = '".$fecha_cierre."' AND tipo_movimiento like '%LIQ%'";
                    if(isset($_POST['caja']) && intval($_POST["caja"])>0){
                        $query_liquidado .= " AND id_caja = '".$_POST["caja"]."'";
                    }else{
                        $sql .= " AND id_caja IN ".$wherein;
                    }
                    $monto_liquidado = $objconn->consulta_arreglo($query_liquidado);
                    if($monto_liquidado['liquidaciones'] == null){
                        $liquidaciones = 0;
                    }else{
                        $liquidaciones = $monto_liquidado['liquidaciones'];
                    }

                    if(is_array($movimientosDescuento)){
                        foreach ($movimientosDescuento as $mvDesc){
                            $descuento += $mvDesc["monto"];
                        }
                    }
                    if(is_array($movimientosVenta)){
                        foreach ($movimientosVenta as $mvVenta){
                            if ($mvVenta["total"]!=null) {
                                $venta += $mvVenta["total"];
                            }
                        
                        }
                    
                    }

                    if(is_array($movimientosNull)){
                        foreach ($movimientosNull as $mv3){

                            $movimientosCajaNull = $objconn->consulta_matriz("SELECT tipo_movimiento, mc.monto
                            FROM movimiento_caja mc 
                            WHERE estado_fila=1 AND tipo_movimiento LIKE CONCAT('%|', ".$mv3["conc"]." ,'%')");

                            if(is_array($movimientosCajaNull)){
                                foreach ($movimientosCajaNull as $mv4) {
                                    if(strpos($mv4["tipo_movimiento"],"SELL") !== FALSE){
                                        $total_vendido = $total_vendido - floatval($mv4["monto"]);
                                    }
                                    if(strpos($mv4["tipo_movimiento"],"_COBRO") !== FALSE){
                                        $cobro = $cobro - floatval($mv4["monto"]);

                                        if(strpos($mv4["tipo_movimiento"],"VISA_COBRO") !== FALSE){
                                            $cobro_visa = $cobro_visa + floatval($mv4["monto"]);
                                        }
                                        if(strpos($mv4["tipo_movimiento"],"MASTERCARD_COBRO") !== FALSE){
                                            $cobro_mastercard = $cobro_mastercard + floatval($mv4["monto"]);
                                        }
                                        if(strpos($mv4["tipo_movimiento"],"DEPOSITO_COBRO") !== FALSE){
                                            $cobro_deposito = $cobro_deposito + floatval($mv4["monto"]);
                                        }
                                        if(strpos($mv4["tipo_movimiento"],"EFECTIVO_COBRO") !== FALSE){
                                            $cobro_efectivo = $cobro_efectivo + floatval($mv4["monto"]);
                                        }

                                    }
                                    if((strpos($mv4["tipo_movimiento"],"EFECTIVO") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
                                        $efectivo = $efectivo - floatval($mv4["monto"]);
                                    }
                                    if((strpos($mv4["tipo_movimiento"],"VISA") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
                                        $visa = $visa + floatval($mv4["monto"]);
                                    }      
                                    if((strpos($mv4["tipo_movimiento"],"MASTERCARD") !== FALSE) && (strpos($mv4["tipo_movimiento"],"SELL") !== FALSE)){
                                        $master = $master + floatval($mv4["monto"]);
                                    }
                                }
                            }
                        }
                    }

                    if(is_array($movimientos)){
                        foreach ($movimientos as $mv){
                        
                        //echo "/\n".$mv["id"]."*".floatval($mv["monto"]);
                            if(strpos($mv["tipo_movimiento"],"SELL") !== FALSE) {
                            //  $total_vendido += floatval(abs($mv["monto"]));
                                // echo "---".$total_vendido;
                            }
                           if(strpos($mv["tipo_movimiento"],"_COBRO") !== FALSE){
                                if(strpos($mv["tipo_movimiento"],"EXT") !== FALSE){
                                     $buy=abs(floatval($mv["monto"]));
                                }else{
                                    $cobro += abs(floatval($mv["monto"]));

                                    if(strpos($mv["tipo_movimiento"],"VISA_COBRO") !== FALSE){
                                        $cobro_visa += abs(floatval($mv["monto"]));
                                    }
                                    if(strpos($mv["tipo_movimiento"],"MASTERCARD_COBRO") !== FALSE){
                                        $cobro_mastercard += abs(floatval($mv["monto"]));
                                    }
                                    if(strpos($mv["tipo_movimiento"],"DEPOSITO_COBRO") !== FALSE){
                                        $cobro_deposito += abs(floatval($mv["monto"]));
                                    }
                                    if(strpos($mv["tipo_movimiento"],"EFECTIVO_COBRO") !== FALSE){
                                        $cobro_efectivo += abs(floatval($mv["monto"]));
                                    }
                                }
                            }
                            
                            if(strpos($mv["tipo_movimiento"],"_DETRACCION") !== FALSE){
                                $detraccion += floatval($mv["monto"]);

                                if(strpos($mv["tipo_movimiento"],"VISA_DETRACCION") !== FALSE){
                                    $detraccion_visa += floatval($mv["monto"]);
                                }
                                if(strpos($mv["tipo_movimiento"],"MASTERCARD_DETRACCION") !== FALSE){
                                    $detraccion_mastercard += floatval($mv["monto"]);
                                }
                                if(strpos($mv["tipo_movimiento"],"EFECTIVO_DETRACCION") !== FALSE){
                                    $detraccion_efectivo += floatval($mv["monto"]);
                                }
                            }
                        
                            if((strpos($mv["tipo_movimiento"],"EFECTIVO") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){


                                // echo $efectivo ."+". floatval($mv["monto"]) ."=".$efectivo + floatval($mv["monto"])." \n\n <br>";
                                $efectivo += floatval($mv["monto"]);

                                // echo floatval($mv["monto"])." \n\n <br>";

                            }
                            if((strpos($mv["tipo_movimiento"],"VISA") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){
                                $visa += floatval($mv["monto"]);
                            }      
                            if((strpos($mv["tipo_movimiento"],"MASTERCARD") !== FALSE) && (strpos($mv["tipo_movimiento"],"SELL") !== FALSE)){
                                $master += floatval($mv["monto"]);
                            }
                            // if(strpos($mv["tipo_movimiento"],"OPEN") !== FALSE){
                            //     $inicial = $inicial + floatval($mv["monto"]);
                            // }
                            if(strpos($mv["tipo_movimiento"],"INBX") !== FALSE){
                                $adicional += floatval($mv["monto"]);
                            }
                            if(strpos($mv["tipo_movimiento"],"OUTBX") !== FALSE){
                                $salidas += floatval($mv["monto"]);
                            }
                            if(strpos($mv["tipo_movimiento"],"SELL|PEN|EFECTIVO") !== FALSE){
                                $soles += floatval($mv["monto"]);
                            }
                            if(strpos($mv["tipo_movimiento"],"PEN|DESCUENTO") !== FALSE){
                                $descuentos += floatval($mv["monto"]);
                            }
                            if(strpos($mv["tipo_movimiento"],"USD|EFECTIVO") !== FALSE){
                                $dolares += floatval($mv["monto"]);
                            }
                            if(strpos($mv["tipo_movimiento"],"BUY") !== FALSE){
                                if(strpos($mv["tipo_movimiento"],"EXT") !== FALSE){
                                $buy += abs(floatval($mv["monto"]));
                                }else{
                                    $salidas += abs(floatval($mv["monto"]));
                                }
                            }
                            if(strpos($mv["tipo_movimiento"],"PEN|CREDITO") !== FALSE){
                                $credito += floatval($mv["monto"]);
                            }
                            if(strpos($mv["tipo_movimiento"],"PEN|NOTACREDITO") !== FALSE){
                                $notacred += floatval($mv["monto"]);
                            }
                            if(strpos($mv["tipo_movimiento"],"EXT") !== FALSE){
                                    $pruebacotos= $pruebacotos."|".$mv["monto"];
                                    $salidas_cobro = $salidas_cobro + floatval($mv["monto"]);
                                }
                        }
                    // echo "venta--". $venta;
                  
                        $total_vendido= $venta-$descuento;
                        $efectivo =  $total_vendido-$visa-$master-$detraccion_efectivo;
                        $soles =  ($efectivo + $inicial + $adicional) -abs($salidas)- $liquidaciones-$detraccion_efectivo;
                        $dolares = $dolares/floatval($cambio["compra"]);
                        //Restar Liquidaciones
                        $caja = ($efectivo + $dolares + $inicial + $adicional) -abs($salidas) - $liquidaciones-$detraccion_efectivo;
                        $caja_u = ($efectivo + $dolares + $adicional) - abs($salidas)  - $liquidaciones;
                          
                    }

                    if(is_array($movimientosTotalTicket)){
                        foreach ($movimientosTotalTicket as $t){
                                $total_ticket += floatval($t["total"]);
                        }
                    }
                    if(is_array($movimientosTotalBoleta)){
                        foreach ($movimientosTotalBoleta as $b){
                                $total_boleta = $total_boleta + floatval($b["total"]);
                        }
                    }
                    if(is_array($movimientosTotalFactura)){
                        foreach ($movimientosTotalFactura as $f){
                                $total_factura = $total_factura + floatval($f["total"]);
                        }
                    }
                    if(is_array($movimientosTotalTicketDiscount)){
                        foreach ($movimientosTotalTicketDiscount as $td){
                                $total_ticket_discount += floatval($td["monto"]);
                        }
                    }
                    if(is_array($movimientosTotalBoletaDiscount)){
                        foreach ($movimientosTotalBoletaDiscount as $bd){
                                $total_boleta_discount += floatval($bd["monto"]);
                        }
                    }
                    if(is_array($movimientosTotalFacturaDiscount)){
                        foreach ($movimientosTotalFacturaDiscount as $fd){
                                $total_factura_discount += floatval($df["monto"]);
                        }
                    }

                       


                $gastos = $objconn->consulta_matriz("
                    SELECT mc.monto as monto, descripcion
                    FROM movimiento_caja mc
                    INNER JOIN entrada_salida es ON mc.id = es.id_movimiento_caja
                    where fecha_cierre = '" . $fecha_cierre . "' " . $cajasql . " " . $turno . " ");
                $hoy = date('d-m-Y');
                $hora = date('H:i:s');
                $texto = "IMPRESO POR {$res_usuario['nombres_y_apellidos']} El {$hoy} A LAS {$hora}";
                $MAX_DETALLES = 28;
                $MAX_MONTOS = 8;

             
                $items = [];

                $items[] = new Item(str_pad("Total Ticket", $MAX_DETALLES,' ',STR_PAD_RIGHT),  ($total_ticket-$total_ticket_discount) > 0 ? str_pad(number_format(($total_ticket-$total_ticket_discount),2), $MAX_MONTOS,' ',STR_PAD_LEFT): str_pad('0.00', $MAX_MONTOS,' ',STR_PAD_LEFT));
                $items[] = new Item(str_pad("Total Boleta", $MAX_DETALLES,' ',STR_PAD_RIGHT),  ($total_boleta-$total_boleta_discount) > 0 ? str_pad(number_format(($total_boleta-$total_boleta_discount),2), $MAX_MONTOS,' ',STR_PAD_LEFT): str_pad('0.00', $MAX_MONTOS,' ',STR_PAD_LEFT));
                $items[] = new Item(str_pad("Total Factura", $MAX_DETALLES,' ',STR_PAD_RIGHT), ($total_factura-$total_factura_discount) > 0 ? str_pad(number_format(($total_factura-$total_factura_discount),2), $MAX_MONTOS,' ',STR_PAD_LEFT): str_pad('0.00', $MAX_MONTOS,' ',STR_PAD_LEFT));
                $items[] = new Item(str_pad("Total Vendido", $MAX_DETALLES,' ',STR_PAD_RIGHT), str_pad(number_format(($total_vendido+ $cobro)-$notacred-$detraccion,2), $MAX_MONTOS,' ',STR_PAD_LEFT));
                $items[] = new Item(str_pad("Efectivo", $MAX_DETALLES,' ',STR_PAD_RIGHT), str_pad(number_format($efectivo, 2), $MAX_MONTOS,' ',STR_PAD_LEFT));
                $items[] = new Item(str_pad("Visa", $MAX_DETALLES,' ',STR_PAD_RIGHT),str_pad(number_format($visa, 2), $MAX_MONTOS,' ',STR_PAD_LEFT));
                $items[] = new Item(str_pad("MasterCard", $MAX_DETALLES,' ',STR_PAD_RIGHT), str_pad(number_format($master, 2), $MAX_MONTOS,' ',STR_PAD_LEFT));
                $items[] = new Item(str_pad("Monto Inicial", $MAX_DETALLES,' ',STR_PAD_RIGHT), str_pad(number_format($inicial, 2), $MAX_MONTOS,' ',STR_PAD_LEFT));
                $items[] = new Item(str_pad("Ingresos Adicionales", $MAX_DETALLES,' ',STR_PAD_RIGHT), str_pad(number_format($adicional, 2), $MAX_MONTOS,' ',STR_PAD_LEFT));
                $items[] = new Item(str_pad("Salidas", $MAX_DETALLES,' ',STR_PAD_RIGHT), str_pad(number_format(abs($salidas), 2), $MAX_MONTOS,' ',STR_PAD_LEFT));
                if (is_array($gastos)) {
                    foreach ($gastos as $p) {
                        $items[] = new Item(str_pad('--'.utf8_decode($p['descripcion']).' ', $MAX_DETALLES,' ',STR_PAD_RIGHT), str_pad(number_format(abs($p['monto']), 2), $MAX_MONTOS,' ',STR_PAD_LEFT));
                    }
                }     
            
            if(is_array($movimientos)){
                foreach ($movimientos as $mv){
                    if(strpos($mv["tipo_movimiento"],"BUY") !== FALSE){
                        if(strpos($mv["tipo_movimiento"],"EXT") !== FALSE){
                        }else{
                             $items[] = new Item(str_pad('-- Saidas de caja', $MAX_DETALLES .' ',' ',STR_PAD_RIGHT), str_pad(number_format(abs($mv['monto']), 2), $MAX_MONTOS,' ',STR_PAD_LEFT));
                        }
                    }
                }
            }
                                                                               
                $items[] = new Item(str_pad("Descuentos", $MAX_DETALLES,' ',STR_PAD_RIGHT), str_pad(number_format($descuento, 2), $MAX_MONTOS,' ',STR_PAD_LEFT));
                $items[] = new Item(str_pad("Soles Caja", $MAX_DETALLES,' ',STR_PAD_RIGHT), str_pad(number_format($soles, 2), $MAX_MONTOS,' ',STR_PAD_LEFT));
                $items[] = new Item(str_pad("Utilidad Caja", $MAX_DETALLES,' ',STR_PAD_RIGHT), str_pad(number_format($caja_u, 2), $MAX_MONTOS,' ',STR_PAD_LEFT));
                $items[] = new Item(str_pad("Detraccion", $MAX_DETALLES,' ',STR_PAD_RIGHT), str_pad(number_format($detraccion, 2), $MAX_MONTOS,' ',STR_PAD_LEFT));
              
                foreach ($items as $c) {
                       echo $c."\n";
                    }
                try {

                    if ($printer['red'] == 1) {
                        $pos_printer->connectTypeNetwork($printerName);
                    } else {
                        $pos_printer->connectTypeWindows($printerName);
                    }

                    $pos_printer
                        ->setShopName('ticket')
                        ->setTitleCierre($fecha_cierre, $_POST["turno"], $id_caja)
                        ->setItemsCierre($items)
                        ->setFooterCierre($texto)
                        ->cut()
                        ->pulse();
                } catch (Exception $e) {
                    $success = false;
                    $msg = $e->getMessage();
                    $res = 0;
                } finally {
                    $pos_printer->close();
                }

                
            }else{
                $res="ocurrio un error";
               
            }
           
            echo json_encode($res);
            break;
    }
}
