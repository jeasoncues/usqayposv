
<?php
require_once('globales_sistema.php');
if (!isset($_COOKIE['nombre_usuario'])) {
    header('Location: index.php');
}
$titulo_pagina = 'Cotizaciones';
$titulo_sistema = 'Katsu';
require_once('recursos/componentes/header.php');
include_once('nucleo/venta.php');

$objconn = new MasterConexion();

$obj= new venta();



if(isset($_GET["fechaInicio"])){
	$fechaI = $_GET["fechaInicio"];
	$fechaF = $_GET["fechaFin"];
	$objs = $objconn->consulta_matriz("SELECT co.id,cli.id as id_cliente, subtotal, total_impuestos, total, fecha_hora, nombres_y_apellidos, u.id as id_usuario, ca.id as id_caja, ca.nombre as caja, cli.nombre as cliente, direccion, co.estado_fila 
		FROM cotizacion co
		INNER JOIN cliente cli ON cli.id = co.id_cliente
		INNER JOIN caja ca ON ca.id = co.id_caja 
		INNER JOIN usuario u ON u.id = co.id_usuario
		WHERE fecha_hora BETWEEN '".$fechaI." 00:00:00' AND '".$fechaF." 23:59:59'");
}else{
	$fechaI = $fechaF = date('Y-m-d');
	$objs = $objconn->consulta_matriz("SELECT co.id,cli.id as id_cliente, subtotal, total_impuestos, total, fecha_hora, nombres_y_apellidos, u.id as id_usuario, ca.id as id_caja,ca.nombre as caja, cli.nombre as cliente, direccion, co.estado_fila 
		FROM cotizacion co
		INNER JOIN cliente cli ON cli.id = co.id_cliente
		INNER JOIN caja ca ON ca.id = co.id_caja 
		INNER JOIN usuario u ON u.id = co.id_usuario");
}

?>

<fieldset>
  <legend></legend>
    <div class='control-group col-md-5'>
        <label>Fecha Inicio</label>
        <input class='form-control' placeholder='AAAA-MM-DD' id='fecha' name='fecha' required value="<?php echo $fechaI;?>"/>
    </div>
    <div class='control-group col-md-5'>
        <label>Fecha Fin</label>
        <input class='form-control' placeholder='AAAA-MM-DD' id='fecha2' name='fecha2' required value="<?php echo $fechaF;?>"/>
    </div>
    <div class='control-group col-md-2'>
        <p></p>
        <br>
        <button type='button' class='btn btn-primary' onclick='buscar()'>Buscar</button>
    </div>
    <hr/>
    <hr/>
    <hr/>
</fieldset>
<fieldset>
  <legend></legend>
</fieldset>
</form>
<hr/>

<div class='contenedor-tabla'>
    <table id='tb' class='display' cellspacing='0' width='100%'>
        <thead>
            <tr>
                <th>Id</th>
                <th>Cliente</th>
                <th>Fecha</th>
                <th>Usuario</th>
                <th>Caja</th>
                <th>Sub Total</th>
                <th>IGV</th>
                <th>Total</th>
                 <th>Estado</th>
                <!-- <th>Guias</th>-->
                <th>OPC</th>
            </tr>
        </thead>
        <tbody id="block_">
        	<?php  
        		if(is_array($objs)):
        			foreach ($objs as $o):
        	?>
        		<tr>
					<td><?php echo $o["id"]; ?></td>
					<td><?php echo $o["cliente"]; ?></td>
					<td><?php echo $o["fecha_hora"]; ?></td>
					<td><?php echo $o["nombres_y_apellidos"]; ?></td>
					<td><?php echo $o["caja"]; ?></td>
					<td><?php echo number_format($o["subtotal"], 2, '.', ''); ?></td>
					<td><?php echo number_format($o["total_impuestos"], 2, '.', ''); ?></td>
					<td><?php echo number_format($o["total"], 2, '.', ''); ?></td>
                    <td><?php 
                    if ($o["estado_fila"] == 1) {
                       echo ("Cotizacion"); 
                    }elseif ($o["estado_fila"] == 2) {
                    echo ("Vendido"); 
                    }
                    ?></td>
					<td>
                        <!--<button title="imprimir" type='button' class="btn btn-sm btn-warning" onclick='imprimir(<?php echo $o["id"] ?>,<?php echo $o["id_caja"] ?>)'>
                        <i class="fa fa-print" aria-hidden="true"></i>
                        </button>-->
                        <button title="vender" type='button' class="btn btn-sm btn-success" onclick='get_venta(<?php echo $o["id_caja"] ?>,<?php echo $o["id_usuario"] ?>,<?php echo $o["id"] ?>,<?php echo $o["id_cliente"] ?>)'>
                        <i class="fa fa-cart-plus" aria-hidden="true"></i>
                        </button>
                        <button title="descargar" type='button' class="btn btn-sm btn-danger" onclick='descargar(<?php echo $o["id"] ?>,<?php echo $o["id_caja"] ?>)'>
                        <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                        </button>
                      

                    </td>
				</tr>
        	<?php 
        			endforeach;
        		endif;
        		$nombre_tabla = 'compra';
            	require_once('recursos/componentes/footer.php');
        	?>
        </tbody>
    </table>
</div>

<script type="text/javascript">

	 $(document).ready(function() {
        $('#fecha').datepicker({dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });  

        $('#fecha2').datepicker({dateFormat: 'yy-mm-dd',
            changeMonth: true,
            changeYear: true
        });   
    });

    function buscar(){
        var fecha = $("#fecha").val();
        var fecha2 = $("#fecha2").val();
        location.href = "cotizaciones.php?fechaInicio="+fecha+"&fechaFin="+fecha2;
    }

    function get_venta(caja,usuario,id,cliente) {

            $.post('ws/venta.php', {
                op: 'gen',
                id_usuario: usuario,
                id_caja: caja,
                id_cliente: cliente
            }, function(data_venta) {
                var id_coti = id;
                var id_venta = data_venta;
                var id_usuario = usuario;
               
                $.post('ws/producto_venta.php', {
                    op: 'get_detalles_cotizacion',
                    id: id_coti,
                }, function(data_coti) {
                        data_coti.forEach(function(value, number) {
                            var id_producto =value["id_producto"];
                            var precio =value["precio"];
                            var cantidad =value["cantidad"];
                            var total =(value["precio"]* value["cantidad"]);
                                 $.post('ws/producto_venta.php', {
                                        op: 'add',
                                        id_venta: id_venta,
                                        id_producto: id_producto,
                                        precio: precio,
                                        cantidad: cantidad,
                                        total: total
                                    }, function(data_pv) {
                                       
                                   });
                        });
                         $.post('ws/producto_venta.php', {
                                    op: 'mod_detalles_cotizacion',
                                    id: id,
                            }, function(data_mod) {
                                location.href="pantalla_teclado.php?id="+id_venta;         
                            });

                           
                }, 'json');

            }, 'json');
       
    }
    function imprimir(id,caja) {
       
            $.post('ws/venta.php', {
                op: 'imprimir',
                id: id,
                id_caja: caja,
                tipo: 'COT'
            }, function(data_imp) {
                console.log(data_imp);
            });
       
    }
    function descargar(id,caja) {
        location.href="archivos_impresion/cotizacion_descarga.php?id="+id; 
    }


</script>