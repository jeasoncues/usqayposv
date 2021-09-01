<?php

require_once 'nucleo/include/MasterConexion.php';
$objcon = new MasterConexion();

$id_venta = $_GET['id_venta'];

$config = $objcon->consulta_arreglo("SELECT * from configuracion");

$venta = $objcon->consulta_arreglo("SELECT * FROM venta where id = {$id_venta}");

$tipoComprobante= $venta['tipo_comprobante'];

if( intval($tipoComprobante)==0  ){

}

$entrega = $objcon->consulta_arreglo("SELECT * FROM entregas where id_venta = {$id_venta}");

$fecha = $venta["fecha_hora"];
$formatDate = date("d-m-Y", strtotime($fecha));
$total_pedido = 0;

$usuario = $objcon->consulta_arreglo("SELECT * FROM usuario where id = {$venta['id_usuario']}");
$cliente = $objcon->consulta_arreglo("SELECT * FROM cliente where id = {$venta['id_cliente']}");
$cliente_nota = $objcon->consulta_arreglo("SELECT * FROM nota_cliente where id_venta = " . $id_venta);

$boleta = $objcon->consulta_arreglo("SELECT id, serie FROM boleta WHERE id_venta = {$id_venta}");

$productos = $objcon->consulta_matriz("SELECT p.nombre, pv.*  from producto_venta pv left join producto p on pv.id_producto = p.id WHERE pv.estado_fila=1 AND pv.id_venta = $id_venta");


$servicios = $objcon->consulta_matriz("SELECT s.nombre, sv.* from servicio_venta sv left join servicio s on sv.id_servicio = s.id WHERE sv.id_venta = $id_venta");

$ventas = $objcon->consulta_arreglo("SELECT nombre FROM venta v, caja c WHERE v.id = $id_venta AND v.id_caja = c.id");
$medio_pago = $objcon->consulta_arreglo("SELECT * FROM venta_medio_pago WHERE id_venta = $id_venta");
$Descuento = $objcon->consulta_arreglo("SELECT * FROM venta_medio_pago WHERE id_venta = $id_venta AND medio = 'DESCUENTO'");
// echo json_encode($Descuento)

$hash = $objcon->consulta_arreglo("SELECT * FROM new_comprobante_hash WHERE estado=1 pkComprobante = $id_venta");

