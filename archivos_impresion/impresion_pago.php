
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>PAG</title>
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
        text-align: left;
        padding-top: 5%;
        border: 1px solid;
    }

    .productos th {
        text-align: left;
        border: 1px solid;
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
        text-align: center;
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


$id_es = $_GET["id"];

$config = $objcon->consulta_arreglo("SELECT * from configuracion");

$pago = $objcon->consulta_arreglo("SELECT es.id, id_caja, tipo, es.monto, descripcion, fecha, id_turno, id_usuario FROM entrada_salida es, movimiento_caja mv WHERE es.id = ".$id_es." AND id_movimiento_caja = mv.id");

$caja = $objcon->consulta_arreglo("SELECT * FROM caja WHERE id = ".$pago['id_caja']);
$usuario = $objcon->consulta_arreglo("SELECT * FROM usuario where id = {$pago['id_usuario']}");

// echo json_encode($pago);

?>

<h3 class="title"><?php echo $config['nombre_negocio']; ?></h3>
<p class="title"><?php echo "RUC: ".$config['ruc']; ?></p>
<p class="title"><?php echo $config['razon_social']; ?></p>
<p class="title"><?php echo $config['direccion']; ?></p>
<p class="title"><?php echo $config['telefono']; ?></p>
<hr>
<p class="title"><b><!-- <?php echo $pago['tipo'] ?> --> PAGO - <?php echo str_pad($id_es, 8, "0", STR_PAD_LEFT) ?></b></p>
<hr>

<table align="center">
    <tr>
        <td>Fecha de Emision</td>
        <td>: <?php echo $pago['fecha'];?> </td>
    </tr>
    <tr>
        <td>Caja</td>
        <td>: <?php echo $caja['nombre'];?> </td>
    </tr>
    <tr>
        <td>Tipo De Pago </td>
        <td>: <?php echo $pago['tipo']; ?></td>
    </tr>
    <tr>
        <td>Usuario</td>
        <td>: <?php echo $usuario['nombres_y_apellidos'];?> </td>
    </tr> 

</table>

<hr>
<table align="center" class="productos">
    <tr>
        <th>Descripcion</th>
        <th class="precio">Total</th>
    </tr>
    <tr>
        <td><?php echo $pago['descripcion'] ?></td>
        <td class="precio"><?php echo number_format($pago['monto'],2) ?></td>
    </tr>
</table>


<br>
<br>
<br><br><br>
<br>
<br><br><br>
<br>
<br><br>

<div class="msg">
    <p>___________________</p>
    <p><b>Firma</b></p>
</div>
<!-- <div class="msg">
    <p>Usted ha sido atendido por <?php echo $usuario['nombres_y_apellidos']?>  ¡Gracias por su Preferencia! </p>
</div>

<div class="msg2">
    <p><b>USQAY</b>, es Facturacion Electronica visitanos en www.sistemausaqy.com o www.facebook.com/usqayperu</p>
</div> -->

<!-- </center>  -->
</body>
</html>
