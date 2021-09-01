<?php
header("Access-Control-Allow-Origin: *");
require_once('../nucleo/include/MasterConexion.php');

$op = $_POST['op'];
$conn = new MasterConexion();

switch ($op) {

    case 'estan_ahi_mis_vidas':
        $r = ["resp" => true];
        echo json_encode($r);
    break;
    
    case 'verify_producto':
        
        $producto = $conn->consulta_arreglo("SELECT * FROM producto WHERE nombre = '".$_POST['valor']."'");
        
        if( !$producto ){
            $r = 0;
        }else{
            $r = 1;
        }

        header('Content-Type: application/json');
        echo json_encode($r);

    break;

    case 'insert_producto':
        
        // Insertar Producto
        $id = $conn->consulta_id(
            "INSERT INTO producto VALUES(NULL,'".$_POST['valor']['nombre']."','NIU','".$_POST['valor']['precio_compra']."','".$_POST['valor']['precio_venta']."','".$_POST['valor']['incluye_impuesto']."','1')"
        );

        if($id > 0){
            // Crear Taxonomias
            $conn->consulta_simple("Insert into producto_taxonomiap values(NULL,'".$id."','1','".$_POST['valor']['nombre']."','1')");
            $conn->consulta_simple("Insert into producto_taxonomiap values(NULL,'".$id."','-1','".$_POST['sunat']."','1')");

            //Categoria
                //Revisar si existe la categoria
                $categoria = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '2' AND valor = '".$_POST['categoria']['valor']."'");

                if(!$categoria){
                    $id_c = $conn->consulta_id("INSERT INTO taxonomiap_valor VALUES(NULL,'2','".$_POST['categoria']['valor']."',NULL,1)");
                }else{
                    $id_c = $categoria['id'];
                }

                $producto_tax_cat = $conn->consulta_simple(
                    "INSERT INTO producto_taxonomiap VALUES(NULL,'".$id."','2','".$_POST['categoria']['valor']."','1')");

            //Tipo
                //Revisar si existe el tipo
                $tipo = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '3' AND valor = '".$_POST['tipo']['valor']."' AND padre = '".$id_c."'");

                if(!$tipo){
                    $conn->consulta_id("INSERT INTO taxonomiap_valor VALUES(NULL,'3','".$_POST['tipo']['valor']."','".$id_c."',1)");
                }

                $producto_tax_tip = $conn->consulta_simple(
                    "INSERT INTO producto_taxonomiap VALUES(NULL,'".$id."','3','".$_POST['tipo']['valor']."','1')");
            
            //Taxonomias dinamicas
                    
                    $dinamicas = $_POST['caracteristicas'];
                    if( is_array($dinamicas) ){
                        foreach ($dinamicas as $key => $dinamica) {
                            foreach ($dinamica as $llave => $value) {
                                $tax = $conn->consulta_arreglo("SELECT * FROM taxonomiap WHERE nombre = '".$llave."'");
                                if( !$tax ){
                                    $id_new = $conn->consulta_id("INSERT INTO taxonomiap VALUES(NULL,NULL,'".$llave."','1','1')");
                                }else{
                                    $id_new = $tax['id'];
                                }
                                $conn->consulta_simple(
                                    "INSERT INTO producto_taxonomiap VALUES(NULL,'".$id."','".$id_new."','".$value."','1')");
                            }
                        }
                    }
                    
        }   

        header('Content-Type: application/json');
        echo json_encode($id);

    break;

    case 'update_producto':
        
        // Modificar Producto
        $producto = $conn->consulta_arreglo("SELECT * FROM producto WHERE nombre = '".$_POST['anterior']."'");

        $r = $conn->consulta_simple(
            "UPDATE producto SET nombre = '".$_POST['valor']['nombre']."',precio_compra = '".$_POST['valor']['precio_compra']."',precio_venta = '".$_POST['valor']['precio_venta']."',incluye_impuesto = '".$_POST['valor']['incluye_impuesto']."',estado_fila = '1' WHERE id = '".$producto['id']."'"
        );

        if($r > 0){
            // Actualizar Taxonomias
            $conn->consulta_simple("UPDATE producto_taxonomiap SET valor = '".$_POST['valor']['nombre']."' WHERE id_taxonomiap = '1' AND id_producto = '".$producto['id']."'");
            $conn->consulta_simple("UPDATE producto_taxonomiap SET valor = '".$_POST['sunat']."' WHERE id_taxonomiap = '-1' AND id_producto = '".$producto['id']."'");

            //Categoria
                //Revisar si existe la categoria
                $categoria = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '2' AND valor = '".$_POST['categoria']['valor']."'");

                if(!$categoria){
                    $id_c = $conn->consulta_id("INSERT INTO taxonomiap_valor VALUES(NULL,'2','".$_POST['categoria']['valor']."',NULL,1)");
                    $producto_tax_cat = $conn->consulta_simple(
                        "INSERT INTO producto_taxonomiap VALUES(NULL,'".$producto['id']."','2','".$_POST['categoria']['valor']."','1')");
                }else{
                    $id_c = $categoria['id'];
                    $conn->consulta_simple("UPDATE producto_taxonomiap SET valor = '".$_POST['categoria']['valor']."' WHERE id_taxonomiap = '2' AND id_producto = '".$producto['id']."'");
                }
            
                //Tipo
                //Revisar si existe el tipo
                $tipo = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '3' AND valor = '".$_POST['tipo']['valor']."' AND padre = '".$id_c."'");

                if(!$tipo){
                    $conn->consulta_id("INSERT INTO taxonomiap_valor VALUES(NULL,'3','".$_POST['tipo']['valor']."','".$id_c."',1)");
                    $producto_tax_tip = $conn->consulta_simple(
                        "INSERT INTO producto_taxonomiap VALUES(NULL,'".$producto['id']."','3','".$_POST['tipo']['valor']."','1')");
                }else{
                    $conn->consulta_simple("UPDATE producto_taxonomiap SET valor = '".$_POST['tipo']['valor']."' WHERE id_taxonomiap = '3' AND id_producto = '".$producto['id']."'");
                }

            //Taxonomias dinamicas
                    
                    $dinamicas = $_POST['caracteristicas'];
                    if( is_array($dinamicas) ){
                        foreach ($dinamicas as $key => $dinamica) {
                            foreach ($dinamica as $llave => $value) {
                                $tax = $conn->consulta_arreglo("SELECT * FROM taxonomiap WHERE nombre = '".$llave."'");
                                if( !$tax ){
                                    $id_new = $conn->consulta_id("INSERT INTO taxonomiap VALUES(NULL,NULL,'".$llave."','1','1')");
                                    $conn->consulta_simple(
                                        "INSERT INTO producto_taxonomiap VALUES(NULL,'".$producto['id']."','".$id_new."','".$value."','1')");
                                }else{
                                    $id_new = $tax['id'];
                                    $tax_db = $conn->consulta_arreglo("SELECT * FROM producto_taxonomiap WHERE id_producto = '".$producto['id']."' AND id_taxonomiap = '".$tax['id']."'");
                                    if($tax_db){
                                        $id_new = $tax['id'];
                                        $conn->consulta_simple("UPDATE producto_taxonomiap SET valor = '".$value."' WHERE id_taxonomiap = '".$id_new."' AND id_producto = '".$producto['id']."'");
                                    }else{
                                        $conn->consulta_id(
                                            "INSERT INTO producto_taxonomiap VALUES(NULL,'".$producto['id']."','".$id_new."','".$value."','1')");
                                    }
                                    
                                }
                                
                            }
                        }
                    }

                    header('Content-Type: application/json');
                    echo json_encode($r);
                    
        }   

        

    break;

    case 'delete_producto':

        $producto = $conn->consulta_arreglo("SELECT * FROM producto WHERE nombre = '".$_POST['valor']."'");

        $r = $conn->consulta_simple("DELETE FROM producto WHERE nombre = '".$_POST['valor']."'");
        $r2 = $conn->consulta_simple("DELETE FROM producto_taxonomiap WHERE id_producto = '".$producto['id']."'");

        echo json_encode($r);
    break;
    
    case 'verify_producto_precio':
        
        $producto = $conn->consulta_arreglo("SELECT * FROM producto WHERE nombre = '".$_POST['producto']."'");

        if( !$producto ){
            $r = 0;
        }else{
            $r = 1;
        }

        header('Content-Type: application/json');
        echo json_encode($r);

    break;

    case 'verify_producto_precio_edit':
        
        $producto = $conn->consulta_arreglo("SELECT * FROM producto WHERE nombre = '".$_POST['producto']."'");

        if( !$producto ){

            $r = 0;

        }else{
            
            $precio = $conn->consulta_arreglo("SELECT * FROM productos_precios WHERE descripcion = '".$_POST['valor']."' AND id_producto = '".$producto['id']."'");

            if($precio){
                $r = 1;
            }else{
                $r = 0;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($r);

    break;

    case 'insert_precio_producto':
        
        // Verificar el producto
        $producto = $conn->consulta_arreglo("SELECT * FROM producto WHERE nombre = '".$_POST['producto']['nombre']."'");
        
        $id_p = null;

        if( !$producto ){
            $id_p = $conn->consulta_id("INSERT INTO producto VALUES(NULL,'".$_POST['producto']['nombre']."','".$_POST['producto']['precio_compra']."','".$_POST['producto']['precio_venta']."','".$_POST['producto']['incluye_impuesto']."','1')");
        }else{
            $id_p = $producto['id'];
        }

        $id_precio = $conn->consulta_id("INSERT INTO productos_precios VALUES(NULL,'".$id_p."','".$_POST['precio']['descripcion']."','".$_POST['precio']['precio_compra']."','".$_POST['precio']['precio_venta']."','".$_POST['precio']['incluye_impuesto']."','1','".$_POST['precio']['barcode']."','".$_POST['precio']['cantidad']."')");

        header('Content-Type: application/json');
        echo json_encode($id_precio);

    break;

    case 'update_precio_producto':
        
        // Verificar el producto
        $producto = $conn->consulta_arreglo("SELECT * FROM producto WHERE nombre = '".$_POST['producto']['nombre']."'");
    
        if( !$producto ){
            $id_precio = 0;
        }else{
            $id_precio = $conn->consulta_simple("UPDATE productos_precios SET descripcion = '".$_POST['precio']['descripcion']."',
            precio_compra = '".$_POST['precio']['precio_compra']."', precio_venta = '".$_POST['precio']['precio_venta']."', incluye_impuesto = '".$_POST['precio']['incluye_impuesto']."', estado_fila = '1', cantidad = '".$_POST['precio']['cantidad']."', barcode = '".$_POST['precio']['barcode']."'
            WHERE descripcion = '".$_POST['anterior']."' AND id_producto = '".$producto['id']."'");
        }

        

        header('Content-Type: application/json');
        echo json_encode($id_precio);

    break;

    case 'delete_precio':
        
        // Verificar el producto
        $producto = $conn->consulta_arreglo("SELECT * FROM producto WHERE nombre = '".$_POST['producto']."'");
    
        if( !$producto ){
            $id_precio = 0;
        }else{
            $id_precio = $conn->consulta_simple("DELETE FROM productos_precios WHERE descripcion = '".$_POST['valor']."' AND id_producto = '".$producto['id']."'");
        }


        header('Content-Type: application/json');
        echo json_encode($id_precio);

    break;

    case 'verify_category':
        
        $categoria = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '2' AND valor = '".$_POST['valor']."'");
        if( !$categoria ){
            $r = 0;
        }else{
            $r = 1;
        }
        header('Content-Type: application/json');
        echo json_encode($r);

    break;

    case 'insert_category':
        $id = $conn->consulta_id("INSERT INTO taxonomiap_valor VALUES(NULL,'2','".$_POST['valor']."',NULL,1)");
        header('Content-Type: application/json');
        echo json_encode($id);
    break;

    case 'update_category':
        $categoria = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '2' AND valor = '".$_POST['anterior']."'");
        if($categoria){
            $conn->consulta_simple("UPDATE taxonomiap_valor set valor = '".$_POST['valor']."' WHERE id = '".$categoria['id']."'");
            header('Content-Type: application/json');
            echo json_encode($categoria['id']);
        }else{
            header('Content-Type: application/json');
            echo json_encode(0);
        }
        
    break;

    case 'delete_category':
        $r = $conn->consulta_simple("DELETE FROM taxonomiap_valor WHERE id_taxonomiap = '2' AND valor = '".$_POST['valor']."'");
        //$r2 = $conn->consulta_simple("DELETE FROM producto_taxonomiap WHERE id_taxonomiap = '2' AND valor = '".$_POST['valor']."'");
        echo json_encode($r);
    break;

    case 'verify_taxonomiap':

        $taxonomiap = $conn->consulta_arreglo("SELECT * FROM taxonomiap WHERE nombre = '".$_POST['valor']."'");
        if( !$taxonomiap ){
            $r = 0;
        }else{
            $r = 1;
        }
        header('Content-Type: application/json');
        echo json_encode($r);

    break;

    case 'insert_taxonomiap':
        $id = $conn->consulta_id("INSERT INTO taxonomiap VALUES(NULL,NULL,'".$_POST['valor']."','1',1)");
        header('Content-Type: application/json');
        echo json_encode($id);
    break;

    case 'update_taxonomiap':
    
        $taxonomoia = $conn->consulta_arreglo("SELECT * FROM taxonomiap WHERE nombre = '".$_POST['anterior']."'");

        if($taxonomoia){
            $r = $conn->consulta_simple("UPDATE taxonomiap set nombre = '".$_POST['valor']."' WHERE id = '".$taxonomoia['id']."'");
            header('Content-Type: application/json');
            echo json_encode($r);
        }else{
            header('Content-Type: application/json');
            echo json_encode(0);
        }
        
    break;

    case 'delete_taxonomiap':
        $taxonomiap = $conn->consulta_arreglo("SELECT * FROM taxonomiap WHERE WHERE nombre = '".$_POST['valor']."' AND id > 3");
        $r = $conn->consulta_simple("DELETE FROM taxonomiap WHERE nombre = '".$_POST['valor']."' AND id > 3");
        $r2 = $conn->consulta_simple("DELETE FROM producto_taxonomiap WHERE id_taxonomiap = '".$taxonomiap['id']."' AND valor = '".$_POST['valor']."'");
        echo json_encode($r);
    break;
    
    case 'verify_tipo':
        
        $categoria = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '2' AND valor = '".$_POST['padre']."'");
        if( !$categoria ){
            $r = 0;
        }else{
            $r = 1;
        }
        header('Content-Type: application/json');
        echo json_encode($r);

    break;

    case 'verify_tipo_edit':

        $categoria = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '2' AND valor = '".$_POST['padre']."'");
        $tipo = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '3' AND valor = '".$_POST['valor']."' AND padre = '".$categoria['id']."'");
        
        if( !$tipo ){
            $r = 0;
        }else{
            $r = 1;
        }
        header('Content-Type: application/json');
        echo json_encode($r);

    break;

    case 'insert_tipo':
        $categoria = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '2' AND valor = '".$_POST['padre']."'");
        
        $tipo = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE padre = '".$categoria['id']."' AND valor = '".$_POST['valor']."' AND id_taxonomiap = '3'");
        
        if(!$tipo){
            $id = $conn->consulta_id("INSERT INTO taxonomiap_valor VALUES(NULL,'3','".$_POST['valor']."','".$categoria['id']."',1)");   
        }else{
            $id = 0;
        }

        header('Content-Type: application/json');
        echo json_encode($id);
    break;

    case 'update_tipo':
        
        $categoria = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '2' AND valor = '".$_POST['padre']."'");
        if($categoria){
            $r = $conn->consulta_simple("UPDATE taxonomiap_valor set valor = '".$_POST['valor']."' WHERE padre = '".$categoria['id']."' AND valor = '".$_POST['anterior']."'");
            header('Content-Type: application/json');
            echo json_encode($r);
        }
    break;

    case 'delete_tipo':
        
        $categoria = $conn->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '2' AND valor = '".$_POST['padre']."'");
        if($categoria){
            $r = $conn->consulta_simple("DELETE FROM taxonomiap_valor WHERE padre = '".$categoria['id']."' AND valor = '".$_POST['valor']."'");
            header('Content-Type: application/json');
            echo json_encode($r);
        }
    break;

    default:
        # code...
    break;
}

