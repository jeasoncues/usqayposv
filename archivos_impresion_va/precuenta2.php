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
    <title>NOT</title>
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

$fecha = $venta["fecha_hora"];
$total_pedido = 0;

$usuario = $objcon->consulta_arreglo("SELECT * FROM usuario where id = {$venta['id_usuario']}");
$cliente = $objcon->consulta_arreglo("SELECT * FROM cliente where id = {$venta['id_cliente']}");
// echo json_encode($cliente);
$boleta = $objcon->consulta_arreglo("SELECT id, serie FROM boleta WHERE id_venta = {$id_venta}");

$productos = $objcon->consulta_matriz("SELECT p.nombre, pv.* from producto_venta pv left join producto p on pv.id_producto = p.id WHERE pv.estado_fila= 1 AND pv.id_venta = $id_venta");

$servicios = $objcon->consulta_matriz("SELECT s.nombre, sv.* from servicio_venta sv left join servicio s on sv.id_servicio = s.id WHERE sv.id_venta = $id_venta");

$ventas = $objcon->consulta_arreglo("SELECT nombre FROM venta v, caja c WHERE v.id = $id_venta AND v.id_caja = c.id");
$medio_pago = $objcon->consulta_arreglo("SELECT * FROM venta_medio_pago WHERE id_venta = $id_venta");
$Descuento = $objcon->consulta_arreglo("SELECT * FROM venta_medio_pago WHERE id_venta = $id_venta AND medio = 'DESCUENTO'");

$hash = $objcon->consulta_arreglo("SELECT * FROM comprobante_hash WHERE pkComprobante = $id_venta");

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

