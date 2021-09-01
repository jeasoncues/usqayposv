<?php

require_once('../nucleo/include/MasterConexion.php');
$conn = new MasterConexion();

require_once('../nucleo/impresora.php');
$objImpresora = new impresora();

if (isset($_POST['op'])) { 
    switch ($_POST['op']) {
        case 'add':
            $objImpresora->setVar('id', NULL);
            $objImpresora->setVar('nombre', $_POST['nombre']);            
            $ids = $objImpresora->insertDB();
            echo json_encode($ids); 
        break;

        case 'edit':
            $objImpresora->setVar('id', $_POST['id']);
            $objImpresora->setVar('nombre', $_POST['nombre']);            
            $ids = $objImpresora->updateDB();
            echo json_encode($ids);
        break;


        case 'list':
            $res = $objImpresora->listDBImp();
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
        break;

       
       
        case 'del':
            $objImpresora->setVar('id', $_POST['id']);
            $res=$objImpresora->deleteDB();                     
            echo json_encode($res);
        break;       

        case 'get':
            $res = $objImpresora->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
        break;        


    }
}
?>