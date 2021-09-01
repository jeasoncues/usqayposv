<?php
$err = 0;
if (isset($_COOKIE['nombre_usuario'])) {
//    header('Location: dashboard_sistema.php');
    header('Location: pantalla_teclado.php');
} else {
    if (isset($_REQUEST['documento']) && isset($_REQUEST['password'])) {
        include_once('nucleo/include/MasterConexion.php');
        $conn = new MasterConexion();
        $passHash = sha1($_REQUEST['password']);
        // echo password_verify($_REQUEST['password'], $passHash);
        // $query = "Select * from usuario where documento = '".$_REQUEST['documento']."' AND password = '".$_REQUEST['password']."' and estado_fila = 1";
        $query = "Select * from usuario where documento = '".$_REQUEST['documento']."' and estado_fila = 1";
        $res = $conn->consulta_arreglo($query);
        echo password_verify($res['password'], $passHash);
        if ($res !== 0) {//Existe el usuario, ahora se debe comprobar el password
            if ($res['password'] == $passHash) {

                $sql = "SELECT id_guia_producto
                        FROM movimiento_producto mp
                        INNER JOIN guia_movimiento gm ON mp.id = gm.id_movimiento_producto
                        WHERE tipo_movimiento = 'ALMACEN' AND fecha_vencimiento <= ADDDATE(now(), interval 5  DAY) AND fecha_vencimiento <> '0000-00-00'
                        GROUP BY id_guia_producto";
                $vencidos = $conn->consulta_arreglo($sql);

                // echo "<script type='text/javascript'>alert('Estimados, para el uso del servicio por favor regulizar el pago de S/ 1000.00 Cuenta Corriente BCP 4752362226046 A nombre de corporacion leon lluen SRL');</script>";  

                include_once 'recursos/componentes/validador.php';

                setcookie("nombre_usuario", $res["nombres_y_apellidos"]);
                if($vencidos !== 0)
                    setcookie("vencidos", 1);
                else
                    setcookie("vencidos", 0);

                setcookie("id_usuario", $res["id"]);
                setcookie("tipo_usuario", $res["tipo_usuario"]);
                setcookie("id_caja", $_REQUEST["caja"]);
                if (!isset($_COOKIE["imprimir"])) {
                    setcookie("imprimir", 1, 2147483647, '/');
                    setcookie("descargar", 1, 2147483647, '/');
                } 
                header('Location: apertura_caja.php');
            }else{
                echo "<script type='text/javascript'>alert('La contrasena es incorrecta');</script>"; 
            }
        } else {
            echo "<script type='text/javascript'>alert('El documento es incorrecto');</script>"; 
            $err = 1;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>UsqayPOS</title>
    <!-- Tell the browser to be responsive to screen width -->

    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="recursos/adminLTE/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="recursos/css/bootstrap-overrides.css">
    <link rel="shortcut icon" type="image/x-icon" href="usqay-icon.svg">
    <style>
        html {
            height: 100%;
        }

        body {
            margin: 0px;
            height: 100%;
            background-image: url('recursos/img/fondo-katsu.png');
            background-size: cover;
        }

        @media (max-width: 1170px) {
            #jeanmarco {
                display: none;
            }
        }
    </style>
</head>

<body>
<div class="row" style='margin: 0px !important; height: 100%;'>
    <div class="col-md-5 col-sm-12 col-xs-12" style='height: 100%;
                background-color: rgba(21, 105, 255, 0.2);
                background: rgba(21, 105, 255, 0.2);
                color: rgba(21, 105, 255, 0.2);'>
        <div class="row" style='margin: 0px !important; height: 100%;'>
            <div class="col-md-1 col-xs-1 col-sm-1" style='height: 100%;'></div>
            <div class="col-md-10 col-xs-10 col-sm-10" style='height: 100%;'>
                <form method="post" style='
                      width: 90%; 
                      top: 15%;
                      left: 5%;
                      transform: translate(5%,20%);
                      padding: 5%;
                      background-color: #FFF;
                      text-align: center;'>
                    <img src='recursos/img/katsu-venta-logo.png' style='width:80%;margin-top: 5%;'>
                    <div class="input-group input-group-lg" style='margin-top: 12%;'>
                        <span class="input-group-addon" id="sizing-addon1"
                              style='background-color: #00395e;color:#FFF;'><span class="glyphicon glyphicon-user"
                                                                                  aria-hidden="true"></span></span>
                        <input type="text" class="form-control" placeholder="Ingrese Dni/ Documento Extrangeria"
                               name='documento' aria-describedby="sizing-addon1">
                    </div>

                    <div class="input-group input-group-lg" style='margin-top: 2%;'>
                        <span class="input-group-addon" id="sizing-addon2"
                              style='background-color: #00395e;color:#FFF;'><span class="glyphicon glyphicon-lock"
                                                                                  aria-hidden="true"></span></span>
                        <input type="password" class="form-control" placeholder="Password" name='password'
                               aria-describedby="sizing-addon2">
                    </div>
                    
                    <?php if(isset($_GET["idc"])): ?>                        
                    <div class="input-group input-group-lg" style='margin-top: 2%;'>
                        <span class="input-group-addon" id="sizing-addon3"
                              style='background-color: #00395e;color:#FFF;'><span
                                    class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></span>
                        <?php
                        include_once('nucleo/include/MasterConexion.php');
                        $conn0 = new MasterConexion();
                        $query = "Select * from caja where id = '".$_GET["idc"]."'";
                        $res = $conn0->consulta_matriz($query);
                        $n_caja = "";
                        if (is_array($res)){
                            foreach ($res as $cj){
                                $n_caja = $cj["nombre"];
                            }
                        }
                        ?>
                        <input type="text" class="form-control" name='ncaja' aria-describedby="sizing-addon3" value='<?php echo $n_caja;?>' readonly/>
                    </div>
                    <input type="hidden" name="caja" value="<?php echo $_GET["idc"];?>"/>                    
                    <?php else: ?>                    
                    <div class="input-group input-group-lg" style='margin-top: 2%;'>
                        <span class="input-group-addon" id="sizing-addon3"
                              style='background-color: #00395e;color:#FFF;'><span
                                    class="glyphicon glyphicon-chevron-down" aria-hidden="true"></span></span>
                        <select class="form-control" name="caja" aria-describedby="sizing-addon3">
                            <?php
                            include_once('nucleo/include/MasterConexion.php');
                            $conn0 = new MasterConexion();
                            $query = "Select * from caja where estado_fila = 1";                            
                            $res = $conn0->consulta_matriz($query);                           
                            if (is_array($res)):
                                foreach ($res as $cj):
                                    ?>
                                    <option value="<?php echo $cj["id"]; ?>" <?php if(isset($_COOKIE["id_caja"])){
                                        if(intval($_COOKIE["id_caja"]) === intval($cj["id"])){echo "selected";}
                                    }?>><?php echo $cj["nombre"]; ?></option>
                                    <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                    <?php endif;?>
                    <div class="form-group" style="text-align: right; margin-top: 2%;">
                        <button type="submit" class="btn btn-primary btn-lg">Entrar</button>
                    </div>

                    <div class="form-group"
                         style="text-align: center; margin-top: 10%; color: #00395e;font-weight: 700;">
                        <span>C3L Soluciones Tecnológicas</span>
                        <br/>
                        <span>www.sistemausqay.com</span>
                    </div>

                </form>
            </div>
            <div class="col-md-1 col-xs-1 col-sm-1" style='height: 100%;'></div>
        </div>
    </div>
    <div class="col-md-7 col-sm-12 col-xs-12" style='height: 100%;' id='jeanmarco'></div>
</div>

<!-- Bootstrap 3.3.5 -->
<script src="recursos/adminLTE/plugins/jQuery/jQuery-2.1.4.min.js"></script>
<script src="recursos/adminLTE/bootstrap/js/bootstrap.min.js"></script>

</body>
</html>

 