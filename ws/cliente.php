<?php

require_once('../nucleo/cliente.php');
require_once('Facturacion.php');

require '../vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$objcliente = new cliente();

if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'add':

            if($_POST['documento'] == "-"){
                $objcliente->setVar('nombre', $_POST['nombre']);
                $objcliente->setVar('documento', random_int(1, 9999));
                $objcliente->setVar('direccion', $_POST['direccion']);
                $objcliente->setVar('correo', $_POST['correo']);
                $objcliente->setVar('tipo_cliente', $_POST['tipo_cliente']);
                $objcliente->setVar('fecha_nacimiento', $_POST['fecha_nacimiento']);
                $objcliente->setVar('correo', $_POST['correo']);
                $objcliente->setVar('estado_fila', "1");
                echo json_encode($objcliente->insertDB());
            }else{

                $customer = $objcliente->searchDB($_POST['documento'],'documento');
                if(!($customer)){

                    $objcliente->setVar('nombre', $_POST['nombre']);
                    $objcliente->setVar('documento', $_POST['documento']);
                    $objcliente->setVar('direccion', $_POST['direccion']);
                    $objcliente->setVar('correo', $_POST['correo']);
                    $objcliente->setVar('tipo_cliente', $_POST['tipo_cliente']);
                    $objcliente->setVar('fecha_nacimiento', $_POST['fecha_nacimiento']);
                    $objcliente->setVar('correo', $_POST['correo']);
                    $objcliente->setVar('estado_fila', "1");
                    echo json_encode($objcliente->insertDB());

                }
            }

            break;

        case 'mod':
            $objcliente->setVar('id', $_POST['id']);
            $objcliente->setVar('nombre', $_POST['nombre']);
            $objcliente->setVar('documento', $_POST['documento']);
            $objcliente->setVar('direccion', $_POST['direccion']);
            $objcliente->setVar('correo', $_POST['correo']);
            $objcliente->setVar('tipo_cliente', $_POST['tipo_cliente']);
            $objcliente->setVar('fecha_nacimiento', $_POST['fecha_nacimiento']);
            $objcliente->setVar('correo', $_POST['correo']);
            echo json_encode($objcliente->updateDB());
            break;

        case 'del':
            $objcliente->setVar('id', $_POST['id']);
            echo json_encode($objcliente->deleteDB());
            break;

        case 'get':
            $res = $objcliente->searchDB($_POST['id'], 'id', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'getdocumento':
            $res = $objcliente->consulta_matriz("SELECT * FROM cliente WHERE documento = ".$_POST['doc']);
            // $res = $objcliente->searchDB($_POST['doc'], 'documento', 1);
            if (is_array($res)) {
                echo json_encode($res[0]);
            } else {
                echo json_encode(0);
            }
            break;

        case 'list':
            $res = $objcliente->listDB();
            if (is_array($res)) {
                echo json_encode($res);
            } else {
                echo json_encode(0);
            }
            break;
        
        case 'server_side':
            $result = $objcliente->ServerSide($_POST);
            echo json_encode($result);
        break;
        
        case 'search':
            $res = $objcliente->searchDB($_POST['data'], $_POST['value'], $_POST['type']);
            if (is_array($res)) {
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
            $ruta = "../recursos/uploads/clientes/";
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
                if (file_exists($ruta . $id_usuario . ".png")) {
                    unlink($ruta . $id_usuario . ".png");
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

                //Creamos una imagen en blanco de tamaño $ancho_final  por $alto_final .
                $tmp = imagecreatetruecolor($ancho_final, $alto_final);

                //Copiamos $img_original sobre la imagen que acabamos de crear en blanco ($tmp)
                imagecopyresampled($tmp, $img_original, 0, 0, 0, 0, $ancho_final, $alto_final, $ancho, $alto);

                //Se destruye variable $img_original para liberar memoria
                imagedestroy($img_original);

                //Se crea la imagen final en el directorio indicado
                $ruta = $ruta . $id_usuario . '.png';
                imagepng($tmp, $ruta);
            }
            echo json_encode($exito);
            break;


        case 'delFile':
            $response = $objcliente->consulta_simple('DELETE FROM archivos WHERE id = '.$_POST['id']);
            echo $response;
            break;

        case 'AddCredito':

            $getConsumo = $objcliente->consulta_arreglo("
                SELECT * FROM cliente_credito WHERE IdCliente = ".$_POST['idcliente']." AND Estado = 1");

            if(isset($getConsumo["id"])){
                if($getConsumo["Monto"] === $getConsumo["Consumo"]){
                    $response = $objcliente->consulta_simple("
                        INSERT INTO cliente_credito(Id, IdCliente, Monto, FechaLimite, Consumo, Estado,EnvioCorreo) 
                        VALUES ('',".$_POST['idcliente'].", ".$_POST['monto'].", '".$_POST['fecha_pago']."','0','1','0')");
                    echo $response;
                }else{
                    echo 2;
                }
            }else{
                $response = $objcliente->consulta_simple("
                INSERT INTO cliente_credito(Id, IdCliente, Monto, FechaLimite, Consumo, Estado,EnvioCorreo) 
                VALUES ('',".$_POST['idcliente'].", ".$_POST['monto'].", '".$_POST['fecha_pago']."','0','1','0')");
                echo $response;
            }
            
            break;

        case 'searchCredLimit':
            $response = $objcliente->consulta_matriz("
                SELECT * FROM cliente_credito WHERE IdCliente = ".$_POST['idcliente']." AND Estado = 1");
            echo json_encode($response);
            break;

        case 'insertPayCredit':

            $getConsumo = $objcliente->consulta_arreglo("
                SELECT * FROM cliente_credito WHERE IdCliente = ".$_POST['idcliente']." AND Estado = 1");
            $consumo = $getConsumo["Consumo"] + $_POST["monto"];
            // echo $consumo;
            $hoy = new DateTime(date("Y-m-d"));
            $fecha_pago = new DateTime($getConsumo["FechaLimite"]);
            $diff = date_diff($hoy, $fecha_pago);
            $d = intval($diff->format('%R%a') );
            
            if($d >= 0){
                if($getConsumo["Monto"] > $consumo ){
                    $response = $objcliente->consulta_simple("
                        UPDATE  cliente_credito  SET Consumo = ".$consumo." WHERE IdCliente = ".$_POST['idcliente']." AND Estado = 1");
                    echo $response;
                }else{
                    echo 3;
                }
            }else{
                echo 2;
            }
            
            break;

        case 'CancelPayCredit':

            $getConsumo = $objcliente->consulta_arreglo("
                SELECT * FROM cliente_credito WHERE IdCliente = ".$_POST['idcliente']." AND Estado = 1");
            $consumo = $getConsumo["Consumo"] - $_POST["monto"];
            // echo $consumo;
            $hoy = new DateTime(date("Y-m-d"));
            $fecha_pago = new DateTime($getConsumo["FechaLimite"]);
            $diff = date_diff($hoy, $fecha_pago);
            $d = intval($diff->format('%R%a') );
            
            if($d >= 0){
                if($getConsumo["Monto"] > $consumo ){
                    $response = $objcliente->consulta_simple("
                        UPDATE  cliente_credito  SET Consumo = ".$consumo." WHERE IdCliente = ".$_POST['idcliente']." AND Estado = 1");
                    echo $response;
                }else{
                    echo 3;
                }
            }else{
                echo 2;
            }
            
            break;


        case 'CloseCredit':
            $response = $objcliente->consulta_simple("
                UPDATE  cliente_credito  SET Estado = 2 WHERE Id = ".$_POST['id']);
            echo $response;
            break;

        case 'EditCredit':
            $response = $objcliente->consulta_arreglo("
                SELECT * FROM cliente_credito WHERE Id = ".$_POST['id']);
            echo json_encode($response);
            break;

        case 'ModCredito':
        
            $response = $objcliente->consulta_simple("
                UPDATE  cliente_credito SET Monto = ".$_POST['monto'].", FechaLimite = '".$_POST['fecha_pago']."'  WHERE Id = ".$_POST['id']);
            echo $response;
            break;

        case 'EnvioCorreo':
            $all = $objcliente->consulta_matriz("
                SELECT cre.id, IdCliente, nombre, correo, Monto, FechaLimite, Consumo, Estado, EnvioCorreo
                FROM cliente_credito cre
                INNER JOIN cliente cli ON cre.IdCliente = cli.id
                WHERE Estado = 1 AND EnvioCorreo = 0;");
            // echo json_encode($all);
            if(is_array($all)):

                $hoy = new DateTime(date("Y-m-d"));
                $items = array();

                foreach ($all as $a):
                    $data = array();
                    $fecha_pago = new DateTime($a["FechaLimite"]);
                    $diff = date_diff($hoy, $fecha_pago);
                    $d = intval($diff->format('%R%a') );

                    if($d >= 0 && $d <= 3):
                        $data["id"] = $a["id"];
                        if(!$a["correo"]):
                            $data["correo"] = "No Tiene Correo Agregado";
                        else:
                            $data["correo"] = $a["correo"];
                        endif;
                        // echo $a["IdCliente"];
                        $data["IdCliente"] = $a["IdCliente"];
                        $data["nombre"] = $a["nombre"];
                        $data["Monto"] = $a["Monto"];
                        $data["FechaLimite"] = $a["FechaLimite"];
                        $data["Consumo"] = $a["Consumo"];

                        $items[] = $data;

                    endif;
                    
                endforeach;
            endif;

            header('Content-type: application/json; charset=utf-8');
            echo json_encode($items);

            break;

        case 'changeStatusSend':

            $data = $_POST["data"];
            // echo count($data);

            for($i = 0 ; $i<count($data); $i++):
                $objcliente->consulta_simple("
                UPDATE  cliente_credito SET EnvioCorreo = 1  WHERE Id = ".$data[$i]['id']);
            endfor;
        
            
            echo 1;
            break;

        case 'addTrabajador':

            // $sueldo = number_format($_POST['sueldo'],2,"",".");
            $response = $objcliente->consulta_simple('
                INSERT INTO trabajador(id, nombres_y_apellidos, tipo_documento, documento, sueldo_basico, condicion, situacion, fecha_de_ingreso, quinta_categoria, asignacion_familiar, regimen_pensionario, cuspp, estado_fila, fecha_cese, ocupacion, contrato, tipo_flujo) 
                VALUES (NULL,"'.$_POST['nombre'].'",'.$_POST['tipo_documento'].','.$_POST['documento'].','.$_POST['sueldo'].','.$_POST['condicion'].','.$_POST['situacion'].',"'.$_POST['fecha_ingreso'].'",'.$_POST['quinta_categoria'].','.$_POST['asignacion_familiar'].','.$_POST['regimen_pensionario'].',"'.$_POST['cuspp'].'",1,"'.$_POST['fecha_cese'].'","'.$_POST['ocupacion'].'",'.$_POST['contrato'].','.$_POST['tipo_flujo'].')');
            echo $response;
        break;

        case 'gettrabajador':
            $response = $objcliente->consulta_arreglo('SELECT * FROM trabajador WHERE id = '.$_POST['id']);
            echo json_encode($response);
        break;

        case 'upTrabajador':
            $response = $objcliente->consulta_simple('
                UPDATE trabajador SET nombres_y_apellidos= "'.$_POST['nombre'].'" ,tipo_documento= '.$_POST['tipo_documento'].',documento= '.$_POST['documento'].',sueldo_basico= '.$_POST['sueldo'].',condicion= '.$_POST['condicion'].',situacion= '.$_POST['situacion'].',fecha_de_ingreso= "'.$_POST['fecha_ingreso'].'",quinta_categoria= '.$_POST['quinta_categoria'].',asignacion_familiar= '.$_POST['asignacion_familiar'].',regimen_pensionario= '.$_POST['regimen_pensionario'].',cuspp= "'.$_POST['cuspp'].'", fecha_cese = "'.$_POST['fecha_cese'].'", ocupacion = "'.$_POST['ocupacion'].'", contrato = '.$_POST['contrato'].', tipo_flujo = '.$_POST['tipo_flujo'].' WHERE id = '.$_POST['id'].'');
            echo $response;
        break;


        case 'addConceptoIng':
            $response = $objcliente->consulta_simple('
                INSERT INTO conceptos_ingresos(codigo, descripcion, tipo, monto, estado, afecto, essalud)  
                VALUES ("'.$_POST['codigo'].'","'.$_POST['descripcion'].'",'.$_POST['tipo'].','.$_POST['monto'].',1,'.$_POST['afecto'].','.$_POST['Essalud'].')'
            );
            echo $response;
        break;

        case 'getconceptoing':
            $response = $objcliente->consulta_arreglo('SELECT * FROM conceptos_ingresos WHERE codigo = '.$_POST['codigo']);
            echo json_encode($response);
        break;

        case 'upConceptoIng':

            $response = $objcliente->consulta_simple('
                UPDATE conceptos_ingresos SET codigo= "'.$_POST['codigo'].'" ,descripcion= "'.$_POST['descripcion'].'",tipo= '.$_POST['tipo'].',monto= '.$_POST['monto'].',afecto= '.$_POST['afecto'].',essalud= '.$_POST['Essalud'].' WHERE codigo = '.$_POST['codigo'].'');
            echo $response;
        break;

        case 'addConceptoDes':
            $response = $objcliente->consulta_simple('
                INSERT INTO conceptos_descuentos(codigo, descripcion, tipo, monto, estado, afecto, essalud)  
                VALUES ("'.$_POST['codigo'].'","'.$_POST['descripcion'].'",'.$_POST['tipo'].','.$_POST['monto'].',1,'.$_POST['afecto'].','.$_POST['Essalud'].')'
            );
            echo $response;
        break;

        case 'getconceptoDes':
            $response = $objcliente->consulta_arreglo('SELECT * FROM conceptos_descuentos WHERE codigo = '.$_POST['codigo']);
            echo json_encode($response);
        break;

        case 'upConceptoDes':
            $response = $objcliente->consulta_simple('
                UPDATE conceptos_descuentos SET codigo= "'.$_POST['codigo'].'" ,descripcion= "'.$_POST['descripcion'].'",tipo= '.$_POST['tipo'].',monto= '.$_POST['monto'].',afecto= '.$_POST['afecto'].',essalud= '.$_POST['Essalud'].' WHERE codigo = '.$_POST['codigo'].'');
            echo $response;
        break;

        case 'addConceptoapo':
            $response = $objcliente->consulta_simple('
                INSERT INTO conceptos_aportes(codigo, descripcion, tipo, monto, estado)  
                VALUES ("'.$_POST['codigo'].'","'.$_POST['descripcion'].'",'.$_POST['tipo'].','.$_POST['monto'].',1)'
            );
            echo $response;
        break;

        case 'getconceptoapo':
            $response = $objcliente->consulta_arreglo('SELECT * FROM conceptos_aportes WHERE codigo = '.$_POST['codigo']);
            echo json_encode($response);
        break;

        case 'upConceptoapo':
            $response = $objcliente->consulta_simple('
                UPDATE conceptos_aportes SET codigo= "'.$_POST['codigo'].'" ,descripcion= "'.$_POST['descripcion'].'",tipo= '.$_POST['tipo'].',monto= '.$_POST['monto'].' WHERE codigo = '.$_POST['codigo'].'');
            echo $response;
        break;

        case 'addConceptoemp':
            $response = $objcliente->consulta_simple('
                INSERT INTO conceptos_aportes_empleador(codigo, descripcion, tipo, monto, estado)  
                VALUES ("'.$_POST['codigo'].'","'.$_POST['descripcion'].'",'.$_POST['tipo'].','.$_POST['monto'].',1)'
            );
            echo $response;
        break;

        case 'getconceptoemp':
            $response = $objcliente->consulta_arreglo('SELECT * FROM conceptos_aportes_empleador WHERE codigo = '.$_POST['codigo']);
            echo json_encode($response);
        break;

        case 'upConceptoemp':
            $response = $objcliente->consulta_simple('
                UPDATE conceptos_aportes_empleador SET codigo= "'.$_POST['codigo'].'" ,descripcion= "'.$_POST['descripcion'].'",tipo= '.$_POST['tipo'].',monto= '.$_POST['monto'].' WHERE codigo = '.$_POST['codigo'].'');
            echo $response;
        break;

        case 'addConceptosus':
            $response = $objcliente->consulta_simple('
                INSERT INTO conceptos_suspension_labores(codigo, descripcion, estado)  
                VALUES ("'.$_POST['codigo'].'","'.$_POST['descripcion'].'",1)'
            );
            echo $response;
        break;

        case 'getconceptosus':
            $response = $objcliente->consulta_arreglo('SELECT * FROM conceptos_suspension_labores WHERE codigo = '.$_POST['codigo']);
            echo json_encode($response);
        break;

        case 'upConceptosus':
            $response = $objcliente->consulta_simple('
                UPDATE conceptos_suspension_labores SET codigo= "'.$_POST['codigo'].'" ,descripcion= "'.$_POST['descripcion'].'" WHERE codigo = '.$_POST['codigo'].'');
            echo $response;
        break;

        case 'addRegimenPensionario':
            $response = $objcliente->consulta_simple('
                INSERT INTO regimen_pensionario(id, nombre, comision_porcentual, prima_seguro, aportacion_obligatoria, estado, comision_porcentual_sf) 
                VALUES ("'.$_POST['codigo'].'","'.$_POST['descripcion'].'",'.$_POST['comisionporcentual'].','.$_POST['primaseguro'].','.$_POST['aportacionobl'].',1,'.$_POST['comi_sf'].')'
            );
            echo $response;
        break;

        case 'getRegimenPensionario':
            $response = $objcliente->consulta_arreglo('SELECT * FROM regimen_pensionario WHERE id = '.$_POST['codigo']);
            echo json_encode($response);
        break;

        case 'upRegimenPensionario':
            $response = $objcliente->consulta_simple('
                UPDATE regimen_pensionario SET id= "'.$_POST['codigo'].'" ,nombre= "'.$_POST['descripcion'].'",comision_porcentual= '.$_POST['comisionporcentual'].' , prima_seguro= '.$_POST['primaseguro'].' , aportacion_obligatoria= '.$_POST['aportacionobl'].', comision_porcentual_sf= '.$_POST['comi_sf'].' WHERE id = '.$_POST['codigo'].'');
            echo $response;
        break;

        case 'getConceptos':

            if($_POST['id'] == 1){
                $response = $objcliente->consulta_matriz('SELECT *  FROM conceptos_aportes');
            }elseif ($_POST['id'] == 2) {
                $response = $objcliente->consulta_matriz('SELECT *  FROM conceptos_aportes_empleador');
            }elseif ($_POST['id'] == 3) {
                $response = $objcliente->consulta_matriz('SELECT *  FROM conceptos_descuentos');
            }elseif ($_POST['id'] == 4) {
                $response = $objcliente->consulta_matriz('SELECT *  FROM conceptos_ingresos');
            }else{
                $response = $objcliente->consulta_matriz('SELECT *  FROM conceptos_suspension_labores');
            }
            echo json_encode($response);
        break;

        case 'getConceptos2':

            if($_POST['id'] == 1){
                $response = $objcliente->consulta_arreglo('SELECT *  FROM conceptos_aportes WHERE codigo = '.$_POST['id2']);
            }elseif ($_POST['id'] == 2) {
                $response = $objcliente->consulta_arreglo('SELECT *  FROM conceptos_aportes_empleador WHERE codigo = '.$_POST['id2']);
            }elseif ($_POST['id'] == 3) {
                $response = $objcliente->consulta_arreglo('SELECT *  FROM conceptos_descuentos WHERE codigo = '.$_POST['id2']);
            }elseif ($_POST['id'] == 4) {
                $response = $objcliente->consulta_arreglo('SELECT *  FROM conceptos_ingresos WHERE codigo = '.$_POST['id2']);
            }else{
                $response = $objcliente->consulta_arreglo('SELECT *  FROM conceptos_suspension_labores WHERE codigo = '.$_POST['id2']);
            }
            echo json_encode($response);
        break;

        case 'boletadepago':
            $data = $_POST['data'];

            $tam = count($data);
            $utltimopago = $objcliente->consulta_arreglo('SELECT * FROM boleta_de_pago ORDER BY id DESC limit 1');
            $idboleta = $utltimopago["id"]+1;
            for ($i=0; $i < $tam; $i++) {
                if($data[$i]["id"] == '1'){

                    // if($data[$i]["codigo"] == '0605')
                    //     $data[$i]["descuento"] = $_POST["qc"];

                    $response = $objcliente->consulta_simple('
                    INSERT INTO boleta_aportes(id, id_boleta, codigo_concepto, monto)  
                        VALUES (NULL,'.$idboleta.',"'.$data[$i]["codigo"].'",'.$data[$i]["descuento"].')'
                    );
                }elseif($data[$i]["id"] == '2'){
                    $response = $objcliente->consulta_simple('
                    INSERT INTO boleta_aportes(id, id_boleta, codigo_concepto, monto)  
                        VALUES (NULL,'.$idboleta.',"'.$data[$i]["codigo"].'",'.$data[$i]["neto"].')'
                    );
                }elseif($data[$i]["id"] == '3'){
                    $response = $objcliente->consulta_simple('
                    INSERT INTO boleta_descuentos(id, id_boleta, codigo_concepto, monto)  
                        VALUES (NULL,'.$idboleta.',"'.$data[$i]["codigo"].'",'.$data[$i]["descuento"].')'
                    );
                }elseif($data[$i]["id"] == '4'){
                    $response = $objcliente->consulta_simple('
                    INSERT INTO boleta_ingresos(id, id_boleta, codigo_concepto, monto)  
                        VALUES (NULL,'.$idboleta.',"'.$data[$i]["codigo"].'",'.$data[$i]["ingreso"].')'
                    );
                }else{
                    //boletas suspenciones
                }
            }
            // echo $tam2;

            if(is_array($_POST['data2'])){
                $data2=$_POST['data2'];
                $tam2 = count($data2);

                if($tam2>0){
                    for ($i=0; $i < $tam2; $i++) {
                        $response = $objcliente->consulta_simple('
                        INSERT INTO boleta_suspensiones(id, id_boleta, codigo_concepto, dias)  
                            VALUES (NULL,'.$idboleta.',"'.$data2[$i]["codigo"].'",'.$data2[$i]["dias"].')'
                        );
                    }
                }
            }

            

            $response = $objcliente->consulta_simple('
                INSERT INTO boleta_de_pago(id, id_trabajador, fecha_generada, mes, ano, total_bruto, total_descuentos, total_aportes_trabajador, total_neto, total_aportes_empleador, dias_laborados, dias_no_laborados, dias_subsidiados, horas_ordinarias, minutos_ordinarios, horas_extra, minutos_extra, id_usuario) 
                VALUES (NULL,'.$_POST["idt"].', "'.date('Y-m-d H:i:s').'",'.$_POST["mes"].','.date("Y").','.$_POST["tbruto"].','.$_POST["td"].','.$_POST["tat"].','.$_POST["totalneto"].','.$_POST["tae"].','.$_POST["dl"].','.$_POST["dnl"].','.$_POST["ds"].','.$_POST["joth"].','.$_POST["jom"].','.$_POST["sth"].','.$_POST["sm"].','.$_POST["idu"].')');

            echo $idboleta;
        break;

        case 'delTrabajador':
            $response = $objcliente->consulta_simple('DELETE FROM trabajador WHERE id = '.$_POST['id']);
            $objcliente->consulta_simple('DELETE FROM boleta_de_pago WHERE id_trabajador = '.$_POST['id']);
            echo $response;
        break;

        case 'delConpIngreso':
            $response = $objcliente->consulta_simple('DELETE FROM conceptos_ingresos WHERE codigo = '.$_POST['id']);
            echo $response;
        break;

        case 'delConpApo':
            $response = $objcliente->consulta_simple('DELETE FROM conceptos_aportes WHERE codigo = '.$_POST['id']);
            echo $response;
        break;

        case 'delconceptoemp':
            $response = $objcliente->consulta_simple('DELETE FROM conceptos_aportes_empleador WHERE codigo = '.$_POST['id']);
            echo $response;
        break;

        case 'delconceptoDes':
            $response = $objcliente->consulta_simple('DELETE FROM conceptos_descuentos WHERE codigo = '.$_POST['id']);
            echo $response;
        break;

        case 'delConceptosus':
            $response = $objcliente->consulta_simple('DELETE FROM conceptos_suspension_labores WHERE codigo = '.$_POST['id']);
            echo $response;
        break;

        case 'delRegimenPensionario':
            $response = $objcliente->consulta_simple('DELETE FROM regimen_pensionario WHERE id = '.$_POST['id']);
            echo $response;
        break;

        case 'getAfectacion':
            $response = $objcliente->consulta_arreglo('SELECT * FROM afectacion WHERE codigo = "'.$_POST['id'].'"');
            echo json_encode($response);
        break;

        case 'addAfectacion':
            $data = $_POST['data'];
            if($data[0]['action'] > 0){
                $response = $objcliente->consulta_simple('UPDATE afectacion SET essalud_trabajador='.$data[0]['essalud_trabajador'].',essalud_pesquero='.$data[0]['essalud_pesquero'].',essalud_agricultor='.$data[0]['essalud_agricultor'].',essalud_sctr='.$data[0]['essalud_sctr'].',impuesto_solidaridad='.$data[0]['impuesto_solidaridad'].',fondos_artista='.$data[0]['fondos_artista'].',senati='.$data[0]['senati'].',snp_19990='.$data[0]['snp_19990'].',sp_pensiones='.$data[0]['sp_pensiones'].',quinta_categoria='.$data[0]['quinta_categoria'].',essalud_pensionista='.$data[0]['essalud_pensionista'].',contrib_solidaria='.$data[0]['contrib_solidaria'].' WHERE  codigo="'.$data[0]['codigo'].'"');
            }else{
                $response = $objcliente->consulta_simple('INSERT INTO afectacion(codigo, essalud_trabajador, essalud_pesquero, essalud_agricultor, essalud_sctr, impuesto_solidaridad, fondos_artista, senati, snp_19990, sp_pensiones, quinta_categoria, essalud_pensionista, contrib_solidaria) VALUES ("'.$data[0]['codigo'].'",'.$data[0]['essalud_trabajador'].','.$data[0]['essalud_pesquero'].','.$data[0]['essalud_agricultor'].','.$data[0]['essalud_sctr'].','.$data[0]['impuesto_solidaridad'].','.$data[0]['fondos_artista'].','.$data[0]['senati'].','.$data[0]['snp_19990'].','.$data[0]['sp_pensiones'].','.$data[0]['quinta_categoria'].','.$data[0]['essalud_pensionista'].','.$data[0]['contrib_solidaria'].')');
            }
            echo $response;
        break;

        case 'filtroxMes':
            $response = $objcliente->consulta_matriz('SELECT bp.id, t.id as id_trabajador, t.nombres_y_apellidos, fecha_generada, mes, ano, total_neto, u.nombres_y_apellidos AS usuario, total_bruto, total_descuentos, total_aportes_empleador, dias_laborados, dias_no_laborados, dias_subsidiados, horas_ordinarias, minutos_ordinarios, horas_extra, minutos_extra
                    FROM boleta_de_pago bp, trabajador t, usuario u
                    WHERE bp.id_trabajador = t.id AND u.id = id_usuario AND mes = '.$_POST['mes'].'');

            if (is_array($response)) {
                echo json_encode($response);
            } else {
                echo json_encode(0);
            }
        break;

        case 'getdocumentosunat':
            $ruc = $_POST['dni'];
    
            require_once("busruc/autoload.php");

            $cookie = array(
                'cookie'        => array(
                    'use'       => true,
                    'file'      => __DIR__ . "/cookie.txt"
                )
            );

            $config = array(
                'representantes_legales'    => false,
                'cantidad_trabajadores'     => false,
                'establecimientos'          => false,
                'cookie'                    => $cookie
            );
            
            $sunat = new \Sunat\ruc( $config );

            $search = $sunat->consulta($ruc); 

            //print_r($search);
            
            if(!$search->success==false)
            {
                $array[] = array(
                    "pk" => $search->result->ruc,
                    'document' => $search->result->ruc,
                    "companyName" => urldecode($search->result->razon_social),
                    "email" => '',
                    "address" => urldecode($search->result->direccion),
                );

                echo json_encode($array);
            }else{
                
                echo json_encode(0);
            }


        break;

        case 'getdocumentoreniec': 
            $dni = $_POST['dni'];
    
            require_once("busdni/autoload.php");
            $reniec = new \Reniec\Reniec();
            $search = $reniec->search($dni);
            //print_r($search);
            //echo json_encode(0);
            if(!$search->success==false){
                 $array[] = array(
                 "pk" => $search->result->DNI,
                 'nombres' => urldecode($search->result->Nombres)." ".urldecode($search->result->apellidos),
                 "direccion" => urldecode($search->result->Distrito),
                 "email" => '');

                 

                 echo json_encode($array);

            }else{
                echo json_encode(0);
            }

        break;

        case 'reportevetasxcliente':
            $fechaInicio = $_POST['fecha_inicio'];
            $fechaFin = $_POST['fecha_fin'];            
            $objs = $objcliente->consulta_matriz("SELECT count(v.id) as noperaciones ,sum(total) as total_vendido,id_cliente,c.nombre as nombrecliente FROM venta v left join cliente c on (v.id_cliente=c.id) where v.estado_fila = 1 and v.fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' group by id_cliente order by sum(total)desc");
            echo json_encode($objs);
        break;

        case 'pruebafactura':
            //echo json_decode(json_encode($_POST['medio_pago']));
            //$medio="[{'id_venta':'1', 'medio':'EFECTIVO','monto':'100','vuelto':'80','moneda':'PEN'} ]";
           // $facturacion = new Facturacion($_POST['cliente'],$_POST['id'],$_POST['tipo_documento'],$_POST['descuento_global'],$_POST['medio_pago']);
          
            $facturacion = new Facturacion($_POST['cliente'],$_POST['id'],$_POST['tipo_documento'],$_POST['descuento_global'],$_POST['medio_pago'],$_POST['tipo_guia'],$_POST['serie_numero']);
            echo json_encode($facturacion->testTypeDocument());
            //var_dump($facturacion->testTypeDocument());
        break;

    }
}?>