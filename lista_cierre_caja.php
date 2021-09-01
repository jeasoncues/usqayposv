<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}

$titulo_sistema = 'Katsu';

require_once 'nucleo/include/MasterConexion.php';
$objconn = new MasterConexion();
$titulo_pagina = "Cierre Caja";
require_once('recursos/componentes/header.php');

$movimientos_tabla = $objconn->consulta_matriz("SELECT 
    mv.fecha_cierre,
    (SELECT 
            ROUND(SUM(monto), 2)
        FROM
            movimiento_caja
        WHERE
            id_caja = 1
                AND tipo_movimiento LIKE '%OPEN%'
                AND fecha_cierre = mv.fecha_cierre) AS inicial_caja_1,
    (SELECT 
            ROUND(SUM(monto), 2)
        FROM
            movimiento_caja
        WHERE
            id_caja = 1
                AND tipo_movimiento LIKE '%SELL%'
                AND tipo_movimiento LIKE '%EFECTIVO%'
                AND fecha_cierre = mv.fecha_cierre) AS total_vendido_caja_1,
    (SELECT 
            ROUND(SUM(monto), 2)
        FROM
            movimiento_caja
        WHERE
            id_caja = 2
                AND tipo_movimiento LIKE '%OPEN%'
                AND fecha_cierre = mv.fecha_cierre) AS inicial_caja_2,
    (SELECT 
            ROUND(SUM(monto), 2)
        FROM
            movimiento_caja
        WHERE
            id_caja = 2
                AND tipo_movimiento LIKE '%SELL%'
                AND tipo_movimiento LIKE '%EFECTIVO%'
                AND fecha_cierre = mv.fecha_cierre) AS total_vendido_caja_2,
    (SELECT 
            ROUND(SUM(monto), 2)
        FROM
            movimiento_caja
        WHERE
            tipo_movimiento LIKE '%EFECTIVO%'
                AND tipo_movimiento LIKE '%SELL%'
                AND fecha_cierre = mv.fecha_cierre) AS total_efectivo,
    (SELECT 
            ROUND(SUM(monto), 2)
        FROM
            movimiento_caja
        WHERE
            tipo_movimiento LIKE '%VISA%'
                AND tipo_movimiento LIKE '%SELL%'
                AND fecha_cierre = mv.fecha_cierre) AS total_visa,
    (SELECT 
            ROUND(SUM(monto), 2)
        FROM
            movimiento_caja
        WHERE
            tipo_movimiento LIKE '%MASTERCARD%'
                AND tipo_movimiento LIKE '%SELL%'
                AND fecha_cierre = mv.fecha_cierre) AS total_mastercard,
    (SELECT 
            ROUND(SUM(monto), 2)
        FROM
            movimiento_caja
        WHERE
            tipo_movimiento LIKE '%INBX%'
                AND fecha_cierre = mv.fecha_cierre) AS ingresos_adicionales,

    (SELECT 
            ROUND(SUM(monto),2)
        FROM
            movimiento_caja
        WHERE
            tipo_movimiento LIKE '%LIQ%'
                AND fecha_cierre = mv.fecha_cierre) AS total_liquidaciones,
    (SELECT 
            ROUND(SUM(ABS(monto)), 2)
        FROM
            movimiento_caja
        WHERE
            (tipo_movimiento LIKE '%OUTBX%'
                OR tipo_movimiento LIKE '%BUY%')
                AND tipo_movimiento NOT LIKE '%PX%'
                AND fecha_cierre = mv.fecha_cierre) AS total_salidas,
    (SELECT 
            ROUND(SUM(monto), 2)
        FROM
            movimiento_caja
        WHERE
            tipo_movimiento LIKE '%SELL%'
                AND fecha_cierre = mv.fecha_cierre) AS total_vendido,
    (SELECT 
            ROUND(SUM(monto), 2) 
        FROM
            movimiento_caja
        WHERE
            tipo_movimiento LIKE '%EFECTIVO%'
                AND tipo_movimiento NOT LIKE '%OPEN%'
                AND tipo_movimiento NOT LIKE '%PX%'
                AND fecha_cierre = mv.fecha_cierre) AS total_general,
    (SELECT 
            ROUND(SUM(monto), 2)
        FROM
            movimiento_caja
        WHERE
            tipo_movimiento LIKE '%EFECTIVO%'
                AND tipo_movimiento NOT LIKE '%OPEN%'
                AND tipo_movimiento NOT LIKE '%PX%'
                AND tipo_movimiento NOT LIKE '%PX%'
                AND fecha_cierre = mv.fecha_cierre) AS utilidad
    FROM
        movimiento_caja mv
    GROUP BY mv.fecha_cierre");


?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel">
                <div class="panel-body">

                        <table id="tblmovimientos" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th rowspan="2">Fecha de cierre</th>
                                    <th colspan="2" class="text-center">Venta efectivo</th>
                                    <th rowspan="2" class="text-center">Total efectivo</th>
                                    <th rowspan="2" class="text-center">Total visa</th>
                                    <th rowspan="2" class="text-center">Total mastercard</th>
                                    <th rowspan="2" class="text-center">Total vendido</th>
                                    <th colspan="2" class="text-center">Ingresos y salidas</th>
                                    <th rowspan="2" class="text-center">Liquidaciones</th>
                                    <th rowspan="2" class="text-center">Saldo efectivo</th>
                                </tr>
                                <tr>
                                    <!--th>Inicial</th-->
                                    <th>Caja 1</th>
                                    <!--th>Inicial</th-->
                                    <th>Caja 2</th>
                                    <th>Ingresos</th>
                                    <th>Salidas</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php  foreach($movimientos_tabla as $mv):?>
                                    <?php $liq = $mv['total_liquidaciones'] != null ? $mv['total_liquidaciones'] : 0?>
                                    <tr>
                                        <td> <a href="cierre_caja.php?fecha=<?php echo $mv['fecha_cierre'] ?>"><?php echo $mv['fecha_cierre'] ?></a></td>
                                        <!--td><?php echo $mv['inicial_caja_1'] ?></td-->
                                        <td class="text-center"><?php echo $mv['total_vendido_caja_1'] ?></td>
                                        <!--td><?php echo $mv['inicial_caja_2'] ?></td-->
                                        <td class="text-center"><?php echo $mv['total_vendido_caja_2'] ?></td>
                                        <td class="text-center"><?php echo $mv['total_efectivo'] ?></td>
                                        <td class="text-center"><?php echo $mv['total_visa'] ?></td>
                                        <td class="text-center"><?php echo $mv['total_mastercard'] ?></td>
                                        <td class="text-center"><?php echo $mv['total_vendido'] ?></td>
                                        <td class="text-center"><?php echo $mv['ingresos_adicionales'] ?></td>
                                        <td class="text-center"><?php echo $mv['total_salidas'] ?></td>
                                        <td class="text-center"><?php echo $mv['total_liquidaciones'] ?></td>
                                        <td class="text-center"><?php echo $mv['total_general'] - $liq  ?></td>
                                    </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>


                </div>
            </div>
        </div>
    </div>
</div>

<!--<div class='control-group col-md-12' style='height: 40px; border-bottom: solid black 3px; margin-bottom: 10px;'>

</div>-->


</form>
<hr/>
<?php
include_once('nucleo/tipo_cambio.php');
$obj = new tipo_cambio();
$objs = $obj->listDB();
?>
<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <tbody>

<?php
$nombre_tabla = 'dummy';
require_once('recursos/componentes/footer.php');
?>
<script>
    $(document).ready(function() {

        $('#fecha').datepicker({dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });

        $('#tblmovimientos').DataTable({
            responsive: true,
            "order": [[ 0, "desc" ]],
            dom: 'Bfrtip',
            buttons: [
                'copyHtml5',
                'excelHtml5',
                'csvHtml5',
                'pdfHtml5'
            ],
            "language": {
                "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Spanish.json"
            }
        });
    });
    
</script>
