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
        $query = "Select * from usuario where documento = '" . $_REQUEST['documento'] . "' and estado_fila = 1";
        $res = $conn->consulta_arreglo($query);
        if ($res !== 0) { //Existe el usuario, ahora se debe comprobar el password
            echo password_verify($res['password'], $passHash);
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
                if ($vencidos !== 0)
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
            } else {
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
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-capable" content="yes">
    <title>UsqayPOS</title>
    <!-- Tell the browser to be responsive to screen width -->

    <!-- Bootstrap 3.3.5 -->
    <link rel="stylesheet" href="recursos/adminLTE/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="recursos/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <link rel="shortcut icon" type="image/x-icon" href="usqay-icon.svg">

</head>

<body>
    <div class="row login-container-c">
        <div class="row">
            <div class="col-12 text-right" style="position: absolute; top: 0px; right: 5%;">
                <img src='recursos/img/logo-negativo.svg' class="logo-a">
            </div>
        </div>

        <div class="col-md-4" style="position: absolute; top: 0px; left: 5%;">
        <div class="login-form-c" style="margin-top: 15vh;">
            <form method="post">
                <div class="text-center">
                    <img src='recursos/img/usqay-circle-icon.svg' class="logo">
                </div>
                <?php
                include_once('nucleo/include/TestConexion.php');
                $test = new TestConexion();
                $res = $test->test();
                if ($res === 100) {
                }
                if ($res === -100) { // NO EXISTE
                    header('Location: migrate.php');
                }
                if ($res === 0) { // SE PRDUJO UN ERROR
                    echo ("SE PRODUJO UN ERROR EN LA CONEXION DEL TEST");
                }
                ?>

                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"><i class="fas fa-user"></i> </span>
                    <input type="text" class="form-control form-control-aux" placeholder="Ingrese su usuario." name='documento'>
                </div>
                <br>
                <br>
                <div class="input-group">
                    <span class="input-group-addon" id="basic-addon1"> <i class="fas fa-lock"></i> </span>
                    <input type="password" class="form-control form-control-aux" placeholder="Ingrese su contraseña" name='password'>
                </div>
                <!-- <div class="form-group">
                    <label class="form-label">Usuario </label>                
                    <input type="text" class="form-control form-control-aux" placeholder="Ingrese su usuario." name='documento'>
                </div> -->
                
               <!--  <div class="form-group">
                    <label class="form-label">Contraseña</label>
                    <input type="password" class="form-control form-control-aux" placeholder="Ingrese su contraseña" name='password'>
                </div> -->
                <br>
                <br>
                <?php if (isset($_GET["idc"])) : ?>
                    <div class="input-group">
                        <span class="input-group-addon" > <i class="fas fa-cash-register"></i> </span>
                        <!-- <label class="form-label">Caja</label> -->
                        <?php
                        include_once('nucleo/include/MasterConexion.php');
                        $conn0 = new MasterConexion();
                        $query = "Select * from caja where id = '" . $_GET["idc"] . "'";
                        $res = $conn0->consulta_matriz($query);
                        $n_caja = "";
                        if (is_array($res)) {
                            foreach ($res as $cj) {
                                $n_caja = $cj["nombre"];
                            }
                        }
                        ?>
                        <input type="text" class="form-control" name='ncaja' value='<?php echo $n_caja; ?>' readonly />
                    </div>
                    <input type="hidden" name="caja" value="<?php echo $_GET["idc"]; ?>" />
                <?php else : ?>
                    <div class="input-group">
                    <span class="input-group-addon" > <i class="fas fa-cash-register"></i> </span>
                        <select class="form-control select" name="caja">
                            <?php
                            include_once('nucleo/include/MasterConexion.php');
                            $conn0 = new MasterConexion();
                            $query = "Select * from caja where estado_fila = 1";
                            $res = $conn0->consulta_matriz($query);
                            if (is_array($res)) :
                                foreach ($res as $cj) :
                            ?>
                                    <option value="<?php echo $cj["id"]; ?>" <?php if (isset($_COOKIE["id_caja"])) {
                                                                                    if (intval($_COOKIE["id_caja"]) === intval($cj["id"])) {
                                                                                        echo "selected";
                                                                                    }
                                                                                } ?>><?php echo $cj["nombre"]; ?></option>
                            <?php
                                endforeach;
                            endif;
                            ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary btn-lg btn-submit-c">Acceder</button>
                </div>

            </form>
            </div >
        </div>
    </div>
    <div class="container login-container-mobile">
        <div class="row">
            <div class="col-xs-12">
                <div class="panel">
                    <form method="post">
                        <div class="text-center">
                            <img src='recursos/img/logo.svg' class="logo">
                        </div>
                        <div class="form-group">
                            <label class="form-label">Usuario</label>
                            <input type="text" class="form-control form-control-aux" placeholder="Ingrese su usuario." name='documento'>
                        </div>
                        <br>
                        <div class="form-group">
                            <label class="form-label">Contraseña</label>
                            <input type="password" class="form-control form-control-aux" placeholder="Ingrese su contraseña" name='password'>
                        </div>
                        <br>
                        <?php if (isset($_GET["idc"])) : ?>
                            <div class="form-group" style='margin-top: 2%;'>
                                <label class="form-label">Caja</label>
                                <?php
                                include_once('nucleo/include/MasterConexion.php');
                                $conn0 = new MasterConexion();
                                $query = "Select * from caja where id = '" . $_GET["idc"] . "'";
                                $res = $conn0->consulta_matriz($query);
                                $n_caja = "";
                                if (is_array($res)) {
                                    foreach ($res as $cj) {
                                        $n_caja = $cj["nombre"];
                                    }
                                }
                                ?>
                                <input type="text" class="form-control" name='ncaja' value='<?php echo $n_caja; ?>' readonly />
                            </div>
                            <input type="hidden" name="caja" value="<?php echo $_GET["idc"]; ?>" />
                        <?php else : ?>
                            <div class="form-group">
                                <label class="form-label">Caja</label>
                                <select class="form-control select" name="caja">
                                    <?php
                                    include_once('nucleo/include/MasterConexion.php');
                                    $conn0 = new MasterConexion();
                                    $query = "Select * from caja where estado_fila = 1";
                                    $res = $conn0->consulta_matriz($query);
                                    if (is_array($res)) :
                                        foreach ($res as $cj) :
                                    ?>
                                            <option value="<?php echo $cj["id"]; ?>" <?php if (isset($_COOKIE["id_caja"])) {
                                                                                            if (intval($_COOKIE["id_caja"]) === intval($cj["id"])) {
                                                                                                echo "selected";
                                                                                            }
                                                                                        } ?>><?php echo $cj["nombre"]; ?></option>
                                    <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                        <?php endif; ?>

                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary btn-lg">Acceder</button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 3.3.5 -->
    <script src="recursos/adminLTE/plugins/jQuery/jQuery-2.1.4.min.js"></script>
    <script src="recursos/adminLTE/bootstrap/js/bootstrap.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/5.4.1/bootbox.js"></script>


</body>

</html>