<?php

require_once('../nucleo/taxonomiav.php');
$objtaxonomiav = new taxonomiav();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objtaxonomiav->setVar('id', $_POST['id']);
            $objtaxonomiav->setVar('padre', $_POST['padre']);
            $objtaxonomiav->setVar('nombre', $_POST['nombre']);
            $objtaxonomiav->setVar('tipo_valor', $_POST['tipo_valor']);
            $objtaxonomiav->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objtaxonomiav->insertDB());
            break;

        case 'mod':
            $objtaxonomiav->setVar('id', $_POST['id']);
            $objtaxonomiav->setVar('padre', $_POST['padre']);
            $objtaxonomiav->setVar('nombre', $_POST['nombre']);
            $objtaxonomiav->setVar('tipo_valor', $_POST['tipo_valor']);
            $objtaxonomiav->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objtaxonomiav->updateDB());
            break;

        case 'del':
            $objtaxonomiav->setVar('id', $_POST['id']);
            echo json_encode($objtaxonomiav->deleteDB());
            break;

        case 'get':
            $res = $objtaxonomiav->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objtaxonomiav->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['padre'] = $objtaxonomiav->searchDB($act['padre'], 'id', 1);
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
            $res = $objtaxonomiav->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
    }
}?>