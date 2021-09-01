<?php
require '../vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
require_once('../nucleo/producto.php');
require_once('../nucleo/movimiento_producto.php');
require_once('../nucleo/taxonomiap.php');
require_once('../nucleo/turno.php');
$objturno = new turno();
$objproducto = new producto();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {

        case 'add':
            $objproducto = new producto();
            $objproducto->setVar('id', $_POST['id']);
            $objproducto->setVar('nombre', $_POST['nombre']);
            $objproducto->setVar('unidad', $_POST['unidad']);
            $objproducto->setVar('precio_compra', $_POST['precio_compra']);
            $objproducto->setVar('precio_venta', $_POST['precio_venta']);
            $objproducto->setVar('incluye_impuesto', $_POST['incluye_impuesto']);
            $objproducto->setVar('estado_fila', $_POST['estado_fila']);
            $idp = $objproducto->insertDB();
            $objconn = new producto();
            $now = date('Y-m-d H:m:s');
            $objconn->consulta_simple("Insert into producto_taxonomiap values(NULL,'".$idp."','1','".$_POST["nombre"]."','1')");
            // $objconn->consulta_simple("Insert into estados_terreno values(NULL,'1','".$idp."','".$_COOKIE['id_usuario']."',NULL,'".$now."')");
            if($_POST['icbper'] == 1){
                $objconn->consulta_simple("Insert into ley_plastico values(NULL,'".$idp."','1')");
            }
            echo json_encode($idp);
        break;
        
        case 'add_status':
            $now = date('Y-m-d H:m:s');
            $objproducto = new producto();
            $productos = $objproducto->consulta_matriz("SELECT * FROM producto WHERE id NOT IN (SELECT DISTINCT id_producto FROM estados_terreno)");
            $count = 0;
            foreach ($productos as $key => $producto) {
                $objproducto->consulta_simple("Insert into estados_terreno values(NULL,'1','".$producto["id"]."','".$_COOKIE['id_usuario']."',NULL,'".$now."')");
                $count++;
            }

            echo json_encode(["count" => $count]);

        break;
        case 'info_terreno':
            $objconn = new producto();
            $data = [];
            $id_producto = $_POST['id_producto'];
            $data["producto"] = $objconn->consulta_arreglo("SELECT * FROM producto WHERE id = {$id_producto}");
            $data["estados_terreno"] = $objconn->consulta_matriz("SELECT * FROM estados_terreno WHERE id_producto = {$id_producto}");
            foreach ($data["estados_terreno"] as $key => &$estado) {
                $estado['estado'] = $objconn->consulta_arreglo("SELECT * FROM estados WHERE id = {$estado['id_estado']}");
                $estado['usuario'] = $objconn->consulta_arreglo("SELECT * FROM usuario WHERE id = {$estado['usuario_id']}");
                /*
                if( $estado['id_venta'] != NULL ){
                    $estado['venta'] = $objconn->consulta_arreglo("SELECT * FROM venta WHERE id = {$estado['id_venta']} AND estado_fila = 1");
                    $estado['venta']['cliente'] = $objconn->consulta_arreglo("SELECT * FROM cliente WHERE id = {$estado['venta']['id_cliente']}");
                    $estado['venta']['detalles'] = $objconn->consulta_arreglo("SELECT * FROM producto_venta WHERE id_venta = {$estado['id_venta']}");
                }
                */
            }

            $venta = $objconn->consulta_arreglo("SELECT * FROM VENTA WHERE id IN (SELECT DISTINCT id_venta from producto_venta where estado_fila= 1 AND id_producto = {$id_producto})");

            if($venta)
            {
                $data["venta"] = $venta;
                $data['venta']['cliente'] = $objconn->consulta_arreglo("SELECT * FROM cliente WHERE id = {$venta['id_cliente']}");
                $data["venta"]["detalles"] = $objconn->consulta_arreglo("SELECT * FROM producto_venta WHERE estado_fila= 1 AND id_venta = {$venta['id']}");
                $data['venta']['pago'] = $objconn->consulta_matriz("SELECT * FROM pagos WHERE id_venta = {$venta['id']}");
            }

            echo json_encode($data);

        break;
        case 'mod':
            $objproducto->setVar('id', $_POST['id']);
            $objproducto->setVar('nombre', $_POST['nombre']);
            $objproducto->setVar('unidad', $_POST['unidad']);
            $objproducto->setVar('precio_compra', $_POST['precio_compra']);
            $objproducto->setVar('precio_venta', $_POST['precio_venta']);
            $objproducto->setVar('incluye_impuesto', $_POST['incluye_impuesto']);
            $objproducto->setVar('estado_fila', $_POST['estado_fila']);

            // echo "UPDATE FROM producto_taxonomiap SET valor = '".$_POST['nombre']."' WHERE id_producto = ".$_POST['id']." AND id_taxonomiap = 1";

            $objproducto->consulta_simple("UPDATE `producto_taxonomiap` SET valor = '".$_POST['nombre']."' WHERE id_producto = ".$_POST['id']." AND id_taxonomiap = 1");

            if($_POST['icbper'] == 1){
                $objproducto->consulta_simple("Insert into ley_plastico values(NULL,'".$_POST['id']."','1')");
            }else{
                $objproducto->consulta_simple("DELETE FROM ley_plastico WHERE id_producto= '".$_POST['id']."'");
            }

            echo json_encode($objproducto->updateDB());
            break;

        case 'del':
            //Eliminamos Taxonomoias
            $objconn = new producto();
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES=0;");
            $objconn->consulta_simple("Delete from producto_taxonomiap where id_producto = '".$_POST['id']."'");
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES=1;");
            //Eliminamos Producto
            $objproducto = new producto();
            $objproducto->setVar('id', $_POST['id']);
            echo json_encode($objproducto->deleteDB());
            break;

        case 'get':
            $res = $objproducto->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'get_precio':
            $res = $objproducto->consulta_arreglo("SELECT * FROM productos_precios WHERE id = ".$_POST['id']);
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'getventa':
            $res = $objproducto->searchDB($_POST['id'], 'id', 1);
            // echo "DDDDDDDD  ".intval($res[0]['incluye_impuesto']);
            $res[0]["ivgAux"] = intval($res[0]['incluye_impuesto']);
            if (is_array($res)) {

                if (intval($res[0]['incluye_impuesto']) == 1){
                    $res[0]['incluye_impuesto'] = "SI";
                }else {
                    $res[0]['incluye_impuesto'] = "NO";
                }

                $res[0]['precios'] = $objproducto->consulta_matriz("SELECT * FROM productos_precios WHERE id_producto = ".$_POST['id']);

                $res[0]['stock'] = $objproducto->getStock($res[0]["id"],$_POST["id_almacen"]);
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'server_side':
            $result = $objproducto->ServerSide($_POST);
            echo json_encode($result);
        break; 
        
        case 'server_side_cotos':
            
            $result = $objproducto->ServerSideCotos($_POST);
            echo json_encode($result);
        break;


        case 'server_side_producto_no_insumo':            
            //$result = $objproducto->ServerSideNoInsumos($_POST);
            //echo json_encode($result);
        break;


        case 'list':
            $res = $objproducto->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    if (intval($act['incluye_impuesto']) == 1){
                        $act['incluye_impuesto'] = "SI";
                    }else {
                        $act['incluye_impuesto'] = "NO";
                    }
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objproducto->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'add-precios':
            $id = $_POST['id'];
            $id_producto = $_POST['id_producto'];
            $descripcion = $_POST['descripcion'];
            $precio_compra = $_POST['precio_compra'];
            $precio_venta = $_POST['precio_venta'];
            $incluye_impuesto = $_POST['incluye_impuesto'];
            $estado_fila = $_POST['estado_fila'];
            $barcode = $_POST['barcode'];
            $cantidad = $_POST['cantidad'];

            $objconn = new producto();
            $r = $objconn->consulta_simple("Insert into productos_precios values('".$id."','".$id_producto."','".$descripcion."','".$precio_compra."','".$precio_venta."','".$incluye_impuesto."','".$estado_fila."','".$barcode."',".$cantidad.")");
            echo json_encode($r);
        break;
       
        case 'mod_precios':
            $id = $_POST['id'];
            $id_producto = $_POST['id_producto'];
            $descripcion = $_POST['descripcion'];
            $precio_compra = $_POST['precio_compra'];
            $precio_venta = $_POST['precio_venta'];
            $incluye_impuesto = $_POST['incluye_impuesto'];
            $estado_fila = $_POST['estado_fila'];
            $barcode = $_POST['barcode'];
            $cantidad = $_POST['cantidad'];

            $objconn = new producto();
            $r = $objconn->consulta_simple("UPDATE productos_precios SET id = '".$id."',id_producto = '".$id_producto."',descripcion = '".$descripcion."',precio_compra = '".$precio_compra."',precio_venta = '".$precio_venta."',incluye_impuesto = '".$incluye_impuesto."',estado_fila = '".$estado_fila."',barcode = '".$barcode."',cantidad = '".$cantidad."' WHERE id = ".$id);
            echo json_encode($r);
        break;
        case 'updateValor':
          
            $updateVal = $_POST['valor'];
            $objconn = new producto();
            $r = $objconn->consulta_simple("UPDATE producto SET precio_venta= (precio_venta*'".$updateVal."')+precio_venta");
            echo json_encode($r);
        break;

        case 'del_precio':
            $id = $_POST['id'];
            $objconn = new producto();
            $r = $objconn->consulta_simple("DELETE FROM productos_precios WHERE id = ".$id);
            echo json_encode($r);
        break;

        case 'img':
            $id_gen = $_POST["id"];
            $exito = 0;
            $key = $_FILES["img"];
            $tipo = 0;
            $id_usuario = $id_gen;
            $ruta = "../recursos/uploads/productos/";
            $tipo_imagen = $key['type'];
            if (strpos($tipo_imagen, "gif")) {
                $tipo = 1;
            } else {
                if (strpos($tipo_imagen, "jpeg")) {
                    $tipo = 2;
                } else {
                    if (strpos($tipo_imagen, "jpg")) {
                        $tipo = 2;
                    } else {
                        if (strpos($tipo_imagen, "png")) {
                            $tipo = 3;
                        } else {
                            $tipo = 0;
                        }
                    }
                }
            }
            if ($tipo > 0) {
                if (file_exists($ruta.$id_usuario.".png")) {
                    unlink($ruta.$id_usuario.".png");
                }
                $exito = 1;
                $nombre_archivo = $id_usuario;
                $img_original = 0;
                switch ($tipo) {
                    case 1:
                        $img_original = imagecreatefromgif($key["tmp_name"]);
                        break;

                    case 2:
                        $img_original = imagecreatefromjpeg($key["tmp_name"]);
                        break;

                    case 3:
                        $img_original = imagecreatefrompng($key["tmp_name"]);
                        break;
                }
                $ancho = imagesx($img_original);
                $alto = imagesy($img_original);
                //Se define el maximo ancho o alto que tendra la imagen final
                $max_ancho = 250;
                $max_alto = 250;

                //Se calcula ancho y alto de la imagen final
                $x_ratio = $max_ancho / $ancho;
                $y_ratio = $max_alto / $alto;

                //Si el ancho y el alto de la imagen no superan los maximos,
                //ancho final y alto final son los que tiene actualmente
                if( ($ancho <= $max_ancho) && ($alto <= $max_alto) ){//Si ancho
                        $ancho_final = $ancho;
                        $alto_final = $alto;
                }
                /*
                 * si proporcion horizontal*alto mayor que el alto maximo,
                 * alto final es alto por la proporcion horizontal
                 * es decir, le quitamos al alto, la misma proporcion que
                 * le quitamos al alto
                 *
                */
                elseif (($x_ratio * $alto) < $max_alto){
                        $alto_final = ceil($x_ratio * $alto);
                        $ancho_final = $max_ancho;
                }
                /*
                 * Igual que antes pero a la inversa
                */
                else{
                        $ancho_final = ceil($y_ratio * $ancho);
                        $alto_final = $max_alto;
                }

                //Creamos una imagen en blanco de tamaño $ancho_final  por $alto_final .
                $tmp=imagecreatetruecolor($ancho_final,$alto_final);

                //Copiamos $img_original sobre la imagen que acabamos de crear en blanco ($tmp)
                imagecopyresampled($tmp,$img_original,0,0,0,0,$ancho_final, $alto_final,$ancho,$alto);

                //Se destruye variable $img_original para liberar memoria
                imagedestroy($img_original);

                //Se crea la imagen final en el directorio indicado
                $ruta = $ruta.$id_usuario.'.png';
                imagepng($tmp, $ruta);
            }
            echo json_encode($exito);
            break;

            case 'searchsale':
            $valor = $_POST["bus"];
            $objproducto = new producto();
            $res0 = $objproducto->consulta_matriz("select * from producto where id = '".$valor."' AND estado_fila = 1");
            if(!is_array($res0)){
                $res1 = $objproducto->consulta_matriz("select * from producto where nombre LIKE '%".$valor."%' AND estado_fila = 1");
                if(is_array($res1)){
                    foreach ($res1 as &$act) {
                        $taxs = $objproducto->consulta_matriz("Select * from producto_taxonomiap where id_producto = '".$act["id"]."'");
                        $act["taxonomias"] = $taxs;
                    }
                    echo json_encode($res1);
                }else{
                    echo json_encode(0);
                }
            }else{
                foreach ($res0 as &$act) {
                    $taxs = $objproducto->consulta_matriz("Select * from producto_taxonomiap where id_producto = '".$act["id"]."'");
                    $act["taxonomias"] = $taxs;
                }
                echo json_encode($res0);
            }
            break;

        case 'act':
            $res = $objproducto->consulta_matriz("SELECT * FROM producto");
            for($i=0; $i<count($res); $i++){
                $objproducto->consulta_simple("UPDATE `producto_taxonomiap` SET valor = '".$res[$i]['nombre']."' WHERE id_producto = ".$res[$i]['id']." AND id_taxonomiap = 1");
            }
            break;

        case 'import':
            $input_precios = explode(",",$_POST['precios']);
            $alphas = range('A', 'Z');
            $obj_taxonomia = new taxonomiap();
            $taxonomias = $obj_taxonomia->consulta_matriz("SELECT * from taxonomiap where nombre <> 'Nombre'");
            //var_dump($taxonomias);
            //return;
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue($alphas[0].'1', 'producto');
            $sheet->setCellValue($alphas[1].'1', 'precio_compra');
            $sheet->setCellValue($alphas[2].'1', 'precio_venta');

            $activity_alpha = 3;

            foreach ($taxonomias as $key => $taxonomia) {
                if($taxonomia['nombre'] != 'CODIGO BARRA CLOUD'){
                    $sheet->setCellValue($alphas[$activity_alpha].'1', $taxonomia['nombre']);
                    $activity_alpha++;
                }
            }

            $sheet->setCellValue($alphas[$activity_alpha].'1', 'tipo impuesto');
            $activity_alpha++;
            $sheet->setCellValue($alphas[$activity_alpha].'1', 'stock');
            $activity_alpha++;
            $sheet->setCellValue($alphas[$activity_alpha].'1', 'almacen');
            $activity_alpha++;
            $sheet->setCellValue($alphas[$activity_alpha].'1', 'fecha vencimiento');
            $activity_alpha++;
            $sheet->setCellValue($alphas[$activity_alpha].'1', 'lote');
            $activity_alpha++;
            $sheet->setCellValue($alphas[$activity_alpha].'1', 'unidad_medida');
            $activity_alpha++;
            foreach ($input_precios as $key => $value) {
                $sheet->setCellValue($alphas[$activity_alpha].'1', $value);
                $activity_alpha++;
                $cantPre = str_replace("pre","cant",$value);
                $sheet->setCellValue($alphas[$activity_alpha].'1', $cantPre);
                $activity_alpha++;
            }

            /*
            $sheet->setCellValue($alphas[$activity_alpha].'1', 'pre-LANZAMIENTO');
            $activity_alpha++;
            $sheet->setCellValue($alphas[$activity_alpha].'1', 'pre-PRE VENTA');
            $activity_alpha++;
            $sheet->setCellValue($alphas[$activity_alpha].'1', 'pre-INICIO HABITACION URBANA');
            $activity_alpha++;
            $sheet->setCellValue($alphas[$activity_alpha].'1', 'pre-INICIO DE OBRA');
            $activity_alpha++;
            $sheet->setCellValue($alphas[$activity_alpha].'1', 'pre-ENTREGA INMEDIATA');
            $activity_alpha++;
            */

            $writer = new Xlsx($spreadsheet);
            $filename = 'formato_importacion';

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'. $filename .'.xlsx"');
            header('Cache-Control: max-age=0');

            $writer->save('php://output'); // download file

        break;

        case 'import_upload':

            $target_dir = "../recursos/uploads/";
            $target_file = $target_dir . basename($_FILES["file"]["name"]);
            $uploadOk = 1;
            $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

            if($fileType != "xlsx") {
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {

            // if everything is ok, try to upload file
            } else {

                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {

                    if (file_exists($target_file)) {
                        /**  Identify the type of $inputFileName  **/
                        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($target_file);

                        /**  Create a new Reader of the type that has been identified  **/
                        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                        $reader->setLoadSheetsOnly('Worksheet');
                        /**  Load $inputFileName to a Spreadsheet Object  **/
                        $spreadsheet = $reader->load($target_file);

                        /**  Convert Spreadsheet Object to an Array for ease of use  **/
                        $schdeules = $spreadsheet->getActiveSheet()->toArray();
                        $attributes = $schdeules[0];
                        //$normal_attributes = ["producto","precio_compra","precio_venta","tipo impuesto","stock",'pre-LANZAMIENTO','pre-PRE VENTA','pre-INICIO HABITACION URBANA','pre-INICIO DE OBRA','pre-ENTREGA INMEDIATA'];
                        $normal_attributes = ["producto","precio_compra","precio_venta","tipo impuesto","stock","almacen", "fecha vencimiento","lote","unidad_medida"];

                        $prices_attributes= [];
                        $cantidad_attributes = [];
                        $taxonomias_index = [];


                        foreach ($attributes as $key => $value) {

                            if($value != null){

                                if( !in_array($value,$normal_attributes) ){

                                    if (strpos($value,'pre-') !== false) {
                                        $prices_attributes[] = $key;
                                    }elseif (strpos($value,'cant-') !== false) {
                                        $cantidad_attributes[] = $key;
                                    }else{
                                        $taxonomias_index[] = $key;
                                    }
                                }
                            }
                        }

                        // Guia de entrada
                        $obj_guia = new taxonomiap();
                        $now = date('Y-m-d H:m:s');
                        $fecha = date('Y-m-d');
                        $n = strtotime("now");
                        $id_guia = $obj_guia->consulta_id("INSERT INTO guia_producto values(NULL,'".$_COOKIE['id_usuario']."','".$now."','".$fecha."','1',NULL,'".$n."','1')");

                        // echo "dkm ".end($taxonomias_index);

                        foreach ($schdeules as $key => $schdeule) {

                            if($key != 0){

                                if($schdeule[0] != null){
                                    $last_index_taxonomias_index = end($taxonomias_index);
                                    $obj_prd = new producto();
                                    $obj_prd->setVar('id', null);
                                    $obj_prd->setVar('nombre', $schdeule[0]);
                                    $obj_prd->setVar('precio_compra', $schdeule[1]);
                                    $obj_prd->setVar('precio_venta', $schdeule[2]);
                                    $obj_prd->setVar('unidad', $schdeule[13]);
                                    // $obj_prd->setVar('incluye_impuesto', $schdeule[$last_index_taxonomias_index + 1]);
                                    $obj_prd->setVar('incluye_impuesto',  $schdeule[8]);

                                    $obj_prd->setVar('estado_fila', 1);
                                    $idp = $obj_prd->insertDB();

                                    $obj_prd->consulta_simple("Insert into producto_taxonomiap values(NULL,'".$idp."','1','".$schdeule[0]."','1')");

                                    foreach ($taxonomias_index as $key => $value) {

                                        $valor_taxonomia = $attributes[$value];

                                        $obj_tax = new taxonomiap();

                                        $taxonomia_by_name = $obj_tax->consulta_arreglo("SELECT * FROM taxonomiap WHERE nombre = '".$valor_taxonomia."' ");

                                        if($taxonomia_by_name['id'] == 2 || $taxonomia_by_name['id'] == 3){

                                            $taxonomiap_valor = $obj_tax->consulta_arreglo("SELECT * FROM taxonomiap_valor WHERE id_taxonomiap = '".$taxonomia_by_name['id']."' AND valor = '".$schdeule[$value]."'");
                                            if(!$taxonomiap_valor){

                                                $id = $obj_tax->consulta_id("INSERT INTO taxonomiap_valor values(NULL,'".$taxonomia_by_name['id']."','".$schdeule[$value]."',NULL,'1')");

                                                if($taxonomia_by_name['id'] == 3){
                                                    $obj_padre = $obj_tax->consulta_arreglo("SELECT * FROM taxonomiap_valor where valor = '".$schdeule[$value-1]."'");
                                                    $obj_tax->consulta_simple("UPDATE taxonomiap_valor SET padre = '".$obj_padre['id']."' WHERE id = '".$id."'");
                                                }

                                            }

                                        }

                                        $obj_tax->consulta_simple("Insert into producto_taxonomiap values(NULL,'".$idp."','".$taxonomia_by_name['id']."','".$schdeule[$value]."','1')");

                                    }

                                    $fecha_vencimiento = null;
                                    if($schdeule[11] != null){
                                        list($mes, $dia, $año) = explode('/', $schdeule[11]);
                                        $fecha_vencimiento = $año."-".$mes."-".$dia;
                                    }


                                    $stocks  = explode("|", $schdeule[9]);
                                    $almacenes = explode("|", $schdeule[10]);

                                    for($i=0; $i<count($stocks); $i++){
                                        $id_movimiento_producto=0;
                                        $almacen = $obj_guia->consulta_arreglo("SELECT * FROM almacen WHERE nombre = '".$almacenes[$i]."'");                                        
                                        if($fecha_vencimiento != null){                                            
                                            $id_movimiento_producto = $obj_guia->consulta_id("INSERT INTO movimiento_producto values(NULL,'".$idp."','".$almacen['id']."','".$stocks[$i]."',NULL,'ALMACEN','".$_COOKIE['id_usuario']."','1','".$now."','".$fecha."','1','".$fecha_vencimiento."','".$schdeule[12]."','1',NULL)");
                                        }else{                                            
                                            $id_movimiento_producto = $obj_guia->consulta_id("INSERT INTO movimiento_producto values(NULL,'".$idp."','".$almacen['id']."','".$stocks[$i]."',NULL,'ALMACEN','".$_COOKIE['id_usuario']."','1','".$now."','".$fecha."','1',null,'".$schdeule[12]."','1',NULL)");
                                        }

                                        $obj_guia->consulta_simple("INSERT INTO guia_movimiento values(NULL,'".$id_movimiento_producto."','".$id_guia."','1')");
                                    }

                                    // Precios productos
                                    if(count($prices_attributes) > 0){
                                        foreach ($prices_attributes as $key => $value) {
                                            $valor_price = $attributes[$value];
                                            $cantidad = floatval($schdeule[$value+1]);
                                            $valor_price_explode = explode("-",$valor_price);
                                            $precios = $schdeule[$value];
                                            $obj_tax = new taxonomiap();
                                            // echo "Insert into productos_precios values(null,'".$idp."','".$valor_price_explode[1]."','".$schdeule[1]."','".$precios."','".$schdeule[7]."','1','','".$cantidad."')";
                                            $obj_tax->consulta_simple("Insert into productos_precios values(null,'".$idp."','".$valor_price_explode[1]."','".$schdeule[1]."','".$precios."','".$schdeule[8]."','1','','".$cantidad."')");
                                        }
                                    }


                                }
                            }
                        }

                        if(unlink($target_file)){
                           $uploadOk = 1;
                        }

                         echo json_encode($uploadOk);

                    }
                } else {

                    echo json_encode(0);
                    return;

                }
            }

        break;

        case 'codigovalidar':
            $objconn = new producto();
            $r = $objconn->consulta_arreglo("SELECT valor FROM producto_taxonomiap WHERE valor = '".$_POST['codigo']."'");
          //  echo($_POST['codigo']);
            echo json_encode($r);
        break;

        case 'getBolsa':
            $objconn = new producto();
            // echo "SELECT * FROM ley_plastico WHERE id_producto = '".$_POST['id_producto']."'";
            $r = $objconn->consulta_arreglo("SELECT * FROM ley_plastico WHERE id_producto = '".$_POST['id_producto']."'");
            echo json_encode($r);
        break;

        
        case 'import_seleccionados':

            $target_dir = "../recursos/uploads/";
            $target_file = $target_dir . basename($_FILES["file"]["name"]);
            $uploadOk = 1;
            $fileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

            if($fileType != "xlsx") {
                $uploadOk = 0;
            }
            // Check if $uploadOk is set to 0 by an error
            if ($uploadOk == 0) {

            // if everything is ok, try to upload file
            } else {

                if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {

                    if (file_exists($target_file)) {
                        /**  Identify the type of $inputFileName  **/
                        $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($target_file);

                        /**  Create a new Reader of the type that has been identified  **/
                        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                        $reader->setLoadSheetsOnly('Sheet1');
                        /**  Load $inputFileName to a Spreadsheet Object  **/
                        $spreadsheet = $reader->load($target_file);

                        /**  Convert Spreadsheet Object to an Array for ease of use  **/
                        $schdeules = $spreadsheet->getActiveSheet()->toArray();

                        $id_turno = $objturno->consulta_arreglo("SELECT id FROM turno WHERE estado_fila = 1 LIMIT 1")[0];

                        $series = $objturno->consulta_arreglo("Select * from configuracion where id = 1");

                        foreach($schdeules as $schdeule) {
                            if($schdeule[0] > 0){
                                $obj = new movimiento_producto();
                                $obj->setVar('id', null);
                                $obj->setVar('id_producto', $schdeule[0]);
                                $obj->setVar('id_almacen', $schdeule[2]);
                                $obj->setVar('cantidad', $schdeule[4]);
                                $obj->setVar('costo', null);
                                $obj->setVar('tipo_movimiento', 'ALMACEN');
                                $obj->setVar('id_usuario', 1);
                                $obj->setVar('id_turno', $id_turno);
                                $obj->setVar('fecha', date('Y-m-d H:m:s'));
                                $obj->setVar('fecha_cierre', $series['fecha_cierre']);
                                $obj->setVar('estado_fila', 1);
                                $obj->insertDB();
                            }                            
                        }

                        if(unlink($target_file)){
                           $uploadOk = 1;
                        }
                        echo($uploadOk);

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
?>