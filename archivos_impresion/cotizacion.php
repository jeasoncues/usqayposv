<?php

?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>COT</title>
    <link rel="stylesheet" href="../recursos/adminLTE/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="impresion.css">
    <script type="text/javascript">
        window.print();
    </script>
</head>

<body>

<style type="text/css" media="print">
    @page {
        margin: 0;
    }
</style>
<?php
require_once '../nucleo/include/MasterConexion.php';
$objcon = new MasterConexion();


$id_coti = $_GET["id"];
$config = $objcon->consulta_arreglo("SELECT * from configuracion");

$cotizacion = $objcon->consulta_arreglo("SELECT * FROM cotizacion where id = {$id_coti}");

// $venta = $objcon->consulta_arreglo("SELECT * FROM venta where id = {$id_coti}");
// echo json_encode($venta);
$usuario = $objcon->consulta_arreglo("SELECT * FROM usuario where id = {$cotizacion['id_usuario']}");
$cliente = $objcon->consulta_arreglo("SELECT * FROM cliente where id = {$cotizacion['id_cliente']}");

$productos = $objcon->consulta_matriz("SELECT dt.id, p.nombre, p.unidad, dt.cantidad, dt.precio
                                        FROM detalles_cotizacion dt
                                        INNER JOIN producto p ON dt.id_producto = p.id
                                        WHERE dt.id_coti = {$id_coti}");

?>

<!-- <center><img src="../recursos/img/logo.png"  style="width: 50%; margin-bottom: -30px;"></center> <br> -->

<body>
    <div class="container">
        <div id="content">
            <div class="logo">
                <img src="../recursos/img/logo.jpeg" height="120px" width="250px">
            </div>

            <div class="numeracion">
                <h3><?php echo $config['nombre_negocio']?></h3>
                <h3><?php echo $config['ruc']?></h3>
                <h4>COTIZACION</h4>
                <h4><?php echo "#".str_pad($id_coti, 8, "0", STR_PAD_LEFT); ?></h4>
            </div>
        </div>

        <div id="content">
            <div class="empresa">
                <small><b><?php echo $config['razon_social']; ?></b></small><br>
                <small class="TxtEmpresa"><?php echo $config['direccion']; ?></small><br>
                <small class="TxtEmpresa"><?php echo $config['telefono']; ?></small>
            </div>
        </div>

        <div id="content">
            <div class="cliente">
                <table>
                    <?php
                        if(strlen($cliente['documento'])> 8){
                            $doc = 'RUC';
                            $cli = 'RAZON SOCIAL';
                        }else{
                            $doc = 'DNI';
                            $cli = 'CLIENTE';
                        }
                    ?>
                    <tr>
                        <td class="tdLabel"><?php echo $cli; ?> </td>
                        <td>: <?php echo strtoupper($cliente["nombre"]); ?></td>
                    </tr>
                    <tr>
                        <td class="tdLabel"><?php echo $doc; ?> </td>
                        <td>: <?php echo $cliente["documento"]; ?></td>
                    </tr>
                    <tr>
                        <td class="tdLabel">DIRECCION </td>
                        <td>: <?php echo strtoupper($cliente["direccion"]); ?></td>
                    </tr>
                </table>
            </div>
            <div class="fechas">
                <table>
                    <tr>
                        <td class="tdLabel">FECHA EMISION </td>
                        <td>: <?php echo date("d-m-Y",strtotime($cotizacion['fecha_hora'])); ?></td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="content">
            <table class="items">
                <thead>
                    <th>Item</th>
                    <th>UM</th>
                    <th>Cantidad</th>
                    <!-- <th>Codigo</th> -->
                    <th class="noBorderLeft">Producto</th>
                    <th>Precio</th>
                    <th>Total Item</th>
                </thead>
                <tbody>
                    <?php
                    $i=1;
                    if (is_array($productos)):
                        foreach ($productos as $p):
                            ?>
                            <tr>
                                <td><?php echo $i++; ?></td>
                                <td><?php echo $p['unidad']; ?></td>
                                <td><?php echo $p['cantidad']; ?></td>
                                <td ><?php echo strtoupper($p['nombre']); ?></td>
                                <td class="precio"  ><?php echo number_format($p['precio'], 2, '.', ''); ?></td>
                                <td width="10%" class="precio" > <?php echo number_format($p['precio']*$p['cantidad'], 2, '.', ''); ?></td>
                            </tr>
                            <?php
                        endforeach;
                    endif;?>
                </tbody>
            </table>
            <table align="right" class="totales">
                <!--<tr class="resumen">
                    <td  style="padding-right: 5%;"><b>Descuento</b></td>
                    <td width="10%"><b>0.00</b></td>
                </tr>-->
                <tr class="resumen">
                    <td  style="padding-right: 5%;"><b>Sub-Total</b></td>
                    <td width="10%"><b><?php echo number_format($cotizacion["subtotal"], 2, '.', ''); ?></b></td>
                </tr>
                <tr class="resumen">
                    <td  style="padding-right: 5%;"><b>I.G.V</b></td>
                    <td width="10%"><b><?php echo number_format($cotizacion["total_impuestos"], 2, '.', ''); ?></b></td>
                </tr>
                <tr class="resumen">
                    <td  style="padding-right: 5%;"><b>Total</b></td>
                    <td width="10%"><b><?php echo number_format($cotizacion["total"], 2, '.', ''); ?></b></td>
                </tr>
            </table>
        </div>
        <div class="content">
            <div class="msg">
                <p>Usted ha sido atendido por <?php echo $usuario['nombres_y_apellidos']?>  ¡Gracias por su Preferencia! </p>
        </div>
    </div>
</body>

</body>
</html>
