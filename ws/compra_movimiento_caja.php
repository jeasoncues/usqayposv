<?php
        require_once('../nucleo/compra_movimiento_caja.php');
        $objcompra_movimiento_caja = new compra_movimiento_caja();
        
                    require_once('../nucleo/compra.php');
                    $objcompra = new compra();
                    
                    require_once('../nucleo/movimiento_caja.php');
                    $objmovimiento_caja = new movimiento_caja();
                    
        if (isset($_POST['op']))
        {
            switch ($_POST['op'])
            {
                case 'add':
                $objcompra_movimiento_caja->setVar('id',$_POST['id']);
                    $objcompra_movimiento_caja->setVar('id_compra',$_POST['id_compra']);
                    $objcompra_movimiento_caja->setVar('id_movimiento_caja',$_POST['id_movimiento_caja']);
                    $objcompra_movimiento_caja->setVar('estado_fila',$_POST['estado_fila']);
                    
                    echo json_encode($objcompra_movimiento_caja->insertDB());
                break;
                
                case 'mod':
                $objcompra_movimiento_caja->setVar('id',$_POST['id']);
                  $objcompra_movimiento_caja->setVar('id_compra',$_POST['id_compra']);
                  $objcompra_movimiento_caja->setVar('id_movimiento_caja',$_POST['id_movimiento_caja']);
                  $objcompra_movimiento_caja->setVar('estado_fila',$_POST['estado_fila']);
                  
                   echo json_encode($objcompra_movimiento_caja->updateDB());
                break;
                
                case 'del':
                   $objcompra_movimiento_caja->setVar('id',$_POST['id']);
                   echo json_encode($objcompra_movimiento_caja->deleteDB());
                break;
                
                case 'get':
                   $res = $objcompra_movimiento_caja->searchDB($_POST['id'],'id',1);
                    if(is_array($res)){
                    $res[0]['id_compra'] = $objcompra->searchDB($res[0]['id_compra'],'id',1);
                    $res[0]['id_compra'] = $res[0]['id_compra'][0];
                    $res[0]['id_movimiento_caja'] = $objmovimiento_caja->searchDB($res[0]['id_movimiento_caja'],'id',1);
                    $res[0]['id_movimiento_caja'] = $res[0]['id_movimiento_caja'][0];
                    echo json_encode($res[0]);
                    }else{
                    echo json_encode(0);
                    }
                break;
                
                case 'list':
                   $res = $objcompra_movimiento_caja->listDB();
                    if(is_array($res)){ 
                       foreach($res as &$act){
                            $act['id_compra'] = $objcompra->searchDB($act['id_compra'],'id',1);
                            $act['id_compra'] = $act['id_compra'][0];
                            $act['id_movimiento_caja'] = $objmovimiento_caja->searchDB($act['id_movimiento_caja'],'id',1);
                            $act['id_movimiento_caja'] = $act['id_movimiento_caja'][0];}
                   echo json_encode($res);
                   }else{
                    echo json_encode(0);
                   }
                break;
                
                case 'search':
                   $res = $objcompra_movimiento_caja->searchDB($_POST['data'],$_POST['value'],$_POST['type']);
                   if(is_array($res)){ 
                       foreach($res as &$act){
                            $act['id_compra'] = $objcompra->searchDB($act['id_compra'],'id',1);
                            $act['id_compra'] = $act['id_compra'][0];
                            $act['id_movimiento_caja'] = $objmovimiento_caja->searchDB($act['id_movimiento_caja'],'id',1);
                            $act['id_movimiento_caja'] = $act['id_movimiento_caja'][0];}
                   echo json_encode($res);
                   }else{
                    echo json_encode(0);
                   }
                break;
                
            }
        }?>