<?php

require_once('../nucleo/include/MasterConexion.php');
$conn = new MasterConexion();

require_once('../nucleo/insumo.php');
$objInsumo = new insumo();

if (isset($_POST['op'])) { 
    switch ($_POST['op']) {
        case 'add':
            $objInsumo->setVar('id', NULL);
            $objInsumo->setVar('id_unidad_medida_insumo_porcion', $_POST['id_unidad_medida_insumo_porcion']);
            $objInsumo->setVar('id_padre', 0);
            $objInsumo->setVar('id_producto',$_POST['id_producto']);
            $objInsumo->setVar('valor_insumo', $_POST['valor_insumo']);
            $objInsumo->setVar('valor_porcion', $_POST['valor_porcion']);
            $objInsumo->setVar('conversion', $_POST['conversion']);
            $objInsumo->setVar('descripcion',"");
            $objInsumo->setVar('estado_fila', 1);
            $ids = $objInsumo->insertDB();
            echo json_encode($ids);
        break;

        case 'addPorcion':
            $objInsumo->setVar('id', NULL);
            $objInsumo->setVar('id_unidad_medida_insumo_porcion', $_POST['id_unidad_medida_insumo_porcion']);
            $objInsumo->setVar('id_padre', $_POST['id_padre']);
            $objInsumo->setVar('id_producto',$_POST['id_producto']);
            $objInsumo->setVar('valor_insumo', $_POST['valor_insumo']);
            $objInsumo->setVar('valor_porcion', $_POST['valor_porcion']);
            $objInsumo->setVar('conversion', $_POST['conversion']);
            $objInsumo->setVar('descripcion', $_POST['descripcion']);
            $objInsumo->setVar('estado_fila', 1);
            $ids = $objInsumo->insertDB();
            echo json_encode($ids);
        break;

        case 'editPorcion':
            $objInsumo->setVar('id', $_POST['id']);
            $objInsumo->setVar('id_unidad_medida_insumo_porcion', $_POST['id_unidad_medida_insumo_porcion']);
            $objInsumo->setVar('id_padre', $_POST['id_padre']);
            $objInsumo->setVar('id_producto',$_POST['id_producto']);
            $objInsumo->setVar('valor_insumo', $_POST['valor_insumo']);
            $objInsumo->setVar('valor_porcion', $_POST['valor_porcion']);
            $objInsumo->setVar('conversion', $_POST['conversion']);
            $objInsumo->setVar('descripcion', $_POST['descripcion']);
            $objInsumo->setVar('estado_fila', 1);
            $ids = $objInsumo->updateDB();
            echo json_encode($ids);
        break;

        case 'list':
            $res = $conn->consulta_matriz("SELECT i.id,i.id_unidad_medida_insumo_porcion, p.nombre as producto, um.nombre as unidad_medida, um.id_padre unidad_medida_padre
            FROM insumo i INNER JOIN unidad_medida_insumo_porcion um ON(i.id_unidad_medida_insumo_porcion=um.id) INNER JOIN producto p ON (i.id_producto=p.id)");
            echo json_encode($res);
        break;

        case 'listPorcion':
            $res = $conn->consulta_matriz("SELECT i.id,i.valor_porcion,i.descripcion as descripcion, p.nombre as producto, um.nombre as unidad_medida, um.id_padre unidad_medida_padre
            FROM insumo i INNER JOIN unidad_medida_insumo_porcion um ON(i.id_unidad_medida_insumo_porcion=um.id) INNER JOIN producto p ON (i.id_producto=p.id) WHERE i.id_padre=".$_POST['id']."");
            echo json_encode($res);
        break;

        case 'list_producto_free':        
        $res = $conn->consulta_matriz("SELECT * FROM producto p WHERE ( id NOT IN (SELECT id_producto FROM insumo) and  id NOT IN (SELECT id_producto FROM plato) ) ");
        echo json_encode($res);
        break;

        case 'del':
            $objInsumo->setVar('id', $_POST['id']);
            $res=$objInsumo->deleteDB();
            $objInsumo->consulta_simple("DELETE FROM receta WHERE id_insumo=".$_POST['id']."");
            $porciones=$objInsumo->consulta_matriz("SELECT * FROM insumo WHERE id_padre=".$_POST['id']."");
            if( is_array($porciones) ){
                foreach( $porciones as $p ){
                    $objInsumo->consulta_simple("DELETE FROM receta WHERE id_insumo=".$p['id']."");
                    $objInsumo->consulta_simple("DELETE FROM insumo WHERE id=".$p['id']."");
                }
            }            
            echo json_encode($res);
        break;

        case 'delPorcion':
            $objInsumo->setVar('id', $_POST['id']);
            echo json_encode($objInsumo->deleteDB());
        break;


        case 'get':
            $res = $objInsumo->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                if(isset($res[0]['id_padre'])){
                    if($res[0]['id_padre']==0){
                        $i= $objInsumo->consulta_arreglo("SELECT nombre  FROM producto WHERE id=".$res[0]['id_producto']."");
                        $res[0]['descripcion']=$i['nombre'];
                    }
                }
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
        break;

        case 'getPorcion':
            $res = $objInsumo->searchDB($_POST['id'], 'id', 1);
            echo json_encode($res[0]);
        break;


    }
}
?>