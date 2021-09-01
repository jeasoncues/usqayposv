<?php

require '../vendor/autoload.php';
require_once '../nucleo/include/MasterConexion.php';
require_once('../nucleo/producto.php');

use Phelium\Component\MySQLBackup;
require_once '../vendor/PHPMailer_copi/PHPMailerAutoload.php';

$from_address="team@hello.sistemausqay.com";
$username="reportes@usqay-cloud.com";
$pass="pudmnscrozhxausq";

$mailDestino="inteligencia@usqay-cloud.com";


$objcon = new MasterConexion();
$obj = new producto();
$tablas = $obj->showTables();
$tbl = [];

foreach ($tablas as $key => $value) {
    foreach ($value as $val) {
        $tbl[] = $val;
    }
}
$dbName='pos';
$Dump = new MySQLBackup('localhost', 'root', '', $dbName);


if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'backup':
            $Dump->addTables($tbl);
            //$Dump->setCompress('zip');
           // $Dump->setDelete(true);
           // $Dump->setDownload(true);
            $Dump->dump();

            echo ("RESPALDO");
            break;
        case 'backup_mail':
            $conf= $objcon->consulta_arreglo("SELECT * FROM configuracion");

            $Dump->addTables($tbl);
            $Dump->setCompress('zip');
            $Dump->dump();
            $mail = new PHPMailer(true);
                $mail->IsSMTP();

                //Configuracion servidor mail
                $mail->From = $from_address; //remitente
                $mail->SMTPAuth = true;
                $mail->SMTPSecure = 'tls'; //seguridad
                $mail->Host = "smtp.gmail.com"; // servidor smtp
                $mail->Port = 587; //puerto
                $mail->Username =$username; //nombre usuario
                $mail->Password = $pass; //contraseña
                
                //Agregar destinatario
                $mail->AddAddress($mailDestino);//correo destino
                $mail->Subject = "Backup BD de ". $conf['nombre_negocio']." del ".date("Y-m-d");// asunto
               // $mail->AddEmbeddedImage('../recursos/img/logo.png','logo.png');
                $archivo ='../recursos/backups/dump_'.$dbName.'_'.date('Ymd-H\hi').'.zip';
                $mail->addAttachment($archivo,$archivo);
                $message="Backup Base de Datos del negocio ". $conf['nombre_negocio']." con fecha de  ".date("Y-m-d");
                

                $mail->Body = $message;
                $mail->AltBody  = $message;
            

                if ($mail->Send()) {
                    echo "msg exito";
                    $Dump->setDelete(true);
                } else {
                    echo "error";
                }


            break;
    }
}?>



