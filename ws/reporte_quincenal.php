<?php

require_once '../vendor/PHPMailer_copi/PHPMailerAutoload.php';
require_once '../nucleo/include/MasterConexion.php';
$objcon = new MasterConexion();

$config = $objcon->consulta_arreglo("SELECT * FROM configuracion");
$from_address="team@hello.sistemausqay.com";
$username="reportes@usqay-cloud.com";
$pass="qkghutdrsdakehqn";

$mailDestino=$config['correoEmisor'];
/*
$from_address="axalpusa1125@gmail.com";
$username="axalpusa1125@gmail.com";
$pass="******";

$mailDestino="puertas_94_13@hotmail.com";*/


setlocale(LC_TIME, "spanish");

$fecha_inicio="";
$fecha_fin="";

$fecha = date("Y-m-d");
            $dia=  date("d", strtotime($fecha)); 
            $mes = date("m", strtotime($fecha)); 
            $anio = date("Y", strtotime($fecha)); 


if ($dia == 16) {
   $fecha_inicio=$anio."-".$mes."-01";
    $fecha_fin=$anio."-".$mes."-15";
}
if ($dia == 1) {
   $fecha_inicio=$anio."-".($mes-1)."-16";
    $fecha_fin=$anio."-".($mes-1)."-31";
}


