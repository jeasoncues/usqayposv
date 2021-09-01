<?php

require '../vendor/autoload.php';

require_once('../nucleo/movimiento_caja.php');
$objmovimiento_caja = new movimiento_caja();

require_once('../nucleo/caja.php');
$objcaja = new caja();

require_once('../nucleo/turno.php');
$objturno = new turno();

require_once('../nucleo/usuario.php');
$objusuario = new usuario();

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            if ($_POST['monto']==10) {
               $monto = 77;
            }else{
                $monto = 99;
            }
            $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");
            $objmovimiento_caja->setVar('id', $_POST['id']);
            $objmovimiento_caja->setVar('id_caja', $_POST['id_caja']);
            $objmovimiento_caja->setVar('monto',$monto );
            $objmovimiento_caja->setVar('tipo_movimiento', $_POST['tipo_movimiento']);
            $objmovimiento_caja->setVar('fecha', $_POST['fecha']);
            $objmovimiento_caja->setVar('fecha_cierre', $cierre_act["fecha_cierre"]);
            $objmovimiento_caja->setVar('id_turno', $_POST['id_turno']);
            $objmovimiento_caja->setVar('id_usuario', $_POST['id_usuario']);
            $objmovimiento_caja->setVar('estado_fila', $_POST['estado_fila']);
           
            echo json_encode($objmovimiento_caja->insertDB());
            break;
        
        case 'addin':
            //Obtenemos fecha cierre y turno
            $objconn = new turno();
            $miturno = "Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'";

            $turno_act = $objconn->consulta_arreglo($miturno);
            $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");
            
            $objmovimiento_caja = new movimiento_caja();
            $objmovimiento_caja->setVar('id_caja', $_POST['id_caja']);
            $objmovimiento_caja->setVar('monto', $_POST['monto']);
            $objmovimiento_caja->setVar('tipo_movimiento', $_POST['tipo_movimiento']);
            $objmovimiento_caja->setVar('fecha', date("Y-m-d H:i:s"));
            $objmovimiento_caja->setVar('fecha_cierre', $cierre_act["fecha_cierre"]);
            $objmovimiento_caja->setVar('id_turno', $turno_act["id"]);
            $objmovimiento_caja->setVar('id_usuario', $_POST['id_usuario']);
            $objmovimiento_caja->setVar('estado_fila',"1");
            
            $idmov = $objmovimiento_caja->insertDB();
            
            $idjb = $objconn->consulta_id("Insert into entrada_salida values(NULL,'".$_POST["monto_abs"]."','".$_POST["tipo"]."','".$_POST["descripcion"]."','".$idmov."',1)");

            
            // $objconn->consulta_simple("Insert into tabla_impresion values(NULL,'".$_POST['id_caja']."','".$idjb."','PAGO',0)");

            $objconn->consulta_simple("Insert into cola_impresion values(NULL, {$idjb}, 'PAG', {$_POST["id_caja"]}, '', 1)");

            echo json_encode($idmov);
        break;

        case 'addpayment':
            //Obtenemos fecha cierre y turno
            $objconn = new turno();
            $miturno = "Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'";

            $turno_act = $objconn->consulta_arreglo($miturno);
            $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");
            
            $objmovimientos_caja = new movimiento_caja();
            $objmovimientos_caja->setVar('id_caja', $_POST['id_caja']);
            $objmovimientos_caja->setVar('monto', $_POST['monto']);
            $objmovimientos_caja->setVar('tipo_movimiento', $_POST['tipo_movimiento']."|".$_POST['comprobante']);
            $objmovimientos_caja->setVar('fecha', date("Y-m-d H:i:s"));
            $objmovimientos_caja->setVar('fecha_cierre', $cierre_act["fecha_cierre"]);
            $objmovimientos_caja->setVar('id_turno', $turno_act["id"]);
            $objmovimientos_caja->setVar('id_usuario', $_POST['id_usuario']);
            $objmovimientos_caja->setVar('estado_fila',"1");
            
            $idmov = $objmovimientos_caja->insertDB();
            
            $idjb = $objconn->consulta_id("Insert into entrada_salida values(NULL,'".$_POST["monto_abs"]."','Pago Externo','".$_POST["descripcion"]."','".$idmov."',1)");

            $objconn->consulta_simple("Insert into tabla_impresion values(NULL,'".$_POST['id_caja']."','".$idjb."','PAGO',0)");

            $objconn->consulta_simple("Insert into cola_impresion values(NULL, {$idjb}, 'PAG', {$_POST["id_caja"]}, '', 1)");

            echo json_encode($idmov);
        break;

        case 'editpayment':
            //Obtenemos fecha cierre y turno
            $objconn = new turno();
            $miturno = "Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'";

            $turno_act = $objconn->consulta_arreglo($miturno);
            $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");

            $objmovimiento_caja->setVar('id', $_POST['id']);
            $objmovimiento_caja->setVar('id_caja', $_POST['id_caja']);
            $objmovimiento_caja->setVar('monto', $_POST['monto']);
            $objmovimiento_caja->setVar('tipo_movimiento', $_POST['tipo_movimiento']."|".$_POST['comprobante']);
            $objmovimiento_caja->setVar('fecha', date("Y-m-d H:i:s"));
            $objmovimiento_caja->setVar('fecha_cierre', $cierre_act["fecha_cierre"]);
            $objmovimiento_caja->setVar('id_turno', $turno_act["id"]);
            $objmovimiento_caja->setVar('id_usuario', $_POST['id_usuario']);
            $objmovimiento_caja->setVar('estado_fila',"1");
            
            $id = $objmovimiento_caja->updateDB();
            
            if( $id > 0 ){
                $objconn->consulta_simple("UPDATE entrada_salida set descripcion = '".$_POST['descripcion']."' WHERE id_movimiento_caja = '".$_POST['id']."'");
            }

            echo json_encode($id);

        break;

        case 'liquidar':
            //Obtenemos fecha cierre y turno
        
            $objconn = new turno();
            $miturno = "Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'";

            $turno_act = $objconn->consulta_arreglo($miturno);
            $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");
            $fech_hora = date("Y-m-d H:i:s");

            $objmovimientos_caja = new movimiento_caja();
            $objmovimientos_caja->setVar('id_caja', $_COOKIE['id_caja']);
            $objmovimientos_caja->setVar('monto', $_POST['total']);
            $objmovimientos_caja->setVar('tipo_movimiento', 'LIQ|PEN|EFECTIVO|PX');
            $objmovimientos_caja->setVar('fecha', $fech_hora );
            $objmovimientos_caja->setVar('fecha_cierre', $cierre_act["fecha_cierre"]);
            $objmovimientos_caja->setVar('id_turno', $turno_act["id"]);
            $objmovimientos_caja->setVar('id_usuario', $_COOKIE['id_usuario']);
            $objmovimientos_caja->setVar('estado_fila',"1");
            
            $idmov = $objmovimientos_caja->insertDB();
            
            $idjb = $objconn->consulta_id("Insert into entrada_salida values(NULL,'".$_POST["total"]."','Liquidacion','Liquidacion ".$fech_hora."','".$idmov."',1)");

            $objconn->consulta_simple("Insert into tabla_impresion values(NULL,'".$_COOKIE['id_caja']."','".$idjb."','CAJA',0)");

            $objconn->consulta_simple("Insert into cola_impresion values(NULL, {$idjb}, 'PAG', {$_COOKIE["id_caja"]}, '', 1)");

            echo json_encode($idmov);
            
        break;

        case 'apertura':
            $objconn = new turno();
            $miturno = "Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'";
            $turno_act = $objconn->consulta_arreglo($miturno);
            
            $objconn->consulta_simple("Update configuracion set fecha_cierre = '".date("Y-m-d")."' where id=1");
            
            if(floatval($_POST["inicial"])>0){
                $sql = "Insert into movimiento_caja values(NULL,'".$_POST["id_caja"]."','".$_POST["inicial"]."','OPEN|PEN|EFECTIVO','".date("Y-m-d H:i:s")."','".date("Y-m-d")."','".$turno_act["id"]."','".$_POST["id_usuario"]."','1')";                     
                $objconn->consulta_simple($sql);
            }
            
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES=0;");
            $objconn->consulta_simple("UPDATE tipo_cambio set compra = '".$_POST["compra"]."', venta = '".$_POST["venta"]."' where moneda_origen = 'USD' AND moneda_destino = 'PEN'");
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES=1;");
            
            echo json_encode("1");
        break;

        case 'nueva_apertura':
            $fecha_tiempo = date("Y-m-d H:i:s", strtotime($_POST['fecha']));
            $fecha = date("Y-m-d", strtotime($_POST['fecha']));
            
            $objconn = new turno();
            $miturno = "Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'";
            $turno_act = $objconn->consulta_arreglo($miturno);
            
            $objconn->consulta_simple("Update configuracion set fecha_cierre = '".$fecha."' where id=1");
            
            if(floatval($_POST["monto_inicial"])>0){
                $sql = "Insert into movimiento_caja values(NULL,'".$_COOKIE['id_caja']."','".$_POST["monto_inicial"]."','OPEN|PEN|EFECTIVO','".$fecha_tiempo."','".$fecha."','".$turno_act["id"]."','".$_COOKIE["id_usuario"]."','1')";
                $objconn->consulta_simple($sql);
            }
            
            echo json_encode("1");
        break;

        case 'Mod_apertura':
            $objconn = new turno();
            $miturno = "Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'";
            $turno_act = $objconn->consulta_arreglo($miturno);
            
            $objconn->consulta_simple("Update configuracion set fecha_cierre = '".date("Y-m-d")."' where id=1");
            
            if(floatval($_POST["inicial"])>0){
                $sql = "UPDATE movimiento_caja SET monto = '".$_POST["inicial"]."' WHERE id = ".$_POST["id"]."";                     
                $objconn->consulta_simple($sql);
            }
            
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES=0;");
            $objconn->consulta_simple("UPDATE tipo_cambio set compra = '".$_POST["compra"]."', venta = '".$_POST["venta"]."' where moneda_origen = 'USD' AND moneda_destino = 'PEN'");
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES=1;");
            
            echo json_encode("1");
            break;
        
        

        case 'mod':

            $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");
            $objmovimiento_caja->setVar('id', $_POST['id']);
            $objmovimiento_caja->setVar('id_caja', $_POST['id_caja']);
            $objmovimiento_caja->setVar('monto', $_POST['monto']);
            $objmovimiento_caja->setVar('tipo_movimiento', $_POST['tipo_movimiento']);
            $objmovimiento_caja->setVar('fecha', $_POST['fecha']);
            $objmovimiento_caja->setVar('fecha_cierre', $cierre_act['fecha_cierre']);
            $objmovimiento_caja->setVar('id_turno', $_POST['id_turno']);
            $objmovimiento_caja->setVar('id_usuario', $_POST['id_usuario']);
            $objmovimiento_caja->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objmovimiento_caja->updateDB());
            break;

        case 'del':
            $objmovimiento_caja->setVar('id', $_POST['id']);
            echo json_encode($objmovimiento_caja->deleteDB());
            break;

        case 'get':
            $res = $objmovimiento_caja->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_caja'] = $objcaja->searchDB($res[0]['id_caja'], 'id', 1);
                $res[0]['id_caja'] = $res[0]['id_caja'][0];
                $res[0]['id_turno'] = $objturno->searchDB($res[0]['id_turno'], 'id', 1);
                $res[0]['id_turno'] = $res[0]['id_turno'][0];
                $res[0]['id_usuario'] = $objusuario->searchDB($res[0]['id_usuario'], 'id', 1);
                $res[0]['id_usuario'] = $res[0]['id_usuario'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
        break;

        case 'get_':
            $res = $objmovimiento_caja->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_caja'] = $objcaja->searchDB($res[0]['id_caja'], 'id', 1);
                $res[0]['id_caja'] = $res[0]['id_caja'][0];
                $res[0]['id_turno'] = $objturno->searchDB($res[0]['id_turno'], 'id', 1);
                $res[0]['id_turno'] = $res[0]['id_turno'][0];
                $res[0]['id_usuario'] = $objusuario->searchDB($res[0]['id_usuario'], 'id', 1);
                $res[0]['id_usuario'] = $res[0]['id_usuario'][0];
                $res[0]['descripcion'] = $objturno->consulta_arreglo("SELECT * FROM entrada_salida WHERE id_movimiento_caja = '".$res[0]['id']."'");
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
        break;

        case 'list':
            $res = $objmovimiento_caja->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_caja'] = $objcaja->searchDB($act['id_caja'], 'id', 1);
                    $act['id_caja'] = $act['id_caja'][0];
                    $act['id_turno'] = $objturno->searchDB($act['id_turno'], 'id', 1);
                    $act['id_turno'] = $act['id_turno'][0];
                    $act['id_usuario'] = $objusuario->searchDB($act['id_usuario'], 'id', 1);
                    $act['id_usuario'] = $act['id_usuario'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objmovimiento_caja->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_caja'] = $objcaja->searchDB($act['id_caja'], 'id', 1);
                    $act['id_caja'] = $act['id_caja'][0];
                    $act['id_turno'] = $objturno->searchDB($act['id_turno'], 'id', 1);
                    $act['id_turno'] = $act['id_turno'][0];
                    $act['id_usuario'] = $objusuario->searchDB($act['id_usuario'], 'id', 1);
                    $act['id_usuario'] = $act['id_usuario'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'move':
            $objmovimiento_caja = new movimiento_caja();
            $filtro = $objmovimiento_caja->consulta_matriz("SELECT mc.`id`, c.`nombre`, mc.`monto`, `tipo_movimiento`, descripcion, `fecha`, `fecha_cierre`, `id_turno`, `id_usuario` 
                FROM `movimiento_caja` mc 
                INNER JOIN caja c ON mc.id_caja = c.id 
                INNER JOIN entrada_salida es ON mc.id = es.id_movimiento_caja
                WHERE tipo_movimiento like '".$_POST['mov']."'");

            if (is_array($filtro)) {
                echo json_encode($filtro);
            } else {
                echo json_encode(0);
            }
        break;

        case 'josef':

            $data = [];

            $meses = array(null, "Enero","Febrero","Marzo", "Abril", "Mayo", "Junio",
            "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");

            $movimientos_caja = [];
            $egresos = [];

            foreach ($meses as $k => $mes) {
                
                if( !is_null($mes) ){
                    
                    $movimiento = $objcaja->consulta_arreglo("SELECT FORMAT(SUM(monto),2) AS y, MONTH(fecha) as label FROM movimiento_caja where tipo_movimiento NOT LIKE '%PX%' AND YEAR(fecha) = YEAR(NOW()) AND MONTH(fecha) = '".$k."' GROUP by MONTH(fecha)");
                    
                    $egreso = $objcaja->consulta_arreglo("SELECT SUM(ABS(monto)) as y, MONTH(fecha) as label FROM movimiento_caja where tipo_movimiento LIKE '%PX%' AND YEAR(fecha) = YEAR(NOW()) AND MONTH(fecha) = '".$k."' GROUP by MONTH(fecha)");
                    
                    if( $movimiento ){
                        $movimientos_caja[] = $movimiento;
                    }else{
                        $movimientos_caja[] = array( "y" => 0 , "label" => $k );
                    }

                    if( $egreso ){
                        $egresos[] = $egreso;
                    }else{
                        $egresos[] = array( "y" => 0 , "label" => $k );
                    }
                    
                    
                }
            }
            
            $data['movimientos_caja'] = $movimientos_caja;
            $data['egresos'] = $egresos;
            
            
            foreach ($data['movimientos_caja'] as $key => &$mov) {
                $mov['label'] = $meses[$mov['label']];
                $mov['indexLabel'] = $mov['y'];
            }

            foreach ($data['egresos'] as $key => &$eg) {
                $eg['label'] = $meses[$eg['label']];
                $eg['indexLabel'] = $eg['y'];
            }
            

            echo json_encode($data);
            

        break;

        case 'show_liq':
            $liquidaciones = $objmovimiento_caja->consulta_matriz(
            "SELECT * FROM movimiento_caja WHERE tipo_movimiento like '%LIQ%' AND fecha_cierre 
            BETWEEN '".$_POST['starts']."' AND '".$_POST['ends']."'");

            if( is_array($liquidaciones) ){

                foreach ($liquidaciones as $key => &$liquidacion) {
                    $liquidacion['caja'] = $objmovimiento_caja->consulta_arreglo("SELECT * FROM caja WHERE id = '".$liquidacion['id_caja']."'");
                    $liquidacion['usuario'] = $objmovimiento_caja->consulta_arreglo("SELECT * FROM usuario WHERE id = '".$liquidacion['id_usuario']."'");
                }

            }
        
            echo json_encode($liquidaciones);

        break;

        case 'show_cuadre':
            
            $saldo_anterior = $objmovimiento_caja->consulta_arreglo("SELECT ROUND(SUM(monto), 2) AS saldo FROM movimiento_caja WHERE tipo_movimiento LIKE '%PX%' AND fecha_cierre < '{$_POST["starts"]}'")[0];

            $movimientos = $objmovimiento_caja->consulta_matriz(
            "SELECT * FROM movimiento_caja WHERE tipo_movimiento like '%PX%' AND fecha_cierre 
            BETWEEN '".$_POST['starts']."' AND '".$_POST['ends']."'");

            if( is_array($movimientos) ){

                foreach ($movimientos as $key => &$movimiento) {
                    $movimiento['caja'] = $objmovimiento_caja->consulta_arreglo("SELECT * FROM caja WHERE id = '".$movimiento['id_caja']."'");
                    $movimiento['usuario'] = $objmovimiento_caja->consulta_arreglo("SELECT * FROM usuario WHERE id = '".$movimiento['id_usuario']."'");
                    $movimiento['descripcion'] = $objmovimiento_caja->consulta_arreglo("SELECT * FROM entrada_salida WHERE id_movimiento_caja = '".$movimiento['id']."'");
                    $tipo_mov = explode("|", $movimiento['tipo_movimiento']);
                    if( $tipo_mov[0] == 'EG' ){
                        $movimiento['tipo'] = array("class" => "danger", "tipo" => "EGRESO");
                    }else if(  $tipo_mov[0] == 'LIQ' ){
                        $movimiento['tipo'] = array("class" => "success", "tipo" => "LIQUIDACION");
                    }else if(  $tipo_mov[0] == 'ADV' ){
                        $movimiento['tipo'] = array("class" => "danger", "tipo" => "ADELANTO");
                    }
                    $movimiento['saldo_anterior'] = $saldo_anterior;
                }

            }
            echo json_encode($movimientos);

        break;


        case 'import':
           
            $alphas = range('A', 'D');

            $liquidaciones = $objmovimiento_caja->consulta_matriz(
                "SELECT * FROM movimiento_caja WHERE tipo_movimiento like '%LIQ%' AND fecha_cierre 
                BETWEEN '".$_POST['starts_']."' AND '".$_POST['ends_']."'");
    
                if( is_array($liquidaciones) ){
    
                    foreach ($liquidaciones as $key => &$liquidacion) {
                        $liquidacion['caja'] = $objmovimiento_caja->consulta_arreglo("SELECT * FROM caja WHERE id = '".$liquidacion['id_caja']."'");
                        $liquidacion['usuario'] = $objmovimiento_caja->consulta_arreglo("SELECT * FROM usuario WHERE id = '".$liquidacion['id_usuario']."'");
                    }
    
                }
           
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue($alphas[0].'1', 'Fecha');
            $sheet->setCellValue($alphas[1].'1', 'Caja');
            $sheet->setCellValue($alphas[2].'1', 'Usuario');
            $sheet->setCellValue($alphas[3].'1', 'Monto');
            
            $indice = 2;
            if( is_array($liquidaciones) ){    
                foreach ($liquidaciones as $k => $value) {
                    $sheet->setCellValue($alphas[0].$indice, $value['fecha']);
                    $sheet->setCellValue($alphas[1].$indice, $value['caja']['nombre']);
                    $sheet->setCellValue($alphas[2].$indice, $value['usuario']['nombres_y_apellidos']);
                    $sheet->setCellValue($alphas[3].$indice, $value['monto']);
                    $indice++;
                }
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'liquidaciones';

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output'); // download file

        break;

        case 'import_cuadre':
           
            $alphas = range('A', 'F');

            $movimientos = $objmovimiento_caja->consulta_matriz(
                "SELECT * FROM movimiento_caja WHERE tipo_movimiento like '%PX%' AND fecha_cierre 
                BETWEEN '".$_POST['starts_']."' AND '".$_POST['ends_']."'");
        
                if( is_array($movimientos) ){
        
                    foreach ($movimientos as $key => &$movimiento) {
                        $movimiento['caja'] = $objmovimiento_caja->consulta_arreglo("SELECT * FROM caja WHERE id = '".$movimiento['id_caja']."'");
                        $movimiento['usuario'] = $objmovimiento_caja->consulta_arreglo("SELECT * FROM usuario WHERE id = '".$movimiento['id_usuario']."'");
                        $movimiento['descripcion'] = $objmovimiento_caja->consulta_arreglo("SELECT * FROM entrada_salida WHERE id_movimiento_caja = '".$movimiento['id']."'");
                        $tipo_mov = explode("|", $movimiento['tipo_movimiento']);
                        if( $tipo_mov[0] == 'EG' ){
                            $movimiento['tipo'] = array("class" => "danger", "tipo" => "EGRESO");
                        }else if(  $tipo_mov[0] == 'LIQ' ){
                            $movimiento['tipo'] = array("class" => "success", "tipo" => "LIQUIDACION");
                        }else if(  $tipo_mov[0] == 'ADV' ){
                            $movimiento['tipo'] = array("class" => "danger", "tipo" => "ADELANTO");
                        }
                    }
        
                }
           
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue($alphas[0].'1', 'Fecha');
            $sheet->setCellValue($alphas[1].'1', 'Caja');
            $sheet->setCellValue($alphas[2].'1', 'Usuario');
            $sheet->setCellValue($alphas[3].'1', 'Descripcion');
            $sheet->setCellValue($alphas[4].'1', 'Tipo');
            $sheet->setCellValue($alphas[5].'1', 'Monto');
            
            $indice = 2;
            if( is_array($movimientos) ){    
                foreach ($movimientos as $k => $value) {
                    $sheet->setCellValue($alphas[0].$indice, $value['fecha']);
                    $sheet->setCellValue($alphas[1].$indice, $value['caja']['nombre']);
                    $sheet->setCellValue($alphas[2].$indice, $value['usuario']['nombres_y_apellidos']);
                    $sheet->setCellValue($alphas[3].$indice, $value['descripcion']['descripcion']);
                    $sheet->setCellValue($alphas[4].$indice, $value['tipo']['tipo']);
                    $sheet->setCellValue($alphas[5].$indice, abs($value['monto']));
                    $indice++;
                }
            }

            $writer = new Xlsx($spreadsheet);
            $filename = 'cuadre_general';

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output'); // download file

        break;

        case 'consulta':
            $medios = $objmovimiento_caja->consulta_matriz("
                SELECT * FROM venta
                ");
            $resta = 0;


            // echo json_encode($medios);
            for($i=0; $i<count($medios); $i++){
                $fecha_new = date('Y-m-d', strtotime($medios[$i]['fecha_hora']));

                $mod = $objmovimiento_caja->consulta_simple("UPDATE venta SET fecha_cierre = '".$fecha_new."' WHERE id = ".$medios[$i]["id"]);

                echo $mod;
            }
            // $medios = $objmovimiento_caja->consulta_matriz("
            //     SELECT * FROM venta_medio_pago
            //     ");
            // $resta = 0;
            // for($i=0; $i<count($medios); $i++){
            //     $resta = floatval($medios[$i]["monto"] - $medios[$i]["vuelto"]);


            //     $mc = $objmovimiento_caja->consulta_arreglo("
            //         SELECT * FROM movimiento_caja WHERE tipo_movimiento like '%|".$medios[$i]["id"]."'
            //     ");

            //     $mod = $objmovimiento_caja->consulta_simple("
            //         UPDATE movimiento_caja SET monto = ".$resta." WHERE tipo_movimiento like '%|".$medios[$i]["id"]."'");

            //     echo $mod;
            // }

        break;

    }
}?>