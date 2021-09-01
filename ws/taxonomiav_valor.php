<?php

require_once('../nucleo/taxonomiav_valor.php');
$objtaxonomiav_valor = new taxonomiav_valor();

require_once('../nucleo/taxonomiav.php');
$objtaxonomiav = new taxonomiav();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objtaxonomiav_valor->setVar('id', $_POST['id']);
            $objtaxonomiav_valor->setVar('id_taxonomiav', $_POST['id_taxonomiav']);
            $objtaxonomiav_valor->setVar('valor', $_POST['valor']);
            $objtaxonomiap_valor->setVar('padre', $_POST['padre']);
            $objtaxonomiav_valor->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objtaxonomiav_valor->insertDB());
            break;

        case 'mod':
            $objtaxonomiav_valor->setVar('id', $_POST['id']);
            $objtaxonomiav_valor->setVar('id_taxonomiav', $_POST['id_taxonomiav']);
            $objtaxonomiav_valor->setVar('valor', $_POST['valor']);
            $objtaxonomiap_valor->setVar('padre', $_POST['padre']);
            $objtaxonomiav_valor->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objtaxonomiav_valor->updateDB());
            break;

        case 'del':
            $objtaxonomiav_valor->setVar('id', $_POST['id']);
            echo json_encode($objtaxonomiav_valor->deleteDB());
            break;

        case 'get':
            $res = $objtaxonomiav_valor->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_taxonomiav'] = $objtaxonomiav->searchDB($res[0]['id_taxonomiav'], 'id', 1);
                $res[0]['id_taxonomiav'] = $res[0]['id_taxonomiav'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objtaxonomiav_valor->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_taxonomiav'] = $objtaxonomiav->searchDB($act['id_taxonomiav'], 'id', 1);
                    $act['id_taxonomiav'] = $act['id_taxonomiav'][0];
                    
                    $act['padre'] = $objtaxonomias_valor->searchDB($act['padre'], 'id', 1);
                    $act['padre'] = $act['padre'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'listpadre':
            $res = $objtaxonomiav_valor->consulta_matriz("Select * from taxonomiav_valor where id_taxonomiav = '".$_POST["id_padre"]."'");
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_taxonomiav'] = $objtaxonomiav->searchDB($act['id_taxonomiav'], 'id', 1);
                    $act['id_taxonomiav'] = $act['id_taxonomiav'][0];
                    
                    $act['padre'] = $objtaxonomias_valor->searchDB($act['padre'], 'id', 1);
                    $act['padre'] = $act['padre'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objtaxonomiav_valor->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_taxonomiav'] = $objtaxonomiav->searchDB($act['id_taxonomiav'], 'id', 1);
                    $act['id_taxonomiav'] = $act['id_taxonomiav'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>