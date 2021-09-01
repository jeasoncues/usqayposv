<?php

require_once('../nucleo/include/MasterConexion.php');
$conn = new MasterConexion();

require_once('../nucleo/plato.php');
$objPlato = new plato();

if (isset($_POST['op'])) { 
    switch ($_POST['op']) {
        case 'add':
            $objPlato->setVar('id', NULL);            
            $objPlato->setVar('id_producto',$_POST['id_producto']);            
            $objPlato->setVar('estado_fila', 1);
            $ids = $objPlato->insertDB();
            echo json_encode($ids);
        break;
        
        case 'list':
            $res = $conn->consulta_matriz("SELECT pl.id, p.nombre as producto
            FROM plato pl INNER JOIN  producto p ON (pl.id_producto=p.id)");
            echo json_encode($res);
        break;        

        case 'list_producto_free':
        $res = $conn->consulta_matriz("SELECT * FROM producto p WHERE ( id NOT IN (SELECT id_producto FROM insumo) and  id NOT IN (SELECT id_producto FROM plato) ) ");
        echo json_encode($res);
        break;

        case 'del':
            $objPlato->setVar('id', $_POST['id']);
            $res=$objPlato->deleteDB();
            $objPlato->consulta_simple("DELETE FROM receta WHERE id_plato=".$_POST['id']."");
            echo json_encode($res);
        break;

    }
}
?>