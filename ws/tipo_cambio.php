<?php
        require_once('../nucleo/tipo_cambio.php');
        $objtipo_cambio = new tipo_cambio();
        
        if (isset($_POST['op']))
        {
            switch ($_POST['op'])
            {
                case 'add':
                $objtipo_cambio->setVar('id',$_POST['id']);
                    $objtipo_cambio->setVar('moneda_origen',$_POST['moneda_origen']);
                    $objtipo_cambio->setVar('moneda_destino',$_POST['moneda_destino']);
                    $objtipo_cambio->setVar('tasa',$_POST['tasa']);
                    $objtipo_cambio->setVar('estado_fila',$_POST['estado_fila']);
                    
                    echo json_encode($objtipo_cambio->insertDB());
                break;
                
                case 'mod':
                $objtipo_cambio->setVar('id',$_POST['id']);
                  $objtipo_cambio->setVar('moneda_origen',$_POST['moneda_origen']);
                  $objtipo_cambio->setVar('moneda_destino',$_POST['moneda_destino']);
                  $objtipo_cambio->setVar('tasa',$_POST['tasa']);
                  $objtipo_cambio->setVar('estado_fila',$_POST['estado_fila']);
                  
                   echo json_encode($objtipo_cambio->updateDB());
                break;
                
                case 'del':
                   $objtipo_cambio->setVar('id',$_POST['id']);
                   echo json_encode($objtipo_cambio->deleteDB());
                break;
                
                case 'get':
                   $res = $objtipo_cambio->searchDB($_POST['id'],'id',1);
                    if(is_array($res)){
                    echo json_encode($res[0]);
                    }else{
                    echo json_encode(0);
                    }
                break;
                
                case 'list':
                   $res = $objtipo_cambio->listDB();
                    if(is_array($res)){
                   echo json_encode($res);
                   }else{
                    echo json_encode(0);
                   }
                break;
                
                case 'search':
                   $res = $objtipo_cambio->searchDB($_POST['data'],$_POST['value'],$_POST['type']);
                   if(is_array($res)){
                   echo json_encode($res);
                   }else{
                    echo json_encode(0);
                   }
                break;
                
            }
        }?>