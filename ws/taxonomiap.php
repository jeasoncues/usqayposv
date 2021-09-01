<?php

require_once('../nucleo/taxonomiap.php');
$objtaxonomiap = new taxonomiap();

require_once('../nucleo/producto.php');
$objproducto = new producto();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objtaxonomiap->setVar('id', $_POST['id']);
            $objtaxonomiap->setVar('padre', $_POST['padre']);
            $objtaxonomiap->setVar('nombre', $_POST['nombre']);
            $objtaxonomiap->setVar('tipo_valor', $_POST['tipo_valor']);
            $objtaxonomiap->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objtaxonomiap->insertDB());
            break;

        case 'mod':
            $objtaxonomiap->setVar('id', $_POST['id']);
            $objtaxonomiap->setVar('padre', $_POST['padre']);
            $objtaxonomiap->setVar('nombre', $_POST['nombre']);
            $objtaxonomiap->setVar('tipo_valor', $_POST['tipo_valor']);
            $objtaxonomiap->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objtaxonomiap->updateDB());
            break;

        case 'del':
            //Eliminamos todo lo relacionado a la taxonomia
            $objconn = new producto();
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES=0;");
            $objconn->consulta_simple("Delete from taxonomiap_valor where id_taxonomiap = '".$_POST['id']."'");
            $objconn->consulta_simple("Delete from producto_taxonomiap where id_taxonomiap = '".$_POST['id']."'");
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES=1;");

            //Ahora Eliminamos la Taxonomia
            $objtaxonomiap = new taxonomiap();
            $objtaxonomiap->setVar('id', $_POST['id']);
            echo json_encode($objtaxonomiap->deleteDB());
            break;

        case 'get':
            $res = $objtaxonomiap->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objtaxonomiap->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['padre'] = $objtaxonomiap->searchDB($act['padre'], 'id', 1);
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
            $res = $objtaxonomiap->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objtaxonomiap->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['padre'] = $objtaxonomiap->searchDB($act['padre'], 'id', 1);
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

            case 'level1':
            $sql = "Select * from taxonomiap where tipo_valor = 2 AND id <> -1 AND padre is NULL LIMIT ".$_POST["offset"].",".$_POST["limit"]."";
            $res = $objtaxonomiap->consulta_matriz($sql);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $hay_hijos = $objtaxonomiap->consulta_arreglo("Select count(*) as cantidad from taxonomiap where padre = '".$act["id"]."'");
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
            $res = $objtaxonomiap->consulta_matriz("Select * from taxonomiap_valor where id_taxonomiap = '".$_POST["tax"]."' LIMIT ".$_POST["offset"].",".$_POST["limit"]."");
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

            case 'level3':
            $res = $objtaxonomiap->consulta_matriz("Select * from taxonomiap_valor where padre = '".$_POST["padre"]."' LIMIT ".$_POST["offset"].",".$_POST["limit"]."");
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

            case 'prodtax':
            $res = $objtaxonomiap->consulta_matriz("Select DISTINCT id_producto from producto_taxonomiap where id_taxonomiap = '".$_POST["tax"]."' AND valor = '".$_POST["valor"]."' LIMIT ".$_POST["offset"].",".$_POST["limit"]."");
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                    $act['id_producto'] = $act['id_producto'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

            case 'searchbytax':
            // echo "Select DISTINCT id_producto from producto_taxonomiap where valor LIKE '%".$_POST["valor"]."%' LIMIT ".$_POST["offset"].",".$_POST["limit"]."";
            $newValor=convert_cotos($_POST["valor"]);
            
            //$sqll="Select DISTINCT id_producto from producto_taxonomiap where valor LIKE '%".$_POST["valor"]."%' LIMIT ".$_POST["offset"].",".$_POST["limit"]."";
            //echo($sqll);

            //$res = $objtaxonomiap->consulta_matriz("Select DISTINCT id_producto from producto_taxonomiap where valor LIKE '%".$_POST["valor"]."%' LIMIT ".$_POST["offset"].",".$_POST["limit"]."");
            $res = $objtaxonomiap->consulta_matriz("Select DISTINCT id_producto from producto_taxonomiap where valor LIKE '%".$newValor."%' LIMIT ".$_POST["offset"].",".$_POST["limit"]."");
            //$res = $objtaxonomiap->consulta_matriz_cotos("Select DISTINCT id_producto from producto_taxonomiap where valor LIKE '%".$newValor."%' LIMIT ".$_POST["offset"].",".$_POST["limit"]."");
            // echo($res);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                    $act['id_producto'] = $act['id_producto'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

            case 'searchByBarcode':
            $res = $objtaxonomiap->consulta_matriz("SELECT DISTINCT id_producto FROM producto_taxonomiap WHERE valor = '{$_POST["valor"]}' ORDER BY id_producto LIMIT 1");
                
            if (is_array($res)) {
                $datareturn=array();
                foreach ($res as &$act) {                    
                    $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                    $act['id_producto'] = $act['id_producto'][0];
                    //$data = $objproducto->searchDB($act['id_producto'], 'id', 1);                    
                    //$datareturn['id_producto'] = $data[0];
                    // var_dump($datareturn);
                }
                // echo("antes");
                // echo json_encode($res);
                echo json_encode($res);
            } else {
                $res1 = $objtaxonomiap->consulta_matriz("Select id from producto where id = '".intval($_POST["valor"])."' ORDER BY id DESC LIMIT 1");
                if (is_array($res1)) {
                    foreach ($res1 as &$act) {
                        $act['id_producto'] = $objproducto->searchDB($act['id'], 'id', 1);
                        $act['id_producto'] = $act['id_producto'][0];
                    }
                    echo json_encode($res1);
                }else{
                    $res1 = $objtaxonomiap->consulta_matriz("SELECT DISTINCT * FROM productos_precios WHERE barcode = '{$_POST["valor"]}' LIMIT 1");

                    if (is_array($res1)) {
                        foreach ($res1 as &$act) {
                            $act['id_producto'] = $objproducto->searchDB($act['id_producto'], 'id', 1);
                            $act['id_producto'] = $act['id_producto'][0];
                        }
                        echo json_encode($res1);
                    }else{
                        echo json_encode(0);
                    }

                }
            }
            break;
    }
}

function convert_cotos($entrada){
	$codificacion_=mb_detect_encoding($entrada,"UTF-8,ISO-8859-1");
	$dataa= iconv($codificacion_,'UTF-8',$entrada);
	return $dataa;
}

function convert_array_cotos($entrada){
    var_dump($entrada);
	/* $codificacion_=mb_detect_encoding($entrada,"UTF-8,ISO-8859-1");
	$dataa= iconv($codificacion_,'UTF-8',$entrada);
	return $dataa; */
}


?>