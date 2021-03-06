<?php

require_once('../nucleo/configuracion.php');
$objconfiguracion = new configuracion();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':
            $objconfiguracion->setVar('id', $_POST['id']);
            $objconfiguracion->setVar('fecha_cierre', $_POST['fecha_cierre']);
            $objconfiguracion->setVar('nombre_negocio', $_POST['nombre_negocio']);
            $objconfiguracion->setVar('ruc', $_POST['ruc']);
            $objconfiguracion->setVar('direccion', $_POST['direccion']);
            $objconfiguracion->setVar('tipo_negocio', $_POST['tipo_negocio']);
            $objconfiguracion->setVar('telefono', $_POST['telefono']);
            $objconfiguracion->setVar('pagina_web', $_POST['pagina_web']);
            $objconfiguracion->setVar('razon_social', $_POST['razon_social']);
            $objconfiguracion->setVar('moneda', $_POST['moneda']);
            $objconfiguracion->setVar('serie_boleta', $_POST['serie_boleta']);
            $objconfiguracion->setVar('serie_factura', $_POST['serie_factura']);
            $objconfiguracion->setVar('almacen_principal', $_POST['almacen_principal']);
            $objconfiguracion->setVar('estado_fila', $_POST['estado_fila']);
            $objconfiguracion->setVar('ruta', $_POST['ruta']);
            $objconfiguracion->setVar('token', $_POST['token']);
            $objconfiguracion->setVar('id_detraccion', $_POST['id_detraccion']);

            echo json_encode($objconfiguracion->insertDB());
            break;

        case 'mod':
            $objconfiguracion->setVar('id', $_POST['id']);
            $objconfiguracion->setVar('fecha_cierre', $_POST['fecha_cierre']);
            $objconfiguracion->setVar('nombre_negocio', $_POST['nombre_negocio']);
            $objconfiguracion->setVar('ruc', $_POST['ruc']);
            $objconfiguracion->setVar('direccion', $_POST['direccion']);
            $objconfiguracion->setVar('tipo_negocio', $_POST['tipo_negocio']);
            $objconfiguracion->setVar('telefono', $_POST['telefono']);
            $objconfiguracion->setVar('pagina_web', $_POST['pagina_web']);
            $objconfiguracion->setVar('razon_social', $_POST['razon_social']);
            $objconfiguracion->setVar('moneda', $_POST['moneda']);
            $objconfiguracion->setVar('serie_boleta', $_POST['serie_boleta']);
            $objconfiguracion->setVar('serie_factura', $_POST['serie_factura']);
            $objconfiguracion->setVar('almacen_principal', $_POST['almacen_principal']);
            $objconfiguracion->setVar('estado_fila', $_POST['estado_fila']);
            $objconfiguracion->setVar('ruta', $_POST['ruta']);
            $objconfiguracion->setVar('token', $_POST['token']);
            $objconfiguracion->setVar('correoEmisor', $_POST['correoEmisor']);

            $objconfiguracion->setVar('url_os_ticket', $_POST['url_os_ticket']);
            $objconfiguracion->setVar('key_os_ticket', $_POST['key_os_ticket']);
            $objconfiguracion->setVar('ip_publica_cliente_os_ticket', $_POST['ip_publica_cliente_os_ticket']);

            $objconfiguracion->setVar('logo_ticket', $_POST['logo_ticket']);
            $objconfiguracion->setVar('logo_boleta', $_POST['logo_boleta']);
            $objconfiguracion->setVar('logo_factura', $_POST['logo_factura']);

            $objconfiguracion->setVar('id_detraccion', $_POST['id_detraccion']);

            echo json_encode($objconfiguracion->updateDB());
            break;

        case 'del':
            $objconfiguracion->setVar('id', $_POST['id']);
            echo json_encode($objconfiguracion->deleteDB());
            break;

        case 'get':
            $res = $objconfiguracion->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objconfiguracion->listDB();
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;

        case 'search':
            $res = $objconfiguracion->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
        break;
         case 'img':
            $exito = 0;
            $key = $_FILES["img"];
            $tipo = 0;
            $ruta = "../recursos/img/";
            $ruta2 = "../api/classes/";
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
                if (file_exists($ruta ."logo.png")) {
                    unlink($ruta  . "logo.png");
                }
                if (file_exists($ruta2 ."logo.png")) {
                    unlink($ruta2  . "logo.png");
                }
                $exito = 1;
                $nombre_archivo = "logo";
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
                $max_ancho = 400;
                $max_alto = 160;

                //Se calcula ancho y alto de la imagen final
                $x_ratio = $max_ancho / $ancho;
                $y_ratio = $max_alto / $alto;

                //Si el ancho y el alto de la imagen no superan los maximos,
                //ancho final y alto final son los que tiene actualmente
                if (($ancho <= $max_ancho) && ($alto <= $max_alto)) {//Si ancho
                    $ancho_final = $ancho;
                    $alto_final = $alto;
                }
                /*
                 * si proporcion horizontal*alto mayor que el alto maximo,
                 * alto final es alto por la proporcion horizontal
                 * es decir, le quitamos al alto, la misma proporcion que
                 * le quitamos al alto
                 *
                 */ elseif (($x_ratio * $alto) < $max_alto) {
                    $alto_final = ceil($x_ratio * $alto);
                    $ancho_final = $max_ancho;
                }
                /*
                 * Igual que antes pero a la inversa
                 */ else {
                    $ancho_final = ceil($y_ratio * $ancho);
                    $alto_final = $max_alto;
                }

               //Creamos una imagen en blanco de tama??o $ancho_final  por $alto_final .
                $tmp = imagecreatetruecolor($ancho_final, $alto_final);

                //Copiamos $img_original sobre la imagen que acabamos de crear en blanco ($tmp)
                imagecopyresampled($tmp, $img_original, 0, 0, 0, 0, $ancho_final, $alto_final, $ancho, $alto);

                //Se destruye variable $img_original para liberar memoria
                imagedestroy($img_original);

                //Se crea la imagen final en el directorio indicado
                $ruta = $ruta . 'logo.png';
                $ruta2 = $ruta2 . 'logo.png';
                imagepng($tmp, $ruta);
                imagepng($tmp, $ruta2);
            }
            echo json_encode($exito);
            break;

    }
}?>