$items = $objcon->consulta_arreglo("SELECT pv.id_producto
                FROM producto_venta pv
                INNER JOIN producto p ON pv.id_producto = p.id
                WHERE pv.estado_fila=1 AND pv.id_venta = $id_venta
                UNION
                SELECT sv.id_servicio
                FROM servicio_venta sv
                INNER JOIN servicio s ON sv.id_servicio = s.id
                WHERE sv.id_venta = $id_venta
                ");

$incluye = $objcon->consulta_arreglo("SELECT incluye_impuesto
                FROM producto p
                WHERE p.id = " . $items[0] . "
                UNION
                SELECT incluye_impuesto
                FROM servicio s
                WHERE s.id = " . $items[0] . "");

if ($incluye[0] == 0) {
    $GEIG = "INAFECTA";
} else if ($incluye[0] == 1) {
    $GEIG = "GRAVADA";
} else if ($incluye[0] == 2) {
    $GEIG = "EXONERADA";
} else {
    $GEIG = "GRATUITA";
}

// echo json_encode($hash);
$Pro_total = 0;
$bolsa = 0;
$subTotal = 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Receipt example</title>
    <link rel="stylesheet" href="assets/css/print.css">
</head>

<body>
    <div class="ticket">
        <img src="assets/imagenes/negocios.png" alt="Logo">
        <p class="centered"><?php echo $config['nombre_negocio']; ?>
            <br><?php echo "RUC: " . $config['ruc']; ?>
            <br><?php echo $config['razon_social']; ?>
            <br><?php echo $config['direccion']; ?>
            <br><?php echo $config['telefono']; ?></p>

        <table>
            <thead>
                <tr>
                    <th>
                        <p>
                             - <?php echo str_pad($id_venta, 8, "0", STR_PAD_LEFT) ?>
                            <br>
                            Fecha de Emisión: <?php echo $fecha; ?>
                        </p>

                    </th>
                </tr>


            </thead>
        </table>
        <table style="margin-top: 5px;">
            <thead>
                <tr>
                    <th class="quantity">Cant.</th>
                    <th class="description">Descripción</th>
                    <th class="price">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (is_array($productos)) :
                    foreach ($productos as $p) :
                        $Pro_total += $p['total'];
                        $nombre = $p['nombre'];
                        if ($p['prod_secundario'] != 0) {
                            $prod_second = $objcon->consulta_arreglo("SELECT * FROM productos_precios WHERE id= " . $p['prod_secundario'] . "");
                            $nombre = $nombre . " - " . $prod_second["descripcion"];
                        }
                        $plastico = $objcon->consulta_arreglo("SELECT * FROM ley_plastico WHERE id_producto = '" . $p['id_producto'] . "'");

                        if (is_array($plastico)) {
                            $bolsa += $p["cantidad"] * $config["impuesto_bolsa"];
                        }

                ?>
                        <tr>
                            <td class="quantity"><?php echo $p['cantidad']; ?></td>
                            <td class="description"><?php echo strtoupper($nombre); ?></td>
                            <td class="price"><?php echo number_format($p['total'], 2, '.', ''); ?></td>
                        </tr>
                <?php
                    endforeach;
                endif; ?>

                <?php
                if (is_array($servicios)) :
                    foreach ($servicios as $s) :

                        $Pro_total += $s['total']; ?>

                        <tr>
                            <td class="quantity"><?php echo $s['cantidad']; ?></td>
                            <td class="description"><?php echo $s['nombre']; ?></td>
                            <td class="price"><?php echo number_format($s['total'], 2, '.', ''); ?></td>
                        </tr>
                <?php
                    endforeach;
                endif; ?>

                <?php
                if (isset($Descuento["medio"])) {
                    if ($Descuento["medio"]) {
                        $descuento = $Descuento["monto"] / 1.18;
                        $totalventa = $venta['total'] - $Descuento["monto"];
                        $total = ($venta['total'] / 1.18) - $descuento;
                        if ($GEIG == 'GRAVADA') {
                            $totaligv = $totalventa - ($totalventa / 1.18);
                        } else
                            $totaligv = 0.00;

                        $total2 = $venta['total'] - $Descuento["monto"];

                ?>

                        <tr>
                            <td class="quantity"></td>
                            <td class="description"><b>VUELTO</b></td>
                            <td class="price">S/<?php echo number_format($medio_pago['vuelto'], 2, '.', ''); ?></td>
                        </tr>
                        <tr>
                            <td class="quantity"></td>
                            <td class="description"><b>DESCUENTO</b></td>
                            <td class="price">S/<?php echo number_format($Descuento["monto"], 2, '.', ''); ?></td>
                        </tr>
                        <tr>
                            <td class="quantity"></td>
                            <td class="description"><b><?php echo $GEIG; ?></b></td>
                            <td class="price">S/<?php echo number_format($total, 2, '.', ''); ?></td>
                        </tr>
                        <tr>
                            <td class="quantity"></td>
                            <td class="description"><b>I.G.V</b></td>
                            <td class="price">S/<?php echo number_format($totaligv, 2, '.', ''); ?></td>
                        </tr>
                        <tr>
                            <td class="quantity"></td>
                            <td class="description"><b>TOTAL</b></td>
                            <td class="price">S/<?php echo number_format($totalventa, 2, '.', ''); ?></td>
                        </tr>
                    <?php
                    }
                } else {
                    // echo $Pro_total;

                    if ($GEIG == 'GRAVADA') {
                        $subTotal = $Pro_total / 1.18;
                        $igv = $Pro_total - $subTotal;
                    } else {
                        $igv = 0.00;
                        $subTotal = $Pro_total;
                    }
                    ?>

                    <tr>
                        <td class="quantity"></td>
                        <td class="description"><b>VUELTO</b></td>
                        <td class="price">S/<?php echo number_format($medio_pago['vuelto'], 2, '.', ''); ?></td>
                    </tr>
                    <tr>
                        <td class="quantity"></td>
                        <td class="description"><b><?php echo $GEIG; ?></b></td>
                        <td class="price">S/<?php echo number_format($subTotal, 2, '.', ''); ?></td>
                    </tr>
                    <?php if ($bolsa) : ?>
                        <tr>
                            <td class="quantity"></td>
                            <td class="description"><b>ICBPER</b></td>
                            <td class="price">S/<?php echo number_format($bolsa, 2, '.', ''); ?></td>
                        </tr>
                    <?php endif; ?>
                    <tr>
                        <td class="quantity"></td>
                        <td class="description"><b>I.G.V</b></td>
                        <td class="price">S/<?php echo number_format($igv, 2, '.', ''); ?></td>
                    </tr>
                    <tr>
                        <td class="quantity"></td>
                        <td class="description"><b>TOTAL</b></td>
                        <td class="price">S/<?php echo number_format($Pro_total, 2, '.', ''); ?></td>
                    </tr>



                <?php
                }
                ?>

            </tbody>
        </table>
        <p style="margin-top: 20px;" class="centered">Usted ha sido atendido por <?php echo $usuario['nombres_y_apellidos'] ?>
            <br>¡Gracias por su Preferencia!
        </p>
        <p style="margin-top: 20px;"><b>Representacion impresa de un Ticket el cual
                puede canjear por un comprobante electronico</b></p>
    </div>
    <!--<button id="btnPrint" class="hidden-print">Print</button>-->
    <script>
        window.onload = () => {
            /* var iframe = document.createElement('iframe');
            document.body.appendChild(iframe);
            iframe.style.display = 'none';
            iframe.onload = function() {
                setTimeout(function() {
                    iframe.focus();
                    iframe.contentWindow.print();
                }, 0);
            };
            
            iframe.src = _blobUrl; */
            
            window.print();            
            // var pr = window.matchMedia('print').addListener((evento)=>{console.log(evento);});
            /* $(".action-button").click(function(){
                console.log("ok");
            }); */

            /* $(".action-button").trigger('click') */
            

            //            window.print();
            window.onafterprint = function() {            
                location.href = "/pos/index.php";
            }


        }
    </script>
</body>

</html>