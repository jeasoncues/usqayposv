<?php

require_once('../nucleo/servicio_taxonimias.php');
$objservicio_taxonimias = new servicio_taxonimias();

require_once('../nucleo/servicio.php');
$objservicio = new servicio();

require_once('../nucleo/taxonomias.php');
$objtaxonomias = new taxonomias();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $idadd = 0;
            
            $objconn = new servicio();
            $hay = $objconn->consulta_arreglo("Select * from servicio_taxonimias where id_servicio = '".$_POST['id_servicio']."' AND id_taxonomias = '".$_POST['id_taxonomias']."' AND valor = '".$_POST['valor']."' AND estado_fila = 1");
            if(!is_array($hay)){
                $objservicio_taxonimias = new servicio_taxonimias();
                $objservicio_taxonimias->setVar('id', $_POST['id']);
                $objservicio_taxonimias->setVar('id_servicio', $_POST['id_servicio']);
                $objservicio_taxonimias->setVar('id_taxonomias', $_POST['id_taxonomias']);
                $objservicio_taxonimias->setVar('valor', $_POST['valor']);
                $objservicio_taxonimias->setVar('estado_fila', $_POST['estado_fila']);
                $idadd = $objservicio_taxonimias->insertDB();
            }

            echo json_encode($idadd);
            break;

        case 'add_sunat':
            $idadd = 0;
            $objconn = new servicio();
            $hay = $objconn->consulta_arreglo("Select * from servicio_taxonimias where id_servicio = '".$_POST['id_servicio']."' AND id_taxonomias = '".$_POST['id_taxonomias']."' AND valor = '".$_POST['valor']."' AND estado_fila = 1");
            
            if(!is_array($hay)){
                //Obtener valor by cod sunat
                $objconn = new servicio();
                
                $categoria_sunat = $objconn->consulta_arreglo("SELECT * FROM taxonomia_sunat WHERE codigo = ".$_POST['valor']);
                
                $objservicio_taxonimias = new servicio_taxonimias();
                $objservicio_taxonimias->setVar('id', $_POST['id']);
                $objservicio_taxonimias->setVar('id_servicio', $_POST['id_servicio']);
                $objservicio_taxonimias->setVar('id_taxonomias', $_POST['id_taxonomias']);
                $objservicio_taxonimias->setVar('valor', $_POST['valor']."_".$categoria_sunat['descripcion']);
                $objservicio_taxonimias->setVar('estado_fila', $_POST['estado_fila']);
                $idadd = $objservicio_taxonimias->insertDB();
                
                
            }
            echo json_encode($idadd);
        break;
        case 'mod':
            $objservicio_taxonimias->setVar('id', $_POST['id']);
            $objservicio_taxonimias->setVar('id_servicio', $_POST['id_servicio']);
            $objservicio_taxonimias->setVar('id_taxonomias', $_POST['id_taxonomias']);
            $objservicio_taxonimias->setVar('valor', $_POST['valor']);
            $objservicio_taxonimias->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objservicio_taxonimias->updateDB());
            break;
        
        case 'mod1':
            $objconn = new servicio();
            //Verificamos si existe consulta
            $hay = $objconn->consulta_arreglo("Select * from servicio_taxonimias where id_servicio = '".$_POST['id_servicio']."' AND id_taxonomias = '".$_POST['id_taxonomias']."'");
            if(is_array($hay)){ 
                $objconn->consulta_simple("SET SQL_SAFE_UPDATES=0;");
                $res = $objconn->consulta_simple("Update servicio_taxonimias set valor = '".$_POST['valor']."' where id_servicio = '".$_POST['id_servicio']."' AND id_taxonomias = '".$_POST['id_taxonomias']."'");
                $objconn->consulta_simple("SET SQL_SAFE_UPDATES=1;");
                echo json_encode($res);
            }else{
                $objservicio_taxonimias = new servicio_taxonimias();
                $objservicio_taxonimias->setVar('id_servicio', $_POST['id_servicio']);
                $objservicio_taxonimias->setVar('id_taxonomias', $_POST['id_taxonomias']);
                $objservicio_taxonimias->setVar('valor', $_POST['valor']);
                $objservicio_taxonimias->setVar('estado_fila', '1');
                $idadd = $objservicio_taxonimias->insertDB();
                echo json_encode($idadd);
            }
        break;

        case 'mod1_sunat':
            $objconn = new servicio();         
            //Verificamos si existe consulta
            $hay = $objconn->consulta_arreglo("Select * from servicio_taxonimias where id_servicio = '".$_POST['id_servicio']."' AND id_taxonomias = '".$_POST['id_taxonomias']."'");

            if(is_array($hay)){
                $categoria_sunat = $objconn->consulta_arreglo("SELECT * FROM taxonomia_sunat WHERE codigo = ".$_POST['valor']);
                $objconn->consulta_simple("SET SQL_SAFE_UPDATES=0;");
                $res = $objconn->consulta_simple("Update servicio_taxonimias set valor = '".$_POST['valor']."_".$categoria_sunat['descripcion']."' where id_servicio = '".$_POST['id_servicio']."' AND id_taxonomias = '".$_POST['id_taxonomias']."'");
                $objconn->consulta_simple("SET SQL_SAFE_UPDATES=1;");
                echo json_encode($res);
            }else{
                $categoria_sunat = $objconn->consulta_arreglo("SELECT * FROM taxonomia_sunat WHERE codigo = ".$_POST['valor']);
                $objservicio_taxonimias = new servicio_taxonimias();
                $objservicio_taxonimias->setVar('id_servicio', $_POST['id_servicio']);
                $objservicio_taxonimias->setVar('id_taxonomias', $_POST['id_taxonomias']);
                $objservicio_taxonimias->setVar('valor', $_POST['valor']."_".$categoria_sunat['descripcion']);
                $objservicio_taxonimias->setVar('estado_fila', '1');
                $idadd = $objservicio_taxonimias->insertDB();
                echo json_encode($idadd);
            }        
        break;

        case 'del':
            $objservicio_taxonimias->setVar('id', $_POST['id']);
            echo json_encode($objservicio_taxonimias->deleteDB());
            break;

        case 'get':
            $res = $objservicio_taxonimias->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_servicio'] = $objservicio->searchDB($res[0]['id_servicio'], 'id', 1);
                $res[0]['id_servicio'] = $res[0]['id_servicio'][0];
                $res[0]['id_taxonomias'] = $objtaxonomias->searchDB($res[0]['id_taxonomias'], 'id', 1);
                $res[0]['id_taxonomias'] = $res[0]['id_taxonomias'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'getbyprod':
            $res = $objservicio_taxonimias->consulta_arreglo("Select * from servicio_taxonomias where id_servicio = '".$_POST["ids"]."'");
            if (is_array($res)) {
                $res[0]['id_servicio'] = $objservicio->searchDB($res[0]['id_servicio'], 'id', 1);
                $res[0]['id_servicio'] = $res[0]['id_servicio'][0];
                $res[0]['id_taxonomias'] = $objtaxonomias->searchDB($res[0]['id_taxonomias'], 'id', 1);
                $res[0]['id_taxonomias'] = $res[0]['id_taxonomias'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;
        
        case 'getbytax':
            
            $res = $objtaxonomias->consulta_arreglo("Select * from servicio_taxonimias where id_servicio = '".$_POST["id_servicio"]."' AND id_taxonomias = '".$_POST["id_taxonomias"]."'");
            
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objservicio_taxonimias->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_servicio'] = $objservicio->searchDB($act['id_servicio'], 'id', 1);
                    $act['id_servicio'] = $act['id_servicio'][0];
                    $act['id_taxonomias'] = $objtaxonomias->searchDB($act['id_taxonomias'], 'id', 1);
                    $act['id_taxonomias'] = $act['id_taxonomias'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objservicio_taxonimias->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_servicio'] = $objservicio->searchDB($act['id_servicio'], 'id', 1);
                    $act['id_servicio'] = $act['id_servicio'][0];
                    $act['id_taxonomias'] = $objtaxonomias->searchDB($act['id_taxonomias'], 'id', 1);
                    $act['id_taxonomias'] = $act['id_taxonomias'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>