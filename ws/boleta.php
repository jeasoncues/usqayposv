<?php
        require_once('../nucleo/boleta.php');
        $objboleta = new boleta();
        
                    require_once('../nucleo/venta.php');
                    $objventa = new venta();
                    
        if (isset($_POST['op']))
        {
            switch ($_POST['op'])
            {
                case 'add':
                $objboleta->setVar('id',$_POST['id']);
                    $objboleta->setVar('id_venta',$_POST['id_venta']);
                    $objboleta->setVar('token',$_POST['token']);
                    $objboleta->setVar('serie',$_POST['serie']);
                    $objboleta->setVar('estado_fila',$_POST['estado_fila']);
                    
                    echo json_encode($objboleta->insertDB());
                break;
                
                case 'mod':
                $objboleta->setVar('id',$_POST['id']);
                  $objboleta->setVar('id_venta',$_POST['id_venta']);
                  $objboleta->setVar('token',$_POST['token']);
                  $objboleta->setVar('serie',$_POST['serie']);
                  $objboleta->setVar('estado_fila',$_POST['estado_fila']);
                  
                   echo json_encode($objboleta->updateDB());
                break;
                
                case 'del':
                   $objboleta->setVar('id',$_POST['id']);
                   echo json_encode($objboleta->deleteDB());
                break;
                
                case 'get':
                   $res = $objboleta->searchDB($_POST['id'],'id',1);
                    if(is_array($res)){
                    $res[0]['id_venta'] = $objventa->searchDB($res[0]['id_venta'],'id',1);
                    $res[0]['id_venta'] = $res[0]['id_venta'][0];
                    echo json_encode($res[0]);
                    }else{
                    echo json_encode(0);
                    }
                break;
                
                case 'list':
                   $res = $objboleta->listDB();
                    if(is_array($res)){ 
                       foreach($res as &$act){
                            $act['id_venta'] = $objventa->searchDB($act['id_venta'],'id',1);
                            $act['id_venta'] = $act['id_venta'][0];}
                   echo json_encode($res);
                   }else{
                    echo json_encode(0);
                   }
                break;
                
                case 'search':
                   $res = $objboleta->searchDB($_POST['data'],$_POST['value'],$_POST['type']);
                   if(is_array($res)){ 
                       foreach($res as &$act){
                            $act['id_venta'] = $objventa->searchDB($act['id_venta'],'id',1);
                            $act['id_venta'] = $act['id_venta'][0];}
                   echo json_encode($res);
                   }else{
                    echo json_encode(0);
                   }
                break;
                
            }
        }?>