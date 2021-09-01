<?php
	require_once('globales_sistema.php');
	if (!isset($_COOKIE['nombre_usuario'])) {
	    header('Location: index.php');
	}
	$titulo_pagina = 'Productos por Vencer';
	$titulo_sistema = 'Katsu';


	require_once('recursos/componentes/header.php');
	$conn = new MasterConexion();
	$sql = "SELECT p.nombre, mp.cantidad as stock ,a.nombre as almacen, fecha_vencimiento, lote, id_guia_producto
FROM movimiento_producto mp
INNER JOIN producto p ON mp.id_producto = p.id
INNER JOIN almacen a ON mp.id_almacen = a.id
INNER JOIN guia_movimiento gm ON mp.id = gm.id_movimiento_producto
WHERE mp.cantidad<> 0 AND tipo_movimiento = 'ALMACEN' AND fecha_vencimiento <= ADDDATE(now(), interval 30  DAY) AND fecha_vencimiento >= (now())";
    $vencidos = $conn->consulta_matriz($sql);
?>

<div class="content">
	<div class='contenedor-tabla'>
        <table id="tblKardex" title="Total de Ventas" class="display dataTable no-footer" >
            <thead>
                <tr>
                    <th><center>Producto</center></th>
                    <th><center>Almacen</center></th>
                    <th><center>Guia #</center></th>
                    <th><center>Fecha Vencimiento</center></th>
                    <th><center>Lote</center></th>
                </tr>
            </thead>
			<tbody>
				<?php 
					if (is_array($vencidos)): 
						foreach ($vencidos as $o):
					?>
					<tr>
						<td style="text-align: center;"><?php echo $o["nombre"] ?></td>
						<td style="text-align: center;"><?php echo $o["almacen"] ?></td>
						<td style="text-align: center;"><?php echo $o["id_guia_producto"] ?></td>
						<td style="text-align: center;"><?php echo $o["fecha_vencimiento"] ?></td>
						<td style="text-align: center;"><?php echo $o["lote"] ?></td>
					</tr>
				<?php endforeach; endif; 
				$nombre_tabla = 'dummy';
            	require_once('recursos/componentes/footer.php');
				?>
			</tbody>
		</table>


<script type="text/javascript">
	
	$(document).ready(function() {
		var tbl = $('#tblKardex').DataTable({
		    responsive: true,
		        "order": [[ 2, "ASC" ]],
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