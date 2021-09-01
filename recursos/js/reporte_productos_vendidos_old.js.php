<?php require_once('../../globales_sistema.php');?>
 $(document).ready(function() {
	 	/*
		$('#tbl-productos').DataTable({
			responsive: true,
		});
        
		var tbl = $("#tblKardex").dataTable({
		    "dom": 'T<"clear">lfrtip',
		    "bInfo": false,
                    "bSort": false,
		    "oTableTools": {
		            "sSwfPath": "recursos/swf/copy_csv_xls_pdf.swf",
		            "aButtons": [
		            {
		                "sExtends": "xls"
		            },
		            {
		                "sExtends": "pdf"
		            }
		        ]
		    }
		});
		*/
		
	$('#form-ventas').on('submit', function(e){
		e.preventDefault();
		const data = $(this).serialize();
		const method = $(this).attr('method');
		const action = $(this).attr('action');

		if ($.fn.dataTable.isDataTable('#tbl-productos'))
		{
			table = $('#tbl-productos').DataTable();
			table.destroy();
		}
		$("#tbl-productos > tbody").html("");
		
		$.post(action, data, function(response){
			console.log(response);
			let tfoot = ``;
			let totalFinal = 0;
			let reporte1 = [];
			let reporte2 = [];

			$.each(response, (i, val) => {
		
			let utilidad = Number(val['precio_venta']) - Number(val['precio_compra']);
			let utilidadTotal = Number(val['totalventa']) - ( Number(val['precio_compra']) * Number(val['cantidad']) );
			totalFinal += utilidadTotal;
			
			if(i <= 5){
				const objeto = {
					y: Number(val['cantidad']),
					name: val['nombre']
				};

				const objeto2 = {
					y: Number(val['cantidad']),
					label: val['nombre']
				}

				reporte1.push(objeto);
				reporte2.push(objeto2);
			}
			

            let tbody = `<tr>`;
			tbody += `<td class="text-center">${val['id_producto']}</td>`;
            tbody += `<td class="text-center">${val['nombre']}</td>`;
            tbody += `<td class="text-center">${val['cantidad']}</td>`;
            tbody += `<td class="text-center">${val['precio_compra']}</td>`;
            tbody += `<td class="text-center">${val['precio_venta']}</td>`;
            tbody += `<td class="text-center">${utilidad.toFixed(2)}</td>`;
            tbody += `<td class="text-center">${Number(val['totalventa']).toFixed(2)}</td>`;
            tbody += `<td class="text-center">${utilidadTotal.toFixed(2)}</td>`;
            tbody += `</tr>`;
            $("#tbl-productos > tbody:last").append(tbody);
			
        });

		tfoot += `<tr>`;
        tfoot += `<th class="text-center" colspan="7">Utilidad Total</th>`;
        tfoot += `<th class="text-center">${totalFinal.toFixed(2)}</th>`;
        tfoot += `</tr>`;
		$('#tbl-productos > tfoot').html(tfoot);
		$('#tbl-productos').DataTable();
		console.log(reporte1);
		var chart = new CanvasJS.Chart("chartContainer", {
			theme: "dark2",
			exportFileName: "Doughnut Chart",
			exportEnabled: true,
			animationEnabled: true,
			title:{
				text: "Top 5 productos más vendidos"
			},
			legend:{
				cursor: "pointer",
				itemclick: explodePie
			},
			data: [{
				type: "doughnut",
				innerRadius: 90,
				showInLegend: true,
				toolTipContent: "<b>{name}</b>: ${y} (#percent%)",
				indexLabel: "{name} - #percent%",
				dataPoints: reporte1
			}]
		});
		chart.render();

		var chart2 = new CanvasJS.Chart("chartContainer2", {
			animationEnabled: true,
			theme: "light2", // "light1", "light2", "dark1", "dark2"
			title:{
				text: "Top Productos Más Vendidos"
			},
			axisY: {
				title: "Cantidad"
			},
			data: [{        
				type: "column",  
				showInLegend: true, 
				legendMarkerColor: "grey",
				legendText: "MMbbl = one million barrels",
				dataPoints: reporte2
			}]
		});
		chart2.render();
		}, 'json');
	});
	




		//tbl.fnSort( [ [0,'desc'] ] );
    });
    function buscar() {
        window.location.href = "reporte_productos_vendidos.php?fecha_inicio=" + $('#txtfechaini').val() + 
            "&fecha_fin="+ $('#txtfechafin').val();
    }
    
    
	function explodePie (e) {
	if(typeof (e.dataSeries.dataPoints[e.dataPointIndex].exploded) === "undefined" || !e.dataSeries.dataPoints[e.dataPointIndex].exploded) {
		e.dataSeries.dataPoints[e.dataPointIndex].exploded = true;
	} else {
		e.dataSeries.dataPoints[e.dataPointIndex].exploded = false;
	}
	e.chart.render();
}