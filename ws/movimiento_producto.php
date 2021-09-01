 <?php
include_once('../nucleo/include/MasterConexion.php');

require_once('../nucleo/movimiento_producto.php');
$objmovimiento_producto = new movimiento_producto();

require_once('../nucleo/producto.php');
$objproducto = new producto();

require_once('../nucleo/almacen.php');
$objalmacen = new almacen();

require_once('../nucleo/usuario.php');
$objusuario = new usuario();

require_once('../nucleo/turno.php');
$objturno = new turno();

require_once('../nucleo/guia_movimiento.php');
$objguia_movimiento = new guia_movimiento();

require_once('../nucleo/guia_producto.php');
$objguia_producto = new guia_producto();

require_once('../nucleo/proveedor.php');
$objproveedor = new proveedor();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objmovimiento_producto->setVar('id', $_POST['id']);
            $objmovimiento_producto->setVar('id_producto', $_POST['id_producto']);
            $objmovimiento_producto->setVar('id_almacen', $_POST['id_almacen']);
            $objmovimiento_producto->setVar('cantidad', $_POST['cantidad']);
            $objmovimiento_producto->setVar('costo', $_POST['costo']);
            $objmovimiento_producto->setVar('tipo_movimiento', $_POST['tipo_movimiento']);
            $objmovimiento_producto->setVar('id_usuario', $_POST['id_usuario']);
            $objmovimiento_producto->setVar('id_turno', $_POST['id_turno']);
            $objmovimiento_producto->setVar('fecha', $_POST['fecha']);
            $objmovimiento_producto->setVar('fecha_cierre', $_POST['fecha_cierre']);
            $objmovimiento_producto->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objmovimiento_producto->insertDB());
            break;
        
        case 'addguia':
            //Obtenemos tipo de mov
            $objconn = new turno();
            $dg = $objconn->consulta_arreglo("Select * from guia_producto where id = '".$_POST["id_guia_producto"]."'");
            
            $objmovimiento_producto = new movimiento_producto();
            $objmovimiento_producto->setVar('id_producto', $_POST['id_producto']);
            $objmovimiento_producto->setVar('id_almacen', $_POST['id_almacen']);
            if(intval($dg["tipo"])>0){
                $objmovimiento_producto->setVar('cantidad', $_POST['cantidad']);
            }else{
                $objmovimiento_producto->setVar('cantidad', "-".$_POST['cantidad']);
            }
            $objmovimiento_producto->setVar('costo', $_POST['costo']);
            $objmovimiento_producto->setVar('tipo_movimiento','ALMACEN');
            $objmovimiento_producto->setVar('id_usuario', $_POST['id_usuario']);
            
            $objmovimiento_producto->setVar('fecha',date("Y-m-d H:i:s"));
            //Obtenemos fecha cierre y turno
            
            $turno_act = $objconn->consulta_arreglo("Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'");
            $cierre_act = $objconn->consulta_arreglo("Select * from configuracion where id=1");
            
            $objmovimiento_producto->setVar('id_turno', $turno_act["id"]);
            $objmovimiento_producto->setVar('fecha_cierre', $cierre_act["fecha_cierre"]);
            
            $objmovimiento_producto->setVar('estado_fila', $_POST['estado_fila']);
            $objmovimiento_producto->setVar('fecha_vencimiento', $_POST['fecha_vencimiento']);
            $objmovimiento_producto->setVar('lote', $_POST['lote']);
            
            $id_movimiento = $objmovimiento_producto->insertDB();
            
            $objguia_movimiento = new guia_movimiento();
            $objguia_movimiento->setVar('id_movimiento_producto', $id_movimiento);
            $objguia_movimiento->setVar('id_guia_producto', $_POST['id_guia_producto']);
            $objguia_movimiento->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objguia_movimiento->insertDB());
            break;

        case 'mod':
            $objmovimiento_producto->setVar('id', $_POST['id']);
            $objmovimiento_producto->setVar('id_producto', $_POST['id_producto']);
            $objmovimiento_producto->setVar('id_almacen', $_POST['id_almacen']);
            $objmovimiento_producto->setVar('cantidad', $_POST['cantidad']);
            $objmovimiento_producto->setVar('costo', $_POST['costo']);
            $objmovimiento_producto->setVar('tipo_movimiento', $_POST['tipo_movimiento']);
            $objmovimiento_producto->setVar('id_usuario', $_POST['id_usuario']);
            $objmovimiento_producto->setVar('id_turno', $_POST['id_turno']);
            $objmovimiento_producto->setVar('fecha', $_POST['fecha']);
            $objmovimiento_producto->setVar('fecha_cierre', $_POST['fecha_cierre']);
            $objmovimiento_producto->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objmovimiento_producto->updateDB());
            break;
        
        case 'server_side':
            $result = $objmovimiento_producto->ServerSide($_POST);
            echo json_encode($result);
        break;    

        case 'del':
            $objmovimiento_producto->setVar('id', $_POST['id']);
            echo json_encode($objmovimiento_producto->deleteDB());
            break;
        
        case 'delguia':
            $objguia_movimiento = new guia_movimiento();
            $objguia_movimiento->setVar('id', $_POST['id']);
            $objguia_movimiento->getDB();           
            
            $objmovimiento_producto = new movimiento_producto();
            $objmovimiento_producto->setVar('id', $objguia_movimiento->getIdMovimientoProducto());
            $objmovimiento_producto->deleteDB();
            
            echo json_encode($objguia_movimiento->deleteDB());
            break;

        case 'get':
            $res = $objmovimiento_producto->searchDB($_POST['id'], 'id', 1);
          
            if (is_array($res)) {
                $res[0]['id_producto'] = $objproducto->searchDB($res[0]['id_producto'], 'id', 1);
                $res[0]['id_producto'] = $res[0]['id_producto'][0];
                $res[0]['id_almacen'] = $objalmacen->searchDB($res[0]['id_almacen'], 'id', 1);
                $res[0]['id_almacen'] = $res[0]['id_almacen'][0];
                $res[0]['id_usuario'] = $objusuario->searchDB($res[0]['id_usuario'], 'id', 1);
                $res[0]['id_usuario'] = $res[0]['id_usuario'][0];
                $res[0]['id_turno'] = $objturno->searchDB($res[0]['id_turno'], 'id', 1);
                $res[0]['id_turno'] = $res[0]['id_turno'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objmovimiento_producto->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                    $act['id_producto'] = $act['id_producto'][0];
                    $act['id_almacen'] = $objalmacen->searchDB($act['id_almacen'], 'id', 1);
                    $act['id_almacen'] = $act['id_almacen'][0];
                    $act['id_usuario'] = $objusuario->searchDB($act['id_usuario'], 'id', 1);
                    $act['id_usuario'] = $act['id_usuario'][0];
                    $act['id_turno'] = $objturno->searchDB($act['id_turno'], 'id', 1);
                    $act['id_turno'] = $act['id_turno'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
        break;


        case 'search':
            $conn = new MasterConexion();
          
            $res = $conn->consulta_matriz("SELECT * FROM movimiento_producto WHERE id_almacen=".$_POST['almacen']." AND id_producto=".$_POST['data']." AND fecha_cierre<='".$_POST['fecha']."' ");
           
            //$res = $objmovimiento_producto->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                    $act['id_producto'] = $act['id_producto'][0];
                    $act['id_almacen'] = $objalmacen->searchDB($act['id_almacen'], 'id', 1);
                    $act['id_almacen'] = $act['id_almacen'][0];
                    $act['id_usuario'] = $objusuario->searchDB($act['id_usuario'], 'id', 1);
                    $act['id_usuario'] = $act['id_usuario'][0];
                    $act['id_turno'] = $objturno->searchDB($act['id_turno'], 'id', 1);
                    $act['id_turno'] = $act['id_turno'][0];

                    //Para maquillar la huevada de 5peso
                    if(intval($act['tipo_movimiento'])){
                        $act['tipo_movimiento'] = 'VENTA';
                    }
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'adddetmov':
            //Obtenemos fecha cierre y turno
            $objconn = new turno();
            $t = $objconn->consulta_arreglo("Select * from turno where inicio <= '".date("H:i:s")."' AND fin >= '".date("H:i:s")."'");
            $c = $objconn->consulta_arreglo("Select * from configuracion where id=1");
            
            //Movimiento Salida
            $objms = new movimiento_producto();
            $objms->setVar('id_producto', $_POST['id_producto']);
            $objms->setVar('id_almacen', $_POST['id_almacen_o']);
            $objms->setVar('cantidad', - $_POST['cantidad']);
            $objms->setVar('costo', "-".$_POST['costo']);
            $objms->setVar('tipo_movimiento', 'ALMACEN');
            $objms->setVar('id_usuario', $_POST['id_usuario']);
            $objms->setVar('id_turno', $t["id"]);
            $objms->setVar('fecha', date("Y-m-d H:i:s"));
            $objms->setVar('fecha_cierre', $c['fecha_cierre']);
            $objms->setVar('estado_fila','1');
            $ids = $objms->insertDB();
            
            //Movimiento Ingreso
            $objmi = new movimiento_producto();
            $objmi->setVar('id_producto', $_POST['id_producto']);
            $objmi->setVar('id_almacen', $_POST['id_almacen_d']);
            $objmi->setVar('cantidad', $_POST['cantidad']);
            $objmi->setVar('costo', $_POST['costo']);
            $objmi->setVar('tipo_movimiento', 'ALMACEN');
            $objmi->setVar('id_usuario', $_POST['id_usuario']);
            $objmi->setVar('id_turno', $t["id"]);
            $objmi->setVar('fecha', date("Y-m-d H:i:s"));
            $objmi->setVar('fecha_cierre', $c['fecha_cierre']);
            $objmi->setVar('estado_fila','1');
            $idi = $objmi->insertDB();
            
            //Insertamos
            $res = $objconn->consulta_id("Insert into guia_movimiento_a values(NULL,'".$_POST["id_movimiento_almacenes"]."','".$ids."', '".$idi."',1)");

            //INSERTAMOS EN LAS GUIAS
            $informacion = $objconn->consulta_arreglo("SELECT * FROM movimiento_almacenes where id=".$_POST["id_movimiento_almacenes"]." ");            
            $rese = $objconn->consulta_id("Insert into guia_movimiento values(NULL,'".$idi."','".$informacion['entrada']."',1)");
            $ress = $objconn->consulta_id("Insert into guia_movimiento values(NULL,'".$ids."','".$informacion['salida']."',1)");
            echo json_encode($res);
            break;
        
        case 'deldetmov':
            //Obtenemos fecha cierre y turno
            $objconn = new turno();
            $gms = $objconn->consulta_matriz("Select * from guia_movimiento_a where id = '".$_POST["id"]."'");

            if(is_array($gms)){
                foreach($gms as $gm){
                    $objconn->consulta_simple("Delete from movimiento_producto where id = '".$gm["salida"]."'");
                    $objconn->consulta_simple("Delete from movimiento_producto where id = '".$gm["entrada"]."'");
                    $objconn->consulta_simple("Delete from guia_movimiento_a where id = '".$gm["id"]."'");

                    $objconn->consulta_simple("Delete from guia_movimiento where id_movimiento_producto = '".$gm["salida"]."'");
                    $objconn->consulta_simple("Delete from guia_movimiento where id_movimiento_producto = '".$gm["entrada"]."'");
                }
            }
            echo json_encode(1);
        break;
        case 'kardex':

            $almacen = $_POST['id_almacen'];
            $inicio = $_POST['inicio'];
            $conn = new MasterConexion();
           // echo($almacen);
            /*
            $data = $conn->consulta_matriz("SELECT p.id as id, p.nombre as nombre, sum(if(mp.cantidad>0, mp.cantidad, 0)) as cantidad1, 
            sum(if(mp.cantidad<0, mp.cantidad, 0)) as cantidad2, (select sum(if(mp.cantidad>0, mp.cantidad, 0)) + sum(if(mp.cantidad<0, mp.cantidad, 0)) ) as cantidad3, 
            mp.costo as valor1, p.precio_venta as valor2, (select sum(if(mp.cantidad>0, mp.cantidad, 0)) * mp.costo) as total1, 
            (select sum(if(mp.cantidad<0, mp.cantidad, 0)) * p.precio_venta ) as total2 
            from producto p inner join movimiento_producto mp on mp.id_producto = p.id inner join almacen a on mp.id_almacen = a.id 
            where (mp.id_almacen = {$almacen} and mp.estado_fila = 1 and mp.fecha_cierre <= '{$inicio}') 
            group by mp.id_producto order by nombre ASC");
            */
            // $data = $conn->consulta_matriz("SELECT p.id as id, p.nombre as nombre, sum(if(mp.cantidad>0, mp.cantidad, 0)) as cantidad1, 
            // sum(if(mp.cantidad<0, mp.cantidad, 0)) as cantidad2, (select sum(if(mp.cantidad>0, mp.cantidad, 0)) + sum(if(mp.cantidad<0, mp.cantidad, 0)) ) as cantidad3 
            // from producto p inner join movimiento_producto mp on mp.id_producto = p.id inner join almacen a on mp.id_almacen = a.id 
            // where (mp.id_almacen = {$almacen} and mp.estado_fila = 1 and mp.fecha_cierre <= '{$inicio}') 
            // group by mp.id_producto order by nombre ASC");

            $data = $conn->consulta_matriz("SELECT p.id as id, p.unidad, p.nombre as nombre,  (select sum(if(mp.cantidad>0, mp.cantidad, 0)) + sum(if(mp.cantidad<0, mp.cantidad, 0)) ) as cantidad3, p.precio_venta as pventa, p.precio_compra as pcompra 
             from producto p inner join movimiento_producto mp on mp.id_producto = p.id inner join almacen a on mp.id_almacen = a.id 
             where (mp.id_almacen = {$almacen} and mp.estado_fila = 1 and mp.fecha_cierre <= '{$inicio}') 
             group by mp.id_producto order by nombre ASC");
            
            echo json_encode(["data" => $data]);
            
        break;

        
    }
}?>