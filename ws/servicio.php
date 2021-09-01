<?php

require_once('../nucleo/servicio.php');
$objservicio = new servicio();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objservicio = new servicio();
            $objservicio->setVar('id', $_POST['id']);
            $objservicio->setVar('nombre', $_POST['nombre']);
            $objservicio->setVar('precio_venta', $_POST['precio_venta']);
            $objservicio->setVar('incluye_impuesto', $_POST['incluye_impuesto']);
            $objservicio->setVar('estado_fila', $_POST['estado_fila']);
            $ids = $objservicio->insertDB();
            $objconn = new servicio();
            $objconn->consulta_simple("Insert into servicio_taxonimias values(NULL,'".$ids."','1','".$_POST["nombre"]."','1')");
            echo json_encode($ids);
            break;

        case 'mod':
            $objservicio->setVar('id', $_POST['id']);
            $objservicio->setVar('nombre', $_POST['nombre']);
            $objservicio->setVar('precio_venta', $_POST['precio_venta']);
            $objservicio->setVar('incluye_impuesto', $_POST['incluye_impuesto']);
            $objservicio->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objservicio->updateDB());
            break;

        case 'del':
            //Eliminamos Taxonomoias
            $objconn = new servicio();
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES=0;");
            $objconn->consulta_simple("Delete from servicio_taxonimias where id_servicio = '".$_POST['id']."'");
            $objconn->consulta_simple("SET SQL_SAFE_UPDATES=1;");
            //Eliminamos Servicio
            $objservicio = new servicio();
            $objservicio->setVar('id', $_POST['id']);
            echo json_encode($objservicio->deleteDB());
            break;

        case 'get':
            $res = $objservicio->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objservicio->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    if (intval($act['incluye_impuesto']) == 1){
                        $act['incluye_impuesto'] = "SI";
                    }
                    else {
                        $act['incluye_impuesto'] = "NO";
                    }
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objservicio->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    if (intval($act['incluye_impuesto']) == 1){
                        $act['incluye_impuesto'] = "SI";
                    }
                    else {
                        $act['incluye_impuesto'] = "NO";
                    }
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'img':
            $id_gen = $_POST["id"];
            $exito = 0;
            $key = $_FILES["img"];
            $tipo = 0;
            $id_usuario = $id_gen;
            $ruta = "../recursos/uploads/servicios/";
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
            $objservicio = new servicio();
            $res0 = $objservicio->consulta_matriz("select * from servicio where id = '".$valor."' AND estado_fila = 1");
            if(!is_array($res0)){
                $res1 = $objservicio->consulta_matriz("select * from servicio where nombre LIKE '%".$valor."%' AND estado_fila = 1");
                if(is_array($res1)){
                    foreach ($res1 as &$act) {
                        $taxs = $objservicio->consulta_matriz("Select * from servicio_taxonimias where id_servicio = '".$act["id"]."'");
                        $act["taxonomias"] = $taxs;
                    }
                    echo json_encode($res1);
                }else{ 
                    echo json_encode(0);
                }
            }else{
                foreach ($res0 as &$act) {
                    $taxs = $objservicio->consulta_matriz("Select * from servicio_taxonimias where id_servicio = '".$act["id"]."'");
                    $act["taxonomias"] = $taxs;
                }
                echo json_encode($res0);
            }
            break;

            case 'reporte':
                //echo("entro");
                $fechaInicio = $_POST['fecha_inicio'];
                $fechaFin = $_POST['fecha_fin'];

                $objservicio = new servicio();

                $objs = $objservicio->consulta_matriz(
                    "SELECT pv.id_servicio AS id_servicio,s.nombre as nombre,s.precio_venta as precio_venta,
                     SUM(pv.cantidad) AS cantidad, SUM(pv.total) AS totalventa, v.fecha_cierre
                    FROM servicio_venta pv
                    INNER JOIN venta v ON (v.id=pv.id_venta)
                    INNER JOIN servicio s on (s.id=pv.id_servicio)
                    WHERE v.estado_fila = 1 AND v.fecha_cierre BETWEEN '{$fechaInicio}' AND '{$fechaFin}'
                    GROUP BY pv.id_servicio
                    ORDER BY totalventa DESC"
                );

                
          /*   $objs = $conn->consulta_matriz("SELECT pv.id_producto AS id_producto, p.nombre as nombre, p.precio_compra as precio_compra,
            p.precio_venta as precio_venta,SUM(pv.cantidad) AS cantidad, SUM(pv.total) AS totalventa, v.fecha_cierre
            FROM producto_venta pv
            INNER JOIN venta v ON v.id = pv.id_venta 
            INNER JOIN producto p ON pv.id_producto = p.id
            WHERE v.estado_fila = 1 AND v.fecha_cierre BETWEEN '{$fechaInicio}' AND '{$fechaFin}'
            GROUP BY pv.id_producto
            ORDER BY totalventa desc"); */

                echo json_encode($objs);
                // $totalvendido = $conn->consulta_arreglo("SELECT sum(sv.total)as total FROM venta v inner join servicio_venta sv on (v.id=sv.id_venta) where v.estado_fila=1 and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59'");
            break;
    }
}?>