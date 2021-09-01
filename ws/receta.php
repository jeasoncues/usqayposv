<?php

require_once('../nucleo/include/MasterConexion.php');
$conn = new MasterConexion();

require_once('../nucleo/receta.php');
$objReceta = new receta();

if (isset($_POST['op'])) { 
    switch ($_POST['op']) {
        case 'add':
            $objReceta->setVar('id', NULL);            
            $objReceta->setVar('id_plato',$_POST['id_plato']);
            $objReceta->setVar('id_insumo',$_POST['id_insumo']);
            $objReceta->setVar('cantidad',$_POST['cantidad']);
            $objReceta->setVar('estado_fila', 1);
            $ids = $objReceta->insertDB();
            echo json_encode($ids);
        break;

        case 'edit':
            $objReceta->setVar('id',$_POST['id']);      
            $objReceta->setVar('id_plato',$_POST['id_plato']);
            $objReceta->setVar('id_insumo',$_POST['id_insumo']);
            $objReceta->setVar('cantidad',$_POST['cantidad']);
            $objReceta->setVar('estado_fila', 1);
            $ids = $objReceta->updateDB();
            echo json_encode($ids);
        break;
        
        case 'list':
            $res = $conn->consulta_matriz("SELECT r.*, p.nombre as producto
            FROM plato pl INNER JOIN  producto p ON (pl.id_producto=p.id) INNER JOIN receta r ON (r.id_plato=pl.id) WHERE pl.id=".$_POST['id']."");
            if(is_array($res)){
                foreach($res as &$p){
                    $insumo=$conn->consulta_arreglo("SELECT i.*,p.nombre as nombreinsumopadre FROM insumo i INNER JOIN producto p ON(i.id_producto=p.id) WHERE i.id=".$p['id_insumo']."");
                    $p['nombre_porcion']=$insumo['descripcion'];
                    $p['nombre_insumo']=$insumo['nombreinsumopadre'];                    
                }

            }
            echo json_encode($res);
        break;

        case 'list_insumo_porciones':
        $res = $conn->consulta_matriz("SELECT i.id, p.nombre as nombre_insumo, um.nombre as unidad_medida, i.id_padre as es_padre
            FROM insumo i INNER JOIN unidad_medida_insumo_porcion um ON(i.id_unidad_medida_insumo_porcion=um.id) INNER JOIN producto p ON (i.id_producto=p.id) where i.id_padre=0
            UNION ALL 
            SELECT i.id, i.descripcion as nombre_insumo, um.nombre as unidad_medida, i.id_padre as es_padre
            FROM insumo i INNER JOIN unidad_medida_insumo_porcion um ON(i.id_unidad_medida_insumo_porcion=um.id) INNER JOIN producto p ON (i.id_producto=p.id) where i.id_padre>0");
        echo json_encode($res);
        break;

        case 'del':
            $objReceta->setVar('id', $_POST['id']);
            echo json_encode($objReceta->deleteDB());
        break;

        case 'get':
            $res = $objReceta->searchDB($_POST['id'], 'id', 1);
            if(is_array($res)){
                // echo json_encode($res[0]);               
                $res[0]['data_insumo']=$objReceta->consulta_arreglo("SELECT i.id,i.descripcion,i.id_unidad_medida_insumo_porcion, p.nombre as producto, um.nombre as unidad_medida, um.id_padre unidad_medida_padre
                FROM insumo i INNER JOIN unidad_medida_insumo_porcion um ON(i.id_unidad_medida_insumo_porcion=um.id) 
                INNER JOIN producto p ON (i.id_producto=p.id) WHERE i.id=".$res[0]['id_insumo']."");
                echo json_encode($res[0]);
            }else{
                // echo json_encode($res[0]);
                echo json_encode(0);
            }            
        break;

        case 'showTest':
            $id_producto=$_POST['id_producto'];
            $plato=$objReceta->consulta_arreglo("SELECT * FROM plato WHERE id_producto=".$id_producto."");
            if(isset($plato['id'])){
                $receta=$objReceta->consulta_matriz("SELECT (r.cantidad*i.valor_insumo) as cantidad_total  , r.cantidad as cantidad_recete,i.id_producto id_producto_insumo, i.valor_insumo, i.valor_porcion, i.conversion,i.id_padre as es_pdre FROM receta r INNER JOIN insumo i ON (r.id_insumo=i.id) WHERE r.id_plato=".$plato['id']."");
                if(is_array($receta)){
                    echo json_encode($receta);
                    // INSERTAMOS EL MOVIMIENTO 
                    
                }else{
                    // NO HACEMOS NADA
                }
            }
        break;
        


    }
}
?>