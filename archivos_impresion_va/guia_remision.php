<?php
/**
 * Created by PhpStorm.
 * User: Eliu
 * Date: 04/10/2018
 * Time: 16:11
 */
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>REM</title>
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

    .check{
        width: 10px; 
        height: 10px; 
        border: 1px solid;
    }

    .msg{
        text-align:center;
        justify-content: center;
    } 

    .msg2{
        text-align:center;
        justify-content: center;
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


$id_venta = $_GET["id"];

$config = $objcon->consulta_arreglo("SELECT * from configuracion");

$venta = $objcon->consulta_arreglo("SELECT * FROM venta where id = {$id_venta}");

$total_pedido = 0;
$usuario = $objcon->consulta_arreglo("SELECT * FROM usuario where id = {$venta['id_usuario']}");
$cliente = $objcon->consulta_arreglo("SELECT * FROM cliente where id = {$venta['id_cliente']}");
$guia = $objcon->consulta_arreglo("SELECT * FROM guia_remision WHERE id_venta = {$id_venta}");
$fecha = $guia["fecha_emision"];
$productos = $objcon->consulta_matriz("SELECT p.nombre, pv.* from producto_venta pv left join producto p on pv.id_producto = p.id WHERE pv.estado_fila= 1 AND pv.id_venta = $id_venta");
$servicios = $objcon->consulta_matriz("SELECT s.nombre, sv.* from servicio_venta sv left join servicio s on sv.id_servicio = s.id WHERE sv.id_venta = $id_venta");

$qrBol = $objcon->consulta_arreglo("SELECT id FROM boleta WHERE id_venta = {$id_venta}");

if(is_array($qrBol)){
    $ncomprobante = $config['serie_boleta']."-".str_pad($qrBol['id'], 8, "0", STR_PAD_LEFT);
    $comprobante_tipo = "Boleta";
}else{
    $qrFact = $objcon->consulta_arreglo("SELECT id FROM factura WHERE id_venta = {$id_venta}");
    $ncomprobante = $config['serie_factura']."-".str_pad($qrFact['id'], 8, "0", STR_PAD_LEFT);
    $comprobante_tipo = "Factura";
}

$items = $objcon->consulta_arreglo("SELECT pv.id_producto
                FROM producto_venta pv
                INNER JOIN producto p ON pv.id_producto = p.id
                WHERE pv.estado_fila= 1 AND pv.id_venta = $id_venta
                UNION
                SELECT sv.id_servicio
                FROM servicio_venta sv
                INNER JOIN servicio s ON sv.id_servicio = s.id
                WHERE sv.id_venta = $id_venta
                ");

?>

<h3 class="title"><?php echo $config['nombre_negocio']; ?></h3>
<p class="title"><?php echo "RUC: ".$config['ruc']; ?></p>
<p class="title"><?php echo $config['razon_social']; ?></p>
<p class="title"><?php echo $config['direccion']; ?></p>
<p class="title"><?php echo $config['telefono']; ?></p>
<hr>
<p class="title"><b>GUIA DE REMISION REMITENTE</b></p>
<p class="title"><b><?php echo $config['serie_guia']."-".str_pad($guia["id"], 7, "0", STR_PAD_LEFT); ?></b></p> 
<hr>

<table align="center" >
    <tr>
        <td>Fecha de Emision</td>
        <td>: <?php echo $fecha;?> </td>
    </tr>
    <!-- <tr>
        <td>Fecha de Inicio de Traslado</td>
        <td>: <?php echo $fecha;?> </td>
    </tr> -->
    <tr>
        <td><?php echo $comprobante_tipo; ?></td>
        <td>: <?php echo $ncomprobante;?> </td>
    </tr>
    <tr>
        <td>Direccion de Partida</td>
        <td>: 
            <?php echo $config['direccion'];  ?>
        </td>
    </tr>

    <?php if($cliente['id'] != 0){ ?>
        <tr>
            <?php 
                if(strlen($cliente['documento'])> 8){
                    $doc = 'RUC';
                    $cli = 'RAZON SOCIAL';
                }else{
                    $doc = 'DNI';
                    $cli = 'CLIENTE';
                }
            ?>
            <td><?php echo $doc; ?></td>
            <td>: <?php echo $cliente['documento'];?> </td>
        </tr>
        <tr>
            <td><?php echo $cli; ?></td>
            <td>: <?php echo $cliente['nombre'];?> </td>
        </tr>
        <tr>
            <td>Direccion</td>
            <td>: <?php echo $cliente['direccion'] ?></td>
        </tr>
        <tr>
            <td>Correo</td>
            <td>: <?php echo $cliente['correo'] ?></td>
        </tr>
    <?php } ?>
</table>

<hr>
<table align="center" class="productos">
    <tr>
        <th>Cant</th>
        <th>Descripcion</th>
        <th></th>
    </tr>
    <?php
    if (is_array($productos)):
        foreach ($productos as $p):?>
            <tr>
                <td><?php echo $p['cantidad']; ?></td>
                <td><?php echo strtoupper($p['nombre']); ?></td>
                <!-- <th width="1%">S/</th>
                <td width="5%" class="precio"> <?php echo number_format($p['total'], 2, '.', ''); ?></td> -->
            </tr>
            <?php
        endforeach;
    endif;?>

    <?php
    if (is_array($servicios)):
        foreach ($servicios as $s):?>
            <tr>
                <td><?php echo $s['cantidad']; ?></td>
                <td><?php echo $s['nombre']; ?></td>
                <!-- <th width="1%">S/</th>
                <td width="5%" class="precio"><?php echo number_format($s['total'], 2, '.', ''); ?></td> -->
            </tr>
            <?php
        endforeach;
    endif;?>
</table>

<br><br><br>

<table align="center">
    <tr>
        <td>Motivo</td>
        <td class="precio"></td>
    </tr>
    <tr>
        <td>Venta</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Venta sujeta a confirmacion del Comprador</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Compra</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Consignacion</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Devolucion</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Traslado entre establecimiento de una misma empresa</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Traslado de vienes para trasnformacion</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Recojo de bienes transformados</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Traslado para emisor itinerante de Comprobante de pago</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Traslado de zona primaria</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Importacion</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Exportacion</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Venta con entrega a terceros</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    <tr>
        <td>Otros</td>
        <td class="precio"><div class="check"></div></td>
    </tr>
    
</table>

<br><br>
<div class="msg">
    <p>__________________</p>
    <small>Recibi Comforme</small>
</div>

<br><br>
</body>
</html>
