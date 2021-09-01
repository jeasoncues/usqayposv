<?php

require_once('../nucleo/producto_taxonomiap.php');
$objproducto_taxonomiap = new producto_taxonomiap();

require_once('../nucleo/producto.php');
$objproducto = new producto();

require_once('../nucleo/taxonomiap.php');
$objtaxonomiap = new taxonomiap();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $idadd = 0;
            $objconn = new producto();
            $hay = $objconn->consulta_arreglo("Select * from producto_taxonomiap where id_producto = '".$_POST['id_producto']."' AND id_taxonomiap = '".$_POST['id_taxonomiap']."' AND valor = '".$_POST['valor']."' AND estado_fila = 1");
            if(!is_array($hay)){
                $objproducto_taxonomiap = new producto_taxonomiap();
                $objproducto_taxonomiap->setVar('id', $_POST['id']);
                $objproducto_taxonomiap->setVar('id_producto', $_POST['id_producto']);
                $objproducto_taxonomiap->setVar('id_taxonomiap', $_POST['id_taxonomiap']);
                $objproducto_taxonomiap->setVar('valor', $_POST['valor']);
                $objproducto_taxonomiap->setVar('estado_fila', $_POST['estado_fila']);
                $idadd = $objproducto_taxonomiap->insertDB();
            }
            echo json_encode($idadd);
            break;

        case 'add_sunat':
            $idadd = 0;
            $objconn = new producto();
            $hay = $objconn->consulta_arreglo("Select * from producto_taxonomiap where id_producto = '".$_POST['id_producto']."' AND id_taxonomiap = '".$_POST['id_taxonomiap']."' AND valor = '".$_POST['valor']."' AND estado_fila = 1");
            if(!is_array($hay)){
                //Obtener valor by cod sunat
                $objconn = new producto();
                
                $categoria_sunat = $objconn->consulta_arreglo("SELECT * FROM taxonomia_sunat WHERE codigo = ".$_POST['valor']);
                
                $objproducto_taxonomiap = new producto_taxonomiap();
                $objproducto_taxonomiap->setVar('id', $_POST['id']);
                $objproducto_taxonomiap->setVar('id_producto', $_POST['id_producto']);
                $objproducto_taxonomiap->setVar('id_taxonomiap', $_POST['id_taxonomiap']);
                $objproducto_taxonomiap->setVar('valor', $_POST['valor']."_".$categoria_sunat['descripcion']);
                $objproducto_taxonomiap->setVar('estado_fila', $_POST['estado_fila']);
                $idadd = $objproducto_taxonomiap->insertDB();
            }
            echo json_encode($idadd);
            break;

        case 'mod':
            $objproducto_taxonomiap->setVar('id', $_POST['id']);
            $objproducto_taxonomiap->setVar('id_producto', $_POST['id_producto']);
            $objproducto_taxonomiap->setVar('id_taxonomiap', $_POST['id_taxonomiap']);
            $objproducto_taxonomiap->setVar('valor', $_POST['valor']);
            $objproducto_taxonomiap->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objproducto_taxonomiap->updateDB());
            break;
        
        case 'mod1':
            $objconn = new producto();           
            //Verificamos si existe consulta
            $hay = $objconn->consulta_arreglo("Select * from producto_taxonomiap where id_producto = '".$_POST['id_producto']."' AND id_taxonomiap = '".$_POST['id_taxonomiap']."'");
            if(is_array($hay)){
                $objconn->consulta_simple("SET SQL_SAFE_UPDATES=0;");
                $res = $objconn->consulta_simple("Update producto_taxonomiap set valor = '".$_POST['valor']."' where id_producto = '".$_POST['id_producto']."' AND id_taxonomiap = '".$_POST['id_taxonomiap']."'");
                $objconn->consulta_simple("SET SQL_SAFE_UPDATES=1;");
                echo json_encode($res);
            }else{
                $objproducto_taxonomiap = new producto_taxonomiap();
                $objproducto_taxonomiap->setVar('id_producto', $_POST['id_producto']);
                $objproducto_taxonomiap->setVar('id_taxonomiap', $_POST['id_taxonomiap']);
                $objproducto_taxonomiap->setVar('valor', $_POST['valor']);
                $objproducto_taxonomiap->setVar('estado_fila', '1');
                $idadd = $objproducto_taxonomiap->insertDB();
                echo json_encode($idadd);
            }        
            break;

        case 'mod1_sunat':
            $objconn = new producto();           
            //Verificamos si existe consulta
            $hay = $objconn->consulta_arreglo("Select * from producto_taxonomiap where id_producto = '".$_POST['id_producto']."' AND id_taxonomiap = '".$_POST['id_taxonomiap']."'");
            if(is_array($hay)){
                $categoria_sunat = $objconn->consulta_arreglo("SELECT * FROM taxonomia_sunat WHERE codigo = ".$_POST['valor']);
                $objconn->consulta_simple("SET SQL_SAFE_UPDATES=0;");
                $res = $objconn->consulta_simple("Update producto_taxonomiap set valor = '".$_POST['valor']."_".$categoria_sunat['descripcion']."' where id_producto = '".$_POST['id_producto']."' AND id_taxonomiap = '".$_POST['id_taxonomiap']."'");
                $objconn->consulta_simple("SET SQL_SAFE_UPDATES=1;");
                echo json_encode($res);
            }else{
                $categoria_sunat = $objconn->consulta_arreglo("SELECT * FROM taxonomia_sunat WHERE codigo = ".$_POST['valor']);
                $objproducto_taxonomiap = new producto_taxonomiap();
                $objproducto_taxonomiap->setVar('id_producto', $_POST['id_producto']);
                $objproducto_taxonomiap->setVar('id_taxonomiap', $_POST['id_taxonomiap']);
                $objproducto_taxonomiap->setVar('valor', $_POST['valor']."_".$categoria_sunat['descripcion']);
                $objproducto_taxonomiap->setVar('estado_fila', '1');
                $idadd = $objproducto_taxonomiap->insertDB();
                echo json_encode($idadd);
            }        
        break;

        case 'del':
            $objproducto_taxonomiap->setVar('id', $_POST['id']);
            echo json_encode($objproducto_taxonomiap->deleteDB());
            break;

        case 'get':
            $res = $objproducto_taxonomiap->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_producto'] = $objproducto->searchDB($res[0]['id_producto'], 'id', 1);
                $res[0]['id_producto'] = $res[0]['id_producto'][0];
                $res[0]['id_taxonomiap'] = $objtaxonomiap->searchDB($res[0]['id_taxonomiap'], 'id', 1);
                $res[0]['id_taxonomiap'] = $res[0]['id_taxonomiap'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;
        
        case 'getbytax':
            $res = $objproducto_taxonomiap->consulta_arreglo("Select * from producto_taxonomiap where id_producto = '".$_POST["id_producto"]."' AND id_taxonomiap = '".$_POST["id_taxonomiap"]."'");
            
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'getbyprod':
            $res = $objproducto_taxonomiap->consulta_arreglo("Select * from producto_taxonomiap where id_producto = '".$_POST["idp"]."'");
            if (is_array($res)) {
                $res[0]['id_taxonomiap'] = $objtaxonomiap->searchDB($res[0]['id_taxonomiap'], 'id', 1);
                $res[0]['id_taxonomiap'] = $res[0]['id_taxonomiap'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objproducto_taxonomiap->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                    $act['id_producto'] = $act['id_producto'][0];
                    $act['id_taxonomiap'] = $objtaxonomiap->searchDB($act['id_taxonomiap'], 'id', 1);
                    $act['id_taxonomiap'] = $act['id_taxonomiap'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objproducto_taxonomiap->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                    $act['id_producto'] = $act['id_producto'][0];
                    $act['id_taxonomiap'] = $objtaxonomiap->searchDB($act['id_taxonomiap'], 'id', 1);
                    $act['id_taxonomiap'] = $act['id_taxonomiap'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>