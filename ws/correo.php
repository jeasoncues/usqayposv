<?php
require_once 'nucleo/include/MasterConexion.php';
require("PHPMailer/src/PHPMailer.php");
require("PHPMailer/src/SMTP.php");
require("PHPMailer/src/Exception.php");
$objcon = new MasterConexion();


envioCorreo();
try {
    /* $existetable = $objcon->consulta_arreglo("select count(*) FROM information_schema.TABLES WHERE TABLE_NAME = 'check_system'");

    if (!empty($existetable)) {
        $existetable = $existetable[0];
        if ($existetable == 0) { // NO EXISTE LA TABLA CHECK_SYSTEM
            if (crearTablaCheckSystem()) {
                return var_dump(evaluaExisteFilaTableCheckSystem());
            }
        } else { // EVALUAMOS SI HAY UNA INICIALIZACION DE LA TABLA COMO PROTCOLO DEL SYSTEMA
            return var_dump(evaluaExisteFilaTableCheckSystem());
        }
    } */
    //envioCorreo();
} catch (Exception $e) {
    return var_dump("GENERAL");
    //error_log($e);
}

function crearTablaCheckSystem()
{
    require_once 'nucleo/include/MasterConexion.php';
    $objcon1 = new MasterConexion();

    $sqlTableCheckSystem = "create table check_system( id int not null primary key auto_increment,
    fecha_inicializacion datetime not null, fecha_envio datetime  null, fecha_proxima datetime  null);";

    $exitoCreacionTaBLE = $objcon1->consulta_simple($sqlTableCheckSystem);
    if ($exitoCreacionTaBLE) {
        return 1;
    } else {
        return 0;
    }
}

function evaluaExisteFilaTableCheckSystem()
{
    require_once 'nucleo/include/MasterConexion.php';
    $objcon2 = new MasterConexion();
    $arregloprueba = $objcon2->consulta_arreglo("SELECT * FROM check_system LIMIT 1");
    if (empty($arregloprueba)) {
        if (InsertamoFirstFilaTableCheckSystem()) {
           return  evaluaExisteCampoSeriePosEnTableConfiguraion();
        }
    } else {
        return evaluaExisteCampoSeriePosEnTableConfiguraion();
    }
}

function InsertamoFirstFilaTableCheckSystem()
{
    require_once 'nucleo/include/MasterConexion.php';
    $objcon3 = new MasterConexion();
    $sqlInsertFila = "insert into check_system value (null,now(),null,null)";
    $exitosoRegistroFilaCheckSystem = $objcon3->consulta_simple($sqlInsertFila);
    if ($exitosoRegistroFilaCheckSystem) {
        // echo ("creacionnnnnnnnnnnnnnnnnnnnnnnnnn de la fila de lta TABLA CHECK SUM EXITOSOA");
        return 1;
    } else {
        //  echo ("creacionnnnnnnnnnnnnnnnnnnnnnnnnn de la fila de lta TABLA CHECK SUM noooooooooooo EXITOSOA");
        return 0;
    }
}

function evaluaExisteCampoSeriePosEnTableConfiguraion()
{
    require_once 'nucleo/include/MasterConexion.php';
    $objcon4 = new MasterConexion();
    $existeCampoSeriePos = $objcon4->consulta_arreglo("SELECT *  FROM configuracion limit 1");
    if (!empty($existeCampoSeriePos)) {
        $existeCampoSeriePos = $existeCampoSeriePos[0];
        if (!empty($existeCampoSeriePos["serie_pos"])) {
            return evaluaRelacionEntreCampoSeriePosyTableCheckSystem();
        } else { // NO EXISTE EL CAMPO SERIE POR ESO  PROCEDEMOS A CREARLO
            if (CrearCampoSeriePosEnTableConfiguracion()) {
                return evaluaRelacionEntreCampoSeriePosyTableCheckSystem();
            }
        }
    }
}


function evaluaExisteCampoSeriePosEnTableConfiguraionAux()
{
    require_once 'nucleo/include/MasterConexion.php';
    $objcon44 = new MasterConexion();
    $existeCampoSeriePos = $objcon44->consulta_arreglo("SELECT *  FROM configuracion limit 1");
    if (!empty($existeCampoSeriePos)) {
        $existeCampoSeriePos = $existeCampoSeriePos[0];
        if (!empty($existeCampoSeriePos["serie_pos"])) {
            //evaluaRelacionEntreCampoSeriePosyTableCheckSystem();
            return 1;
        } else { // NO EXISTE EL CAMPO SERIE POR ESO  PROCEDEMOS A CREARLO
            if (CrearCampoSeriePosEnTableConfiguracion()) {
                return 1;
            } else {
                return -1;
            }
        }
    }
    return 0;
}