$totalvendido_alto = $objcon->consulta_arreglo("SELECT sum(v.total)as total,v.fecha_cierre 
                                                FROM venta v where v.total is not null and v.estado_fila IN (1,3,4)
                                                 and v.fecha_cierre between '" . $fecha_inicio . " 00:00:00'  AND '" . $fecha_fin . " 23:59:59' 
                                                GROUP by v.fecha_cierre 
                                                ORDER BY `total` desc limit 1");
$totalvendido_bajo = $objcon->consulta_arreglo("SELECT sum(v.total)as total,v.fecha_cierre 
                                                FROM venta v where v.total is not null and v.estado_fila IN (1,3,4) 
                                                 and v.fecha_cierre between '" . $fecha_inicio . " 00:00:00'  AND '" . $fecha_fin . " 23:59:59' 
                                                GROUP by v.fecha_cierre 
                                                ORDER BY `total` ASC limit 1");
$ventaTrabajadorTop = $objcon->consulta_arreglo("SELECT sum(v.total)as total,v.fecha_cierre, us.nombres_y_apellidos as trabajador 
                                            FROM venta v 
                                            inner join usuario us on v.id_usuario=us.id 
                                            where v.total is not null and v.estado_fila IN (1,3,4) 
                                            and v.fecha_cierre between '" . $fecha_inicio . " 00:00:00'  AND '" . $fecha_fin . " 23:59:59' 
                                            GROUP by us.nombres_y_apellidos 
                                            ORDER BY `total` DESC Limit 1");



$fecha_alta=$totalvendido_alto['fecha_cierre'];
$num_day = date("d", strtotime($fecha_alta)); 
$day = strftime("%A", strtotime($fecha_alta));

$fecha_bajo=$totalvendido_bajo['fecha_cierre'];
$num_day_b = date("d", strtotime($fecha_bajo)); 
$day_b = strftime("%A", strtotime($fecha_bajo)); 

$mes = strftime("%B");




if (isset($_POST['op'])) {
    switch ($_POST['op']) {
        case 'descanvas':
            $img = $_POST['imgBase64tv'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $fileData = base64_decode($img);
            $fileName = '../recursos/img/'.'total_vendido.png';
            file_put_contents($fileName, $fileData);
            
            $img = $_POST['imgBase64mv'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $fileData = base64_decode($img);
            $fileName = '../recursos/img/'.'menos_vendido.png';
            file_put_contents($fileName, $fileData);
            
            $img = $_POST['imgBase64msv'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $fileData = base64_decode($img);
            $fileName = '../recursos/img/'.'mas_vendido.png';
            file_put_contents($fileName, $fileData);
            
            $img = $_POST['imgBase64trab'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $fileData = base64_decode($img);
            $fileName = '../recursos/img/'.'trabajadores.png';
            file_put_contents($fileName, $fileData);
            
            echo "exito";
        break;
        case 'send':
                        //Create a new PHPMailer instance
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
                $mail->Subject = "Analizamos estos 15 dias para ti";// asunto,'total_vendido','file/imagen.jpg','base64','image/jpeg'
                $mail->AddEmbeddedImage('../recursos/img/total_vendido.png','total_vendido.png');
                $mail->AddEmbeddedImage('../recursos/img/mas_vendido.png','mas_vendido.png');
                $mail->AddEmbeddedImage('../recursos/img/menos_vendido.png','menos_vendido.png');
                $mail->AddEmbeddedImage('../recursos/img/trabajadores.png','trabajadores.png');
                $mail->AddEmbeddedImage('../recursos/img/logo_mail.png','logo_mail.png');
                $message="
                <body class=''>
                <style>
                        @charset 'UTF-8';
                        body {
                            font-family: 'Roboto', Arial, serif;
                            background: transparent;
                            border: 20px #00395e solid;
                            -webkit-font-smoothing: antialiased;
                        }

                        img {
                            max-width: 100%;
                            height: auto;		
                        }
                        .logo{
                            margin-top: 1.5em;
                        width: 50%;
                        
                        text-align: center;
                        }
                        .marg{
                            margin-left: 3em;
                            margin-right: 3em;
                        }
                        p{
                            text-align: justify;
                        }
                        
                        h1, h2, h3, h4, h5, h6 {
                        color: rgba(0, 0, 0, 0.8);
                        font-family: 'Roboto', Arial, serif;
                        font-weight: 300;
                        margin: 0 0 30px 0;
                        }
                    
                    </style>
                            <center>
                                <img class='logo'   src=\"cid:logo_mail.png\" /> 
                            </center>
                            <div class='marg' >
                                <br>
                                <p>
                                    Hola te saluda Vania, ya pasaron 15 dias del mes de ".$mes."  , nos tomamos el trabajo de analizar estos dias importantes para ti
                                </p>
                                
                                <p>
                                Sabias que tu dia mas alto en este tiempo fue el ". $day." ". $num_day." lograste vender S/".$totalvendido_alto['total']." 
                                </p>
                                <!--superando la venta del anterior sabado ( esta parte siempre y cuando tenga venta) 
                            
                                Si fue menor le decimos
                                ... S/xx.xx ojo con eso el sabado anterior vendiste mas S/xx.xx -->
                                <p>
                                Tu dia mas bajo fue el ".$day_b ." ". $num_day_b ." apenas llegaste a una venta de S/". $totalvendido_bajo['total']." te recomiendo hagas una estrategia de venta con los productos de menos rotacion en tu negocio asi aproveches a rotar mas tu almacen, e incremetes estos dias bajos
                                </p>

                                <p>
                                Tambien quiero mostratarte que ". $ventaTrabajadorTop['trabajador']."es quien mas ventas realizo en estos dias, seria bueno lo felicites en publico siempre es bueno que tus colaboradores se sientan reconocidos por el buen trabajo que ha hecho
                                </p>

                                <p>
                                Por cierto te dejo los siguientes cuadros para que veas la evolucion de tu negocio
                                </p>

                                <h2>
                                Ventas en estos dias
                                </h2>
                            
                                <center>
                                    <img src=\"cid:total_vendido.png\" />
                                </center>
                                <h2>
                                10 productos menos vendidos en estos 15 dias 
                                </h2>
                                <center>
                                <img src=\"cid:menos_vendido.png\" />
                                </center>
                                    
                                <h2>
                                10 productos mas vendidos en estos 15 dias 
                                </h2>
                                <center>
                                    <img src=\"cid:mas_vendido.png\" />
                                </center>
                                    
                                <h2>
                                Trabajadores segun ventas 
                                </h2>
                                <center>
                                    <img src=\"cid:trabajadores.png\" />
                                </center>
                                <p>
                                Espero que este corto analisis te sirva
                                ... y atento(a) que tendremos mas novedades en 15 dias 
                                </p>

                            
                                <p>
                                Recuerda que estamos para ayudarte cuando lo necesites
                                </p>
                            <center>
                                <h4>
                                Usqay Sistema de Negocios
                                </h4>
                                <h4>
                                 <a href=''>Www.sistemausqay.com</a>
                                </h4>
                                <h4>
                                    Central telefonica: (01) 642 9247
                                </h4>
                            </center>

                            </div>

                        </div>
                </body>";
            

                $mail->Body = $message;
                $mail->AltBody  = $message;
            

                if ($mail->Send()) {
                    echo "msg enviado a:";
                    echo $mailDestino;
                } else {
                    echo "error";
                }

            break;

    }
}









