<?php
        require_once('../nucleo/proveedor.php');
        $objproveedor = new proveedor();
        
        if (isset($_POST['op']))
        {
            switch ($_POST['op'])
            {
                case 'add':
                $objproveedor->setVar('id',$_POST['id']);
                    $objproveedor->setVar('razon_social',$_POST['razon_social']);
                    $objproveedor->setVar('ruc',$_POST['ruc']);
                    $objproveedor->setVar('direccion',$_POST['direccion']);
                    $objproveedor->setVar('telefono',$_POST['telefono']);
                    $objproveedor->setVar('estado_fila',$_POST['estado_fila']);
                    
                    echo json_encode($objproveedor->insertDB());
                break;
                
                case 'mod':
                $objproveedor->setVar('id',$_POST['id']);
                  $objproveedor->setVar('razon_social',$_POST['razon_social']);
                  $objproveedor->setVar('ruc',$_POST['ruc']);
                  $objproveedor->setVar('direccion',$_POST['direccion']);
                  $objproveedor->setVar('telefono',$_POST['telefono']);
                  $objproveedor->setVar('estado_fila',$_POST['estado_fila']);
                  
                   echo json_encode($objproveedor->updateDB());
                break;
                
                case 'del':
                   $objproveedor->setVar('id',$_POST['id']);
                   echo json_encode($objproveedor->deleteDB());
                break;
                
                case 'get':
                   $res = $objproveedor->searchDB($_POST['id'],'id',1);
                    if(is_array($res)){
                    echo json_encode($res[0]);
                    }else{
                    echo json_encode(0);
                    }
                break;
                
                case 'list':
                   $res = $objproveedor->listDB();
                    if(is_array($res)){
                   echo json_encode($res);
                   }else{
                    echo json_encode(0);
                   }
                break;
                
                case 'search':
                   $res = $objproveedor->searchDB($_POST['data'],$_POST['value'],$_POST['type']);
                   if(is_array($res)){
                   echo json_encode($res);
                   }else{
                    echo json_encode(0);
                   }
                break;

                case 'compras':
                    require_once('../nucleo/compra.php');
                    $objcompra = new compra();
                    $compras = $objcompra->searchDB($_POST['id'],'id_proveedor',1);
                    echo json_encode($compras);
                break;
                
            }
        }?>