function CrearCampoSeriePosEnTableConfiguracion()
{
    require_once 'nucleo/include/MasterConexion.php';
    $objcon5 = new MasterConexion();
    $sqlCampoCOnfiguracion = "ALTER TABLE `configuracion` ADD COLUMN `serie_pos` VARCHAR(45) NULL AFTER `token`;";
    $exitoCreacionCampoSeriePos = $objcon5->consulta_simple($sqlCampoCOnfiguracion);
    if ($exitoCreacionCampoSeriePos) {
        //echo ("creacionn exitosaaaaaaaaaaaaaaaaaaaaaaaaa de campo pos");
        //REGISTRAMOS LA PRIMERA INICIAÑLIZACION DEL SISTEMA
        return 1;
    } else {
        // ENVIAMOS QUE NO SEP UDO CREAR LOS CAMPOS PERO OGIUAL E ENVIA LA INFORMACION 
        //echo ("creacionn exitosaaaaaaaaaaaaaaaaaaaaaaaaa NOOOOOOOOOOOOO se creo el campo serie_poss");
        return 0;
    }
}

function evaluaRelacionEntreCampoSeriePosyTableCheckSystem()
{
    //  echo ("evaluyarrrrr si el cmapo serie pos tiene relacion con la tabla checksystem ");
    require_once 'nucleo/include/MasterConexion.php';
    $objcon6 = new MasterConexion();
    $fechaInicializacion = $objcon6->consulta_arreglo("SELECT DATE(fecha_inicializacion) AS fi  FROM check_system limit 1");
    $seriePos = $objcon6->consulta_arreglo("SELECT serie_pos  FROM configuracion limit 1");
    if (!empty($fechaInicializacion)) {
        $fechaInicializacionSola = $fechaInicializacion["fi"];
        $seriPosSola = $seriePos["serie_pos"];
        if (strlen($seriPosSola) == 0) {
            $fechaInicializacionSolaAux = str_replace('-', '', $fechaInicializacionSola);
            $sqlupdateSeriePos = "UPDATE configuracion SET serie_pos ='" . $fechaInicializacionSolaAux . "' WHERE (id = '1');";
            $exitoConsulta = $objcon6->consulta_simple($sqlupdateSeriePos);
            if ($exitoConsulta) {
                //$sqlUpdateConsultaCheckSystem="";
                //$exitoConsulta = $objcon6->consulta_simple($sqlupdateSeriePos);
                //ENVIANDO CORREO
                return envioCorreo();
            }
        } else {
            // 
            return envioCorreo();
        }
    } else {
        evaluaExisteFilaTableCheckSystem();
    }

    // return "evaluyarrrrr si el cmapo serie pos tiene relacion con la tabla checksystem ";
}



function envioCorreo()
{
    
    require_once 'nucleo/include/MasterConexion.php';
    
    $objco = new MasterConexion();

    // try {
        $dataprueba = $objco->consulta_arreglo("SELECT * FROM configuracion limit 1");
        $dataAsuntoEmpresa=$dataprueba["razon_social"]."-".$dataprueba["ruc"];
        $dataprueba = mapped_implode(', ', $dataprueba, ' es ');
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->SMTPKeepAlive = true;   
        //$mail->Mailer = "smtp";
        $subject = "INCIO DE POS EN MAQUINA ".$dataAsuntoEmpresa;        
        $bodyEmail = "PRUEBA DE CORREO CTMRRR";
        $fromemail = "cotospos@gmail.com";
        $fromname = "COTOS POS";
        $host = "smtp.gmail.com";
        $port = "587";
        $SMTPAuth = "login";
        $passwordd = "Covid_2020";
        $_SMTPSecure = "tls";
        $emailTo = "cotospos@gmail.com";
        $mail->SMTPDebug =0;
        //$mail->isSMTP();
        $mail->IsSMTP();
        $mail->Host = $host;
        $mail->Port = $port;
        $mail->SMTPAuth = $SMTPAuth;
        $mail->SMTPSecure = $_SMTPSecure;
        $mail->Username = $fromemail;
        $mail->Password = $passwordd;
        $mail->setFrom($fromemail, $fromname);
        $mail->addAddress($emailTo, "ANTOY COTOS YOVERA");
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $dataprueba;
        $mail->send();
        if (!$mail->send()) {                  
            echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            error_log("enviadoooo bienn");
        }
    // } catch (Exception $e) {        
    //     //error_log($e);
    //      var_dump($e);
    // }
}

function mapped_implode($glue, $array, $symbol = '=') {
    return implode($glue, array_map(
            function($k, $v) use($symbol) {
                /* if(!empty($v)){
                    return $k . $symbol . 'sin valor';
                }else{
                    return $k . $symbol . $v;
                }                 */
                return $k . $symbol . $v;
            },
            array_keys($array),
            array_values($array)
            )
        );
}
?>