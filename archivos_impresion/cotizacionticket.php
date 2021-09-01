<?php

?>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>COT</title>
</head>

<body>
<style>
    body {
        font-family: "Lucida Console", Monaco, monospace;
        font-size: 12px;
        zoom: 200%;
        font-weight: 200;
        width: 100%;
        margin: 0px;
    }

    .title {
        text-align: center;
        text-transform: uppercase;
        margin-bottom: 1px;
        margin-top: 1px;
    }

    hr {
        border: none;
        border-top: 1px solid black;
    }

    table {
        border-collapse: collapse;
        width: 90%;
        font-family: "Lucida Console", Monaco, monospace;
        font-size: 12px !important;
    }

    .productos td {
        text-align: center;
    }

    .productos .precio{
        text-align: right;
    }

    table th {
        text-align: center;
    }

    tr.resumen {
        text-align: right;
    }

    .resumen td {
        border: none;
    }

    .precio {
        text-align: right;
    }

    .msg{
        margin: 0 auto;
        width: 250px;
    } 

    .msg2{
        margin: 0 auto;
        width: 250px;
    }

    .msg p, .msg2 p{
        text-align:center;
        justify-content: center;
    }

    .totales{
        margin-top: 10px;
    }

    
</style>
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
$fecha = $cotizacion["fecha_hora"];

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
<h3 class="title"><?php echo $config['nombre_negocio']; ?></h3>
<p class="title"><?php echo "RUC: ".$config['ruc']; ?></p>
<p class="title"><?php echo $config['razon_social']; ?></p>
<p class="title"><?php echo $config['direccion']; ?></p>
<p class="title"><?php echo $config['telefono']; ?></p>
<hr>
<p class="title"><b>COTIZACION - <?php echo str_pad($id_coti, 8, "0", STR_PAD_LEFT) ?></b></p>
<hr>

<table align="center">
    <tr>
        <td>Fecha de Emision</td>
        <td>: <?php echo $fecha;?> </td>
    </tr>
    <tr>
        <td>Cliente</td>
        <td>: <?php echo strtoupper($cliente['nombre']);?> </td>
    </tr>
</table>

<hr>
<table align="center" class="productos">
    <tr>
        <th>Cant</th>
        <th>Producto</th>
        <!-- <th></th> -->
        <th class="precio">Total</th>
    </tr>
    <?php
    if (is_array($productos)):
        foreach ($productos as $p):            
            ?>
            <tr>
                <td><?php echo $p['cantidad']; ?></td>
                <td style="float:left"><?php echo strtoupper($p['nombre']); ?></td>
                <!-- <th width="1%">S/</th> -->
                <td width="5%" class="precio"> <?php echo number_format($p['precio']*$p['cantidad'], 2, '.', ''); ?></td>
            </tr>
            <?php
        endforeach;
    endif;?>    
</table>

<table align="center" class="totales">    
        <tr class="resumen">
            <td colspan="2" style="padding-right: 5%;"><b>SUBTOTAL</b></td>
            <td width="1%">S/</td>
            <td width="5%"><b><?php echo number_format($cotizacion['subtotal'], 2, '.', ''); ?></b></td>
        </tr>
        <tr class="resumen">
            <td colspan="2" style="padding-right: 5%;"><b>I.G.V</b></td>
            <td width="1%">S/</td>
            <td width="5%"><b><?php echo number_format($cotizacion['total_impuestos'], 2, '.', ''); ?></b></td>
        </tr>
        <tr class="resumen">
            <td colspan="2" style="padding-right: 5%;"><b>Total</b></td>
            <td width="1%">S/</td>
            <td width="5%"><b><?php echo number_format($cotizacion['total'], 2, '.', ''); ?></b></td>
        </tr>
    
</table>

<br>
<div class="msg">
    <p>Usted ha sido atendido por <?php echo $usuario['nombres_y_apellidos']?>  ¡Gracias por su Preferencia! </p>

    <p>Representacion impresa de la <br>Cotizacion</p>

</div>

</body>
</html>
