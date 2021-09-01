<?php 
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Reporte Ranking de Clientes';
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
$obj= new venta();

                
require_once('recursos/componentes/header.php');
?>

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
    if (isset($_GET['fecha_inicio'])){
        $fechaInicio = $_GET['fecha_inicio'];
        $fechaVar = $fechaInicio;
        }
    
    if (isset($_GET['fecha_fin']))
        $fechaFin = $_GET['fecha_fin'];

        
    
    // $objs = $conn->consulta_matriz("SELECT count(id) as noperaciones ,sum(total) as total_vendido,id_cliente FROM venta v where estado_fila = 1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59' group by id_cliente order by sum(total)desc");
   // $totalvendido = $conn->consulta_arreglo("SELECT sum(total)as total FROM venta where estado_fila = 1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59'");
    //$totalimpuestos = $conn->consulta_arreglo("SELECT sum(total_impuestos)as impuestos FROM venta where estado_fila = 1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59'");
   // $subtotal = $conn->consulta_arreglo("SELECT sum(subtotal)as subtotal FROM venta where estado_fila = 1 and fecha_cierre between '".$fechaInicio." 00:00:00' AND '".$fechaFin. " 23:59:59'");
    
    
   
   
//die();
// $sucursal = UserLogin::get_pkSucursal();
    ?>   

<!--    <div class="container">-->

<!--        <br /><br /><br />-->
<!--        <h3>Kardex Resumen</h3>-->        
        </form>

        <div class="row container">
            <p>
                <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                    <i class="fa fa-filter"></i> Filtros
                </button>
            </p>
            <div class="collapse col-md-12" id="collapseExample">
                <div class='col-md-4'>
                    <label>Fecha Inicio</label>
                    <input type="date" id="txtfechaini" type="text" class='form-control' placeholder='AAAA-MM-DD'  name='fecha_inicio' value="<?php echo $fechaInicio ?>"/>
                </div>

                <div class='col-md-4'>                    
                    <label>Fecha Fin</label>
                    <input  type=date id="txtfechafin" type="text" class='form-control' placeholder='AAAA-MM-DD' name='fecha_fin' value="<?php echo $fechaFin ?>"/>
                </div>

                <div class='col-md-4' style="margin-top:27px;">                                     
                    <button type="button" id="btnreporteservicios" class="btn btn-primary"><span class="glyphicon glyphicon-search"></span> Buscar</button>
                </div>
            </div>
        </div>

        <div class="row container" style="margin-top:30px">
            <div class="col-md-6 col-sm-6">
                <canvas id="myChart2" width="400" height="220"></canvas>

            </div>
            <div class="col-md-6 col-sm-6">
                <canvas id="myChart" width="400" height="220"></canvas>
            </div>
        </div>

        <div class='contenedor-tabla' style="margin-top:30px">
        <table id="tblKardex" title="Total de Ventas Por Trabajador" class="display dataTable no-footer" >
            <thead>
                <tr>
                    <th><center>Nombre de Usuario</center></th>
                    <th><center>Nro de Operaciones</center></th>
                    <th><center>Total Vendido</center></th>
                </tr>
            </thead>
        </table>

        <script>
        var lenguaje = {
            "sProcessing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ filas por pagina",
            "sZeroRecords": "No se encontraron resultados",
            "sEmptyTable": "Ningún dato disponible en esta tabla",
            "sInfo": "Mostrando del _START_ al _END_ de _TOTAL_ ",
            "sInfoEmpty": "Mostrando del 0 al 0 de 0 registros",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sSearchPlaceholder": "Dato a buscar",
            "sUrl": "",
            "sInfoThousands": ",",
            "sLoadingRecords": "Cargando...",
            "oPaginate": {
                "sFirst": "Primero",
                "sLast": "Último",
                "sNext": "Siguiente",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": Activar para ordenar la columna de manera ascendente",
                "sSortDescending": ": Activar para ordenar la columna de manera descendente"
            }
        };
    </script>
            
    <?php
            $nombre_tabla = 'reporte_ventaxcliente';
            require_once('recursos/componentes/footer.php');
            ?>
