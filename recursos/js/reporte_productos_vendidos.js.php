<?php require_once('../../globales_sistema.php');?>
 $(document).ready(function() {
	  
	var ctx = document.getElementById('myChart').getContext('2d')
	var ctxx = document.getElementById('myChart2').getContext('2d')

	var newGraficoBarra = new Chart(ctx, {type: 'bar',data:null,options: null})
	var newGraficoDonut = new Chart(ctxx, {
		type: 'doughnut'}
	)

	// pruebaGraficosNew()

	const form_general=$('#form-ventas')
	cargar_grafico()

	
	function generarLetra(){
		var letras = ["a","b","c","d","e","f","0","1","2","3","4","5","6","7","8","9"];
		var numero = (Math.random()*15).toFixed(0);
		return letras[numero];
	}
	
	function colorHEX(){
		/* var coolor = "";
		var prefijo = "#";
		for (var i=0;i<6; i++) {
			coolor = coolor + generarLetra()
		}
		return prefijo+coolor */
		var letters = '0123456789ABCDEF';
		var color = '';
		for( var i=0; i<6; i++ ){
			color += letters[Math.floor(Math.random()*16)];
		}
		console.log('#E'+color+'');
		return '#E6'+color+'';
	}
	
	function cargar_grafico(){
		const data = form_general.serialize();
		const method = form_general.attr('method');
		const action = form_general.attr('action');		
		// removeData(newGraficoDonut)
		newGraficoDonut.destroy()
		newGraficoBarra.destroy()

		if ($.fn.dataTable.isDataTable('#tbl-productos'))
		{
			table = $('#tbl-productos').DataTable();
			table.destroy();
		}
		$("#tbl-productos > tbody").html("");
		
		$.post(action, data, function(response){
			// console.log(response);
			let tfoot = ``;
			let totalFinal = 0;
			let reporte1 = [];
			let reporte2 = [];

			// nuevos
			let labelreportedonut=[];
			let numberreportedonut=[];
			let color=[];
			let dataNewBarra=[];

			

			$.each(response, (i, val) => {

		
			let utilidad = Number(val['precio_venta']) - Number(val['precio_compra']);
			let utilidadTotal = Number(val['totalventa']) - ( Number(val['precio_compra']) * Number(val['cantidad']) );
			totalFinal += utilidadTotal;
			
			if(i < 5){
				// console.log("producto: "+ val['nombre']+ "| cntidad :"+  val['cantidad'])

				let colornew= colorHEX()
				console.log("colornew",colornew)
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
				// console.log("objeto",objeto)
				labelreportedonut.push(val['nombre'])
				// console.log("labelreportedonut",labelreportedonut)
				numberreportedonut.push( Number(val['cantidad']))
				color.push(colornew)

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

		console.log(lenguaje)
		$('#tbl-productos').DataTable({
			responsive:true,
			dom: "<'row'<'col'B>>lft<'row'<'col'i><'col'p>>",
			lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
       		order: [5, 'desc'],
			buttons: ['copyHtml5','excelHtml5','csvHtml5','pdfHtml5'
        ],
			
			language:lenguaje
		});
		

		/* var chart = new CanvasJS.Chart("chartContainer", {			
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
				innerRadius: 60,
				startAngle: 60,
				showInLegend: true,
				toolTipContent: "<b>{name}</b>: ${y} (#percent%)",
				indexLabel: "{name} - #percent%",
				dataPoints: reporte1
			}]
		});
		chart.render();	*/	

		var chartData = {
			labels:labelreportedonut,
			datasets:[{
				label:labelreportedonut,
				backgroundColor: color,
				boderColor: color,
				borderWidth: 2,
				hoverBackgroundColor: color,
				haverBorderColor: color,
				data: numberreportedonut,
				display: true
			}]
		}		

		newGraficoDonut = new Chart(ctxx, {
			type: 'doughnut',
			data: chartData,
			options:{
				responsive:true,
				tooltips: {
					mode: 'label',
					callbacks: {
						label: function(tooltipItem, data) {
							// console.log("data", data['datasets'][0]['data'][tooltipItem['index']] + '%')
							// console.log("label", data['datasets'][0]['label'][tooltipItem['index']] + '%')
							//  return data['datasets'][0]['data'][tooltipItem['index']] + '%';							
							var dataset = data.datasets[tooltipItem.datasetIndex];
							var total = dataset.data.reduce(function(previousValue, currentValue, currentIndex, array) {
								return parseFloat( parseFloat(previousValue) + parseFloat(currentValue));
							})
							var currentValue = dataset.data[tooltipItem.index];
							var percentage = parseFloat(((currentValue/total) * 100)).toFixed(2);   							
							return data['datasets'][0]['label'][tooltipItem['index']]+"  "+ data['datasets'][0]['data'][tooltipItem['index']] +" ( " +  percentage + "% )";
						}
					}
				},
				title: {
					display: true,
					text: 'Top 5 Productos Mas Vendidos'
				}
				
				/* scales:{
					yAxes:[{
						ticks:{
							beginAtZero:true
						}
					}]
				} */
			}			
		})

		/*var chart2 = new CanvasJS.Chart("chartContainer2", {
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
		chart2.render();*/
		// console.log("labelreportedonut",labelreportedonut)
		var chartData2 = {				
				labels:labelreportedonut,
				datasets: [
					{
					label:"Productos",
					backgroundColor: color,
					data: numberreportedonut
					}
				]				
			}
		

		newGraficoBarra= new Chart(ctx, {
			type: 'bar',
			data: chartData2,
			options:{
				responsive:true,
				title: {
					display: true,
					text: 'Top 5 Productos Mas Vendidos'
				}
				
		}})

		}, 'json');


	}
		
	$('#form-ventas').on('submit', function(e){
		e.preventDefault()
		cargar_grafico()
	})


	function addData(chart, label, data) {
		console.log("data",data)
		chart.data.labels.push(label);
		chart.data.datasets.forEach((dataset) => {
			console.log(data)
			dataset.data.push(data);
		});
		chart.update();
	}

	function removeData(chart) {
		chart.data.labels.pop();
		chart.data.datasets.forEach((dataset) => {
			dataset.data.pop();
		});
		chart.update();
	}
	




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