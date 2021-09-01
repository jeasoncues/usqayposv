<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Reporte Ranking por Trabajador';
$titulo_sistema = 'Katsu';
include_once('nucleo/include/MasterConexion.php');
include_once('nucleo/venta.php');
include_once('nucleo/producto_venta.php');
include_once('nucleo/servicio_venta.php');
include_once('nucleo/producto.php');
include_once('nucleo/usuario.php');
include_once('nucleo/servicio.php');
include_once('nucleo/caja.php');
include_once('nucleo/cliente.php');
include_once('nucleo/turno.php');

$conn = new MasterConexion();
$obj = new venta();


require_once('recursos/componentes/header.php');
?>
<style>
    .contenedor-tabla {
        margin-top: 20px;
    }

    .mt-25 {
        margin-top: 25px;
    }
</style>

<body>
    <style>
        .content-wrapper {
            background-color: #FFF !important;
        }
    </style>

    <?php

    $fechaInicio = date('Y-m-d');
    $fechaFin = date('Y-m-d');
    // $fechaCierre=$obj->fechaCierre();
    if (isset($_GET['fecha_inicio'])) {
        $fechaInicio = $_GET['fecha_inicio'];
        $fechaVar = $fechaInicio;
    }

    if (isset($_GET['fecha_fin']))
        $fechaFin = $_GET['fecha_fin'];


    $objs = $conn->consulta_matriz(
        "SELECT count(id) as noperaciones ,sum(total) as total_vendido,id_usuario FROM venta v where estado_fila = 1 
        and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' 
        group by id_usuario 
        order by sum(total)desc"
    );

    $desc = $conn->consulta_matriz(
        "SELECT sum(monto) as descuento, id_usuario 
        FROM venta v 
        INNER JOIN venta_medio_pago vm ON vm.id_venta = v.id 
        where v.estado_fila = 1 
        and fecha_cierre between '" . $fechaInicio . " 00:00:00' AND '" . $fechaFin . " 23:59:59' AND vm.medio = 'DESCUENTO'
        group by id_usuario"
    );


    $totalFinal = 0;
    $total_descuento = 0;
    $dataPoints2 = [];
    $labels=[];
    $dataaa=[];
    $dataaaa=[];
    if (is_array($objs)) {

        foreach ($objs as $key => $trabajador) {
            if (!empty($trabajador['total_vendido'])) {
                $totalFinal += $trabajador['total_vendido'];
            }
        }

        foreach ($objs as $key => $trabajador) {

            $objusuario = new usuario();
            $objusuario->setVar('id', $trabajador['id_usuario']);
            $objusuario->getDB();
            $monto = 0;
            $tv=0;
            if (!empty($trabajador['total_vendido'])) {
                $monto = $trabajador['total_vendido'] * 100 / $totalFinal;
                $tv= $trabajador['total_vendido'];
            }

            $dataPoints2[] = array(
                "label" => $objusuario->getNombresYApellidos(),
                "y" => number_format($monto, 2)
            );

            if (is_array($desc)) :
                foreach ($desc as $a) :
                    if ($objusuario->getId() == $a["id_usuario"]) :
                        $tv = $tv - $a["descuento"];                        
                    endif;
                endforeach;
            endif;

            array_push($labels,$objusuario->getNombresYApellidos());
            array_push($dataaa,number_format($monto, 2));
            array_push($dataaaa,number_format($tv, 2,'.',''));
        }
    }




    // $totalvendido = $conn->consulta_arreglo("SELECT sum(total)as total FROM venta where estado_fila = 1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59'");
    //$totalimpuestos = $conn->consulta_arreglo("SELECT sum(total_impuestos)as impuestos FROM venta where estado_fila = 1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59'");
    // $subtotal = $conn->consulta_arreglo("SELECT sum(subtotal)as subtotal FROM venta where estado_fila = 1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59'");

    //die();
    // $sucursal = UserLogin::get_pkSucursal();
    ?>
    <script>
        window.onload = function() {
            var color=['FF6961','#61FF69','#FF9E61','#8D87FF','#FFD6D4'];
            var ctx = document.getElementById('myChart').getContext('2d')
            var ctxx = document.getElementById('myChart2').getContext('2d')  
            console.log(  "TOTAL DESCEURTNO: " + <?php echo json_encode($total_descuento, JSON_NUMERIC_CHECK); ?>  )
            var newGraficoDonut = new Chart(ctx, {
                type: 'doughnut'}
            )
            var newGraficoBarra = new Chart(ctxx, {type: 'bar',data:null,options: null})
            newGraficoDonut.destroy()
		    newGraficoBarra.destroy()

            function generarLetra(){
                var letras = ["a","b","c","d","e","f","0","1","2","3","4","5","6","7","8","9"];
                var numero = (Math.random()*15).toFixed(0);
                return letras[numero];
            }
            
            function colorHEX(){
                /*var coolor = "";
                var prefijo = "#";
                for (var i=0;i<6; i++) {
                    coolor = coolor + generarLetra()
                }
                return prefijo+coolor*/
                var letters = '0123456789ABCDEF';
                var color = '';
                for( var i=0; i<6; i++ ){
                    color += letters[Math.floor(Math.random()*16)];
                }
                console.log('#E'+color+'');
                return '#E6'+color+'';
            }
            var color=[];
            /*var chart = new CanvasJS.Chart("chartContainer", {
                animationEnabled: true,
                title: {
                    text: "Ranking Por Trabajador"
                },
                subtitles: [{
                    text: "Porcentaje S/. ventas"
                }],
                data: [{
                    type: "pie",
                    yValueFormatString: "#,##0.00\"%\"",
                    indexLabel: "{label} ({y})",
                    dataPoints: <?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>
                }]
            });
            chart.render();*/
            var longitud=<?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>;

            if(longitud.length>0){
                for($i=0;$i<longitud.length;$i++){
                    color.push(colorHEX())
                }

                var chartData = {
                    labels:<?php echo json_encode($labels, JSON_NUMERIC_CHECK); ?>,
                    datasets:[{
                        label:<?php echo json_encode($labels, JSON_NUMERIC_CHECK); ?>,
                        backgroundColor: color,
                        boderColor: color,
                        borderWidth: 2,
                        hoverBackgroundColor: color,
                        haverBorderColor: color,
                        data:<?php echo json_encode($dataaa, JSON_NUMERIC_CHECK); ?>,
                        display: true
                    }]
                }		

                newGraficoDonut = new Chart(ctx, {
                    type: 'doughnut',
                    data: chartData,
                    options:{
                        responsive:true,
                        tooltips: {
                            mode: 'label',
                            callbacks: {
                                label: function(tooltipItem, data) {
                                    return data['datasets'][0]['label'][tooltipItem['index']]+"  "+ data['datasets'][0]['data'][tooltipItem['index']] +" %";
                                }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Top 5 Trabajadores que mas Venden'
                        }
                    }
                })

                var chartData2 = {				
                    labels:<?php echo json_encode($labels, JSON_NUMERIC_CHECK); ?>,
                    datasets: [
                        {
                        label: "Trabajadores",
                        backgroundColor: color,
                        data:<?php echo json_encode($dataaaa, JSON_NUMERIC_CHECK); ?>
                        }
                    ]
                }
            

                newGraficoBarra= new Chart(ctxx, {
                    type: 'bar',
                    data: chartData2,
                    options:{
                        responsive:true,
                        title: {
                            display: true,
                            text: 'Top 5 Trabajadores que mas Venden'
                        }
                }})

            }
            console.log(<?php echo json_encode($dataPoints2, JSON_NUMERIC_CHECK); ?>)
            console.log(longitud.length)
            console.log("--------")
            console.log(<?php echo json_encode($dataaa, JSON_NUMERIC_CHECK); ?>)
            console.log(<?php echo json_encode($dataaaa, JSON_NUMERIC_CHECK); ?>)
        }
    </script>

    <!--    <div class="container">-->

    <!--        <br /><br /><br />-->
    <!--        <h3>Kardex Resumen</h3>-->
    </form>
    <div class="container-fluid">
        <div class="row">
            <p>
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-filter"></i> Filtros
                </button>
            </p>
            <div class="collapse col-md-12" id="collapseExample">
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Fecha Inicio</label>
                        <input type="date" id="txtfechaini" class='form-control' placeholder='AAAA-MM-DD' name='fecha_inicio' value="<?php echo $fechaInicio ?>" />
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="form-group">
                        <label>Fecha Fin</label>
                        <input type=date id="txtfechafin" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>" />
                    </div>
                </div>
                <div class="col-md-2 mt-25">
                    <div class="form-group">
                        <button type="button" onclick="buscar()" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <canvas id="myChart" width="400" height="220"></canvas>

            </div>
            <div class="col-md-6">
                <canvas id="myChart2" width="400" height="220"></canvas>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body">
                        <div class='contenedor-tabla'>
                            <table id="tblKardex" title="Total de Ventas Por Trabajador" class="display dataTable no-footer">
                                <thead>
                                    <tr>
                                        <th>
                                            <center>Nombre de Usuario</center>
                                        </th>
                                        <th>
                                            <center>Nro de Operaciones</center>
                                        </th>
                                        <th>
                                            <center>Total Vendido</center>
                                        </th>
                                    </tr>
                                </thead>

                                <tbody>
                                    <?php
                                    if (is_array($objs)) :
                                        foreach ($objs as $o) :
                                            ?>
                                            <tr>

                                                <td style="text-align: center;"><?php
                                                                                        $objusuario = new usuario();
                                                                                        $objusuario->setVar('id', $o['id_usuario']);
                                                                                        $objusuario->getDB();
                                                                                        echo $objusuario->getNombresYApellidos();
                                                                                        ?>
                                                </td>
                                                <td style="text-align: center;"><?php echo ($o['noperaciones']); ?></td>
                                                <?php
                                                        $total = $o['total_vendido'];
                                                        if (is_array($desc)) :
                                                            foreach ($desc as $a) :
                                                                if ($o["id_usuario"] == $a["id_usuario"]) :
                                                                    $total = $total - $a["descuento"];
                                                                    $total_descuento += $a["descuento"];
                                                                endif;
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                <td style="text-align: center;"><?php echo number_format(floatval($total), 3); ?></td>
                                            </tr>

                                        <?php
                                            endforeach;
                                            ?>

                                    <?php endif;
                                    ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th class="text-center" colspan="2">Total</th>
                                        <th class="text-center"><?php echo number_format($totalFinal - $total_descuento, 2) ?></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

            </div>           
        </div>

    </div>


    <?php
    $nombre_tabla = 'reporte_ventasxtrabajador';
    require_once('recursos/componentes/footer.php');
    ?> 