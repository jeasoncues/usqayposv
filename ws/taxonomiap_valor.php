<?php

require_once('../nucleo/taxonomiap_valor.php');
$objtaxonomiap_valor = new taxonomiap_valor();

require_once('../nucleo/taxonomiap.php');
$objtaxonomiap = new taxonomiap();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':            
            $objtaxonomiap_valor = new taxonomiap_valor();
            $objtaxonomiap_valor->setVar('id', $_POST['id']);
            $objtaxonomiap_valor->setVar('id_taxonomiap', $_POST['id_taxonomiap']);
            $objtaxonomiap_valor->setVar('valor', $_POST['valor']);
            $objtaxonomiap_valor->setVar('padre', $_POST['padre']);
            $objtaxonomiap_valor->setVar('estado_fila', $_POST['estado_fila']);
            $aidi = $objtaxonomiap_valor->insertDB();
             
            $objconn = new taxonomiap();
            $objconn->consulta_simple("Update taxonomiap set tipo_valor = 2 where id = '".$_POST["id_taxonomiap"]."'");
            
            echo json_encode($aidi);
            break;

        case 'mod':
            $objtaxonomiap_valor->setVar('id', $_POST['id']);
            $objtaxonomiap_valor->setVar('id_taxonomiap', $_POST['id_taxonomiap']);
            $objtaxonomiap_valor->setVar('valor', $_POST['valor']);
            $objtaxonomiap_valor->setVar('padre', $_POST['padre']);
            $objtaxonomiap_valor->setVar('estado_fila', $_POST['estado_fila']);

            echo json_encode($objtaxonomiap_valor->updateDB());
            break;

        case 'del':
            $objtaxonomiap_valor->setVar('id', $_POST['id']);
            echo json_encode($objtaxonomiap_valor->deleteDB());
            break;

        case 'get':
            $res = $objtaxonomiap_valor->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                $res[0]['id_taxonomiap'] = $objtaxonomiap->searchDB($res[0]['id_taxonomiap'], 'id', 1);
                $res[0]['id_taxonomiap'] = $res[0]['id_taxonomiap'][0];
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objtaxonomiap_valor->listDB();
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_taxonomiap'] = $objtaxonomiap->searchDB($act['id_taxonomiap'], 'id', 1);
                    $act['id_taxonomiap'] = $act['id_taxonomiap'][0];
                    
                    $act['padre'] = $objtaxonomiap_valor->searchDB($act['padre'], 'id', 1);
                    $act['padre'] = $act['padre'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'listpadre':
            $res = $objtaxonomiap_valor->consulta_matriz("Select * from taxonomiap_valor where id_taxonomiap = '".$_POST["id_padre"]."'");
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_taxonomiap'] = $objtaxonomiap->searchDB($act['id_taxonomiap'], 'id', 1);
                    $act['id_taxonomiap'] = $act['id_taxonomiap'][0];
                    
                    $act['padre'] = $objtaxonomiap_valor->searchDB($act['padre'], 'id', 1);
                    $act['padre'] = $act['padre'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'listbytp':
            $res = $objtaxonomiap_valor->consulta_matriz("Select * from taxonomiap_valor where id_taxonomiap = '".$_POST["id"]."'");
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_taxonomiap'] = $objtaxonomiap->searchDB($act['id_taxonomiap'], 'id', 1);
                    $act['id_taxonomiap'] = $act['id_taxonomiap'][0];
                }
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
        case 'listbypadre':
            //El father
            $searchPrev=$_POST["valor"]; 
            $codificacion=mb_detect_encoding($searchPrev,"ISO-8859-1,UTF-8");            
            $search = iconv($codificacion,'UTF-8',$searchPrev);
            // echo($search);
            $hector = $objtaxonomiap_valor->consulta_arreglo("Select * from taxonomiap_valor where valor = '".$search."'");            
            $res = $objtaxonomiap_valor->consulta_matriz("Select * from taxonomiap_valor where id_taxonomiap = '3'  and padre = '".$hector["id"]."' and estado_fila = 1");
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
            
            

        case 'search':
            $res = $objtaxonomiap_valor->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
                foreach ($res as &$act) {
                    $act['id_taxonomiap'] = $objtaxonomiap->searchDB($act['id_taxonomiap'], 'id', 1);
                    $act['id_taxonomiap'] = $act['id_taxonomiap'][0];
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
            $ruta = "../recursos/uploads/valores_atributos_productos/";
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
    }
}?>