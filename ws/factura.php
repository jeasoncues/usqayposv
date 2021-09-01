<?php
        require_once('../nucleo/factura.php');
        $objfactura = new factura();
        
                    require_once('../nucleo/venta.php');
                    $objventa = new venta();
                    
        if (isset($_POST['op']))
        {
            switch ($_POST['op'])
            {
                case 'add':
                $objfactura->setVar('id',$_POST['id']);
                    $objfactura->setVar('id_venta',$_POST['id_venta']);
                    $objfactura->setVar('token',$_POST['token']);
                    $objfactura->setVar('serie',$_POST['serie']);
                    $objfactura->setVar('estado_fila',$_POST['estado_fila']);
                    
                    echo json_encode($objfactura->insertDB());
                break;
                
                case 'mod':
                $objfactura->setVar('id',$_POST['id']);
                  $objfactura->setVar('id_venta',$_POST['id_venta']);
                  $objfactura->setVar('token',$_POST['token']);
                  $objfactura->setVar('serie',$_POST['serie']);
                  $objfactura->setVar('estado_fila',$_POST['estado_fila']);
                  
                   echo json_encode($objfactura->updateDB());
                break;
                
                case 'del':
                   $objfactura->setVar('id',$_POST['id']);
                   echo json_encode($objfactura->deleteDB());
                break;
                
                case 'get':
                   $res = $objfactura->searchDB($_POST['id'],'id',1);
                    if(is_array($res)){
                    $res[0]['id_venta'] = $objventa->searchDB($res[0]['id_venta'],'id',1);
                    $res[0]['id_venta'] = $res[0]['id_venta'][0];
                    echo json_encode($res[0]);
                    }else{
                    echo json_encode(0);
                    }
                break;
                
                case 'list':
                   $res = $objfactura->listDB();
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
                   $res = $objfactura->searchDB($_POST['data'],$_POST['value'],$_POST['type']);
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