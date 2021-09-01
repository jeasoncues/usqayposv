<?php require_once('../../globales_sistema.php');?>

$(document).ready(function() {

	const form_general=$('#form-servicios')
	var ctx = document.getElementById('myChart').getContext('2d')
	var ctxx = document.getElementById('myChart2').getContext('2d')

	var newGraficoBarra = new Chart(ctx, {type: 'bar',data:null,options: null})
	var newGraficoDonut = new Chart(ctxx, {type: 'doughnut'})
	
	cargar_grafico()

	$("#btnreporteservicios").on('click',(e)=>{
		e.preventDefault()
		cargar_grafico()
		return false
	})
	
	function generarLetra(){
		var letras = ["a","b","c","d","e","f","0","1","2","3","4","5","6","7","8","9"];
		var numero = (Math.random()*15).toFixed(0);
		return letras[numero];
	}
	
	function colorHEX(){
		/*var coolor = "";
		var prefijo = "#E6";
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
	
	function cargar_grafico(){
		const fi= $("#txtfechaini").val()
		const ff= $("#txtfechafin").val()
		const op= "reporte";		
		newGraficoDonut.destroy()
		newGraficoBarra.destroy()

		if ($.fn.dataTable.isDataTable('#tbl-productos'))
		{
			table = $('#tbl-productos').DataTable();
			table.destroy();
		}

		$("#tbl-productos > tbody").html("");
		
		$.post("ws/servicio.php", {'op':op,'fecha_inicio':fi,'fecha_fin' :ff}, function(response){			
			let tfoot = ``;
			let totalFinal = 0;
			let reporte1 = [];
			let reporte2 = [];

			// nuevos
			let labelreportedonut=[];
			let numberreportedonut=[];
			let color=[];
			let dataNewBarra=[];
			
			if(response.length>0){
				$.each(response, (i, val) => {					
					totalFinal =  Number(totalFinal) + Number(val['totalventa'])
					
					if(i < 5){
						let colornew= colorHEX()												
						labelreportedonut.push(val['nombre'])						
						numberreportedonut.push( Number(val['cantidad']))
						color.push(colornew)

					}

					let tbody = `<tr>`;			
					tbody += `<td class="text-center">${val['nombre']}</td>`;
					tbody += `<td class="text-center">${val['cantidad']}</td>`;            
					tbody += `<td class="text-center">${val['precio_venta']}</td>`;            
					tbody += `<td class="text-center">${Number(val['totalventa']).toFixed(2)}</td>`;            
					tbody += `</tr>`;
					$("#tbl-productos > tbody:last").append(tbody);
					
				});
			}

			tfoot += `<tr>`;
			tfoot += `<th class="text-center" colspan="3">Totales</th>`;
			tfoot += `<th class="text-center">${totalFinal.toFixed(2)}</th>`;
			tfoot += `</tr>`;
			$('#tbl-productos > tfoot').html(tfoot);
			
			console.log(lenguaje)
		$('#tbl-productos').DataTable({
			responsive:true,
			dom: "<'row'<'col'B>>lft<'row'<'col'i><'col'p>>",
			lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
       		//order: [5, 'desc'],
			buttons: ['copyHtml5','excelHtml5','csvHtml5','pdfHtml5'
        ],
			
			language:lenguaje
		});

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
						text: 'Top 5 Servicios Mas Vendidos'
					}
				}			
			})
			
			var chartData2 = {
					labels:labelreportedonut,
					datasets: [
						{
						label: "Servicios",
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
						text: 'Top 5 Servicios Mas Vendidos'
					}
					
			}})

		}, 'json');

	}

});
