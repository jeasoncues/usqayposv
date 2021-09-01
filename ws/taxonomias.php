<?php

require_once('../nucleo/taxonomias.php');
$objtaxonomias = new taxonomias();

require_once('../nucleo/servicio.php');
$objservicio = new servicio();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objtaxonomias->setVar('id', $_POST['id']);
            $objtaxonomias->setVar('padre', $_POST['padre']);
            $objtaxonomias->setVar('nombre', $_POST['nombre']);
            $objtaxonomias->setVar('tipo_valor', $_POST['tipo_valor']);
            $objtaxonomias->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objtaxonomias->insertDB());
            break;

        case 'mod':
            $objtaxonomias->setVar('id', $_POST['id']);
            $objtaxonomias->setVar('padre', $_POST['padre']);
            $objtaxonomias->setVar('nombre', $_POST['nombre']);
            $objtaxonomias->setVar('tipo_valor', $_POST['tipo_valor']);
            $objtaxonomias->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objtaxonomias->updateDB());
            break;

        case 'del':
            //Eliminamos todo lo relacionado a la taxonomia
            $objconn = new servicio();
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES=0;");
            $objconn->consulta_simple("Delete from taxonomias_valor where id_taxonomias = '".$_POST['id']."'");
            $objconn->consulta_simple("Delete from servicio_taxonimias where id_taxonomias = '".$_POST['id']."'");
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES=1;");
            
            //Ahora Eliminamos la Taxonomia
            $objtaxonomias = new taxonomias();
            $objtaxonomias->setVar('id', $_POST['id']);
            echo json_encode($objtaxonomias->deleteDB());
            break;

        case 'get':
            $res = $objtaxonomias->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objtaxonomias->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['padre'] = $objtaxonomias->searchDB($act['padre'], 'id', 1);
                    $act['padre'] = $act['padre'][0];
                    
                    switch(intval($act['tipo_valor'])){
                        case 1: 
                            $act['tipo_valor'] = "Abierto";
                        break;
                    
                        case 2:
                            $act['tipo_valor'] = "Rango";
                        break;
                    }
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objtaxonomias->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'level1':
            $sql = "Select * from taxonomias where tipo_valor = 2 AND id <> -1 AND padre is NULL LIMIT ".$_POST["offset"].",".$_POST["limit"]."";
            $res = $objtaxonomias->consulta_matriz($sql);
            if (is_array($res)) {
                foreach ($res as &$act) {                    
                    $hay_hijos = $objtaxonomias->consulta_arreglo("Select count(*) as cantidad from taxonomias where padre = '".$act["id"]."'");
                    if(intval($hay_hijos["cantidad"])>0){
                        $act["es_padre"] = "SI";
                    }else{
                        $act["es_padre"] = "NO";
                    }
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'taxvals':
            $res = $objtaxonomias->consulta_matriz("Select * from taxonomias_valor where id_taxonomias = '".$_POST["tax"]."' LIMIT ".$_POST["offset"].",".$_POST["limit"]."");
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
            case 'level3':
            $res = $objtaxonomias->consulta_matriz("Select * from taxonomias_valor where padre = '".$_POST["padre"]."' LIMIT ".$_POST["offset"].",".$_POST["limit"]."");
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
            case 'servtax':
            $sql = "Select DISTINCT id_servicio from servicio_taxonimias where id_taxonomias = '".$_POST["tax"]."' AND valor = '".$_POST["valor"]."' LIMIT ".$_POST["offset"].",".$_POST["limit"]."";
            //echo $sql;
            $res = $objtaxonomias->consulta_matriz($sql);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_servicio'] = $objservicio->searchDB($act['id_servicio'], 'id', 1);
                    $act['id_servicio'] = $act['id_servicio'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
            case 'searchbytax':
            $res = $objtaxonomias->consulta_matriz("Select DISTINCT id_servicio from servicio_taxonimias where valor LIKE '%".$_POST["valor"]."%' LIMIT ".$_POST["offset"].",".$_POST["limit"]."");
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_servicio'] = $objservicio->searchDB($act['id_servicio'], 'id', 1);
                    $act['id_servicio'] = $act['id_servicio'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>