$incluye = $objcon->consulta_arreglo("SELECT incluye_impuesto
                FROM producto p 
                WHERE p.id = ".$items[0]."
                UNION
                SELECT incluye_impuesto
                FROM servicio s 
                WHERE s.id = ".$items[0]."");

if($incluye[0] == 0){
    $GEIG = "INAFECTA";
}else if($incluye[0] == 1){
    $GEIG = "GRAVADA";
}else if($incluye[0] == 2){
    $GEIG = "EXONERADA";
}else{
    $GEIG = "GRATUITA";
}

// echo json_encode($hash);

$Pro_total = 0;
?>

<h3 class="title"><?php echo $config['nombre_negocio']; ?></h3>
<p class="title"><?php echo "RUC: ".$config['ruc']; ?></p>
<p class="title"><?php echo $config['razon_social']; ?></p>
<p class="title"><?php echo $config['direccion']; ?></p>
<p class="title"><?php echo $config['telefono']; ?></p>
<hr>
<p class="title"><b>PRE CUENTA - <?php echo str_pad($id_venta, 8, "0", STR_PAD_LEFT) ?></b></p>
<!-- <p class="title"><b><?php echo $boleta['serie'] ."-". str_pad($boleta['id'], 8, "0", STR_PAD_LEFT); ?></b></p> -->
<!--<p class="title">serie impresora: --><?php //echo $config['serie_impresora']; ?><!--</p>-->
<!--<p class="title">Fecha:  ?></p>-->
<hr>

<table align="center">
    <tr>
        <td>Fecha de Emision</td>
        <td>: <?php echo $fecha;?> </td>
    </tr>
    <!-- <tr>
        <td>Moneda</td>
        <td>: 
            <?php 
                if($medio_pago != 0)
                    if($medio_pago['moneda'] == 'PEN')
                        echo 'Soles';
                    else
                        echo 'Dolares';
                else
                    echo 'Soles';
            ?>
        </td>
    </tr>
    <tr>
        <td>Cajero</td>
        <td>: <?php echo $ventas['nombre'] ?></td>
    </tr> -->
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
            $Pro_total += $p['total'];
            $nombre = $p['nombre'];
            if($p['prod_secundario'] != 0){
                $prod_second = $objcon->consulta_arreglo("SELECT * FROM productos_precios WHERE id= ".$p['prod_secundario']."");
                $nombre = $nombre." - ".$prod_second["descripcion"];
            }
            ?>
            <tr>
                <td><?php echo $p['cantidad']; ?></td>
                <td><?php echo strtoupper($nombre); ?></td>
                <!-- <th width="1%">S/</th> -->
                <td width="5%" class="precio"> <?php echo number_format($p['total'], 2, '.', ''); ?></td>
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
                <!-- <th width="1%">S/</th> -->
                <td width="5%" class="precio"><?php echo number_format($s['total'], 2, '.', ''); ?></td>
            </tr>
            <?php
        endforeach;
    endif;?>
</table>

<table align="center" class="totales">
    <?php
    if(!empty($Descuento["medio"])){

    
        if($Descuento["medio"]){
            $descuento = $Descuento["monto"] / 1.18;
            $totalventa = $venta['total'] - $Descuento["monto"];
            $total = ($venta['total']/1.18) - $descuento; 
            $totaligv = $totalventa - ($totalventa / 1.18);
            
    ?>
        <tr class="resumen">
            <td colspan="2" style="padding-right: 5%;"<b>Descuento</b></td>
            <td width="1%">S/</td>
            <td width="5%"><b><?php echo number_format($descuento, 2, '.', ''); ?></b></td>
        </tr>
        <tr class="resumen">
            <td colspan="2" style="padding-right: 5%;"><b><?php echo $GEIG; ?></b></td>
            <td width="1%">S/</td>
            <td width="5%"><b><?php echo number_format($subTotal, 2, '.', ''); ?></b></td>
        </tr>
        <tr class="resumen">
            <td colspan="2" style="padding-right: 5%;"><b>I.G.V</b></td>
            <td width="1%">S/</td>
            <td width="5%"><b><?php echo number_format($igv, 2, '.', ''); ?></b></td>
        </tr>
        <tr class="resumen">
            <td colspan="2" style="padding-right: 5%;"><b>Total</b></td>
            <td width="1%">S/</td>
            <td width="5%"><b><?php echo number_format($Pro_total, 2, '.', ''); ?></b></td>
        </tr>
    <?php
        }
    }else{
            // echo $Pro_total;

            $subTotal = $Pro_total/1.18;
            $igv = $Pro_total - $subTotal;
    ?>
        <tr class="resumen">
            <td colspan="2" style="padding-right: 5%;"><b><?php echo $GEIG; ?></b></td>
            <td width="1%">S/</td>
            <td width="5%"><b><?php echo number_format($subTotal, 2, '.', ''); ?></b></td>
        </tr>
        <tr class="resumen">
            <td colspan="2" style="padding-right: 5%;"><b>I.G.V</b></td>
            <td width="1%">S/</td>
            <td width="5%"><b><?php echo number_format($igv, 2, '.', ''); ?></b></td>
        </tr>
        <tr class="resumen">
            <td colspan="2" style="padding-right: 5%;"><b>Total</b></td>
            <td width="1%">S/</td>
            <td width="5%"><b><?php echo number_format($Pro_total, 2, '.', ''); ?></b></td>
        </tr>
    <?php
        }
    ?>
</table>

<?php if(!empty($cliente['id'])){ if($cliente['id'] != 1){ ?>
    <!-- <table align="center">
        <tr>
            <td>RUC</td>
            <td>: <?php echo $cliente['documento'];?> </td>
        </tr>
        <tr>
            <td>Razon Social</td>
            <td>: <?php echo $cliente['nombre'];?> </td>
        </tr>
        <tr>
            <td>Direccion</td>
            <td>: <?php echo $cliente['direccion'] ?></td>
        </tr>
    </table> -->
<?php }} ?>
<br>
<br>
<div class="msg">
    <p>Usted ha sido atendido por <?php echo $usuario['nombres_y_apellidos']?>  ¡Gracias por su Preferencia! </p>

    <p>Representacion impresa de la <br>Pre Cuenta</p>

</div>

<div class="msg2">
    <!-- <p>Para ver el documento visita <b><?php echo $config['pagina_web']; ?></b></p>

    <p><b>Autorizado por la SUNAT</b> mediante Resolucion de Intendencia No. <b>034-0050005315</b></p>
    <br> -->
    <p><b>USQAY</b>, es Facturacion Electronica visitanos en www.sistemausaqy.com o www.facebook.com/usqayperu</p>
</div>

<?php
    // $qr = "".$config['ruc']." | 03** | ".$boleta['serie']." | ".str_pad($boleta['id'], 8, "0", STR_PAD_LEFT)." | ".$venta['total_impuestos']." | ".$venta['total']." | ".date("d/m/Y")." | 1* | ".$venta['id_cliente']." |";
?>
               
<!-- <center>
    <img src="qrgen.php?data=<?php echo urlencode($qr); ?>" style="width:130px !important;"/>
</center>  -->

<!-- <center>
    <p>hash: <b><?php echo $hash["hash"]; ?></b></p>
</center> -->
</body>
</